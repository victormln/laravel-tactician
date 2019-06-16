<?php

namespace Victormln\LaravelTactician;

use ReflectionClass;
use InvalidArgumentException;
use League\Tactician\CommandBus;
use League\Tactician\Plugins\LockingMiddleware;
use League\Tactician\Handler\CommandHandlerMiddleware;
use Victormln\LaravelTactician\Exceptions\CommandHandlerNotExists;
use Victormln\LaravelTactician\Locator\LocatorInterface;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;

/**
 * The default Command bus Using Tactician, this is an implementation to dispatch commands to their handlers trough a middleware stack, every class is resolved from the laravel's service container.
 *
 * @package Victormln\LaravelTactician
 */
class Bus implements CommandBusInterface
{

    /** @var CommandBus */
    protected $bus;

    /** @var CommandNameExtractor */
    protected $commandNameExtractor;

    /** @var MethodNameInflector */
    protected $methodNameInflector;

    /** @var LocatorInterface */
    protected $handlerLocator;

    public function __construct(
        MethodNameInflector $MethodNameInflector,
        CommandNameExtractor $CommandNameExtractor,
        LocatorInterface $HandlerLocator
    ) {
        $this->methodNameInflector = $MethodNameInflector;
        $this->commandNameExtractor = $CommandNameExtractor;
        $this->handlerLocator = $HandlerLocator;
    }

    /**
     * Dispatch a command
     *
     * @param  object $command    Command to be dispatched
     * @param  array  $input      Array of input to map to the command
     * @param  array  $middleware Array of middleware class name to add to the stack, they are resolved from the laravel container
     * @throws CommandHandlerNotExists
     * @return mixed
     */
    public function dispatch($command, array $input = [], array $middleware = [])
    {
        $this->bindCommandWitHisCommandHandler($command);

        return $this->handleTheCommand($command, $input, $middleware);
    }

    private function bindCommandWitHisCommandHandler($command)
    {
        $commandFullName = $this->getNameOfClass($command);
        $commandHandlerFullName = $commandFullName . 'Handler';
        if(!class_exists($commandHandlerFullName)) {
            throw CommandHandlerNotExists::with($commandHandlerFullName);
        }

        $this->addHandler($commandFullName, $commandHandlerFullName);
    }

    /**
     * @param $command
     * @return string
     */
    private function getNameOfClass($command): string
    {
        if(\is_string($command)) {
            return $command;
        }

        $reflectionCommand = new \ReflectionObject($command);

        return $reflectionCommand->getNamespaceName() . '\\' . $reflectionCommand->getShortName();
    }

    /**
     * Add the Command Handler
     *
     * @param  string $command Class name of the command
     * @param  string $handler Class name of the handler to be resolved from the Laravel Container
     * @return mixed
     */
    public function addHandler($command, $handler)
    {
        $this->handlerLocator->addHandler($handler, $command);
    }

    /**
     * Handle the command
     *
     * @param  $command
     * @param  $input
     * @param  $middleware
     * @return mixed
     */
    protected function handleTheCommand($command, $input, array $middleware)
    {
        $this->bus = new CommandBus(
            array_merge(
                [new LockingMiddleware()],
                $this->resolveMiddleware($middleware),
                [new CommandHandlerMiddleware($this->commandNameExtractor, $this->handlerLocator, $this->methodNameInflector)]
            )
        );
        return $this->bus->handle($this->mapInputToCommand($command, $input));
    }

    /**
     * Resolve the middleware stack from the laravel container
     *
     * @param  $middleware
     * @return array
     */
    protected function resolveMiddleware(array $middleware)
    {
        $m = [];
        foreach ($middleware as $class) {
            $m[] = app($class);
        }

        return $m;
    }

    /**
     * Map the input to the command
     *
     * @param  $command
     * @param  $input
     * @return object
     */
    protected function mapInputToCommand($command, $input)
    {
        if (is_object($command)) {
            return $command;
        }
        $dependencies = [];
        $class = new ReflectionClass($command);
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if ( $parameter->getPosition() == 0 && $parameter->isArray() ) {
                if ($input !== []) {
                    $dependencies[] = $input;
                } else {
                    $dependencies[] = $this->getDefaultValueOrFail($parameter);
                }
            } else {
                $name = $parameter->getName();
                if (array_key_exists($name, $input)) {
                    $dependencies[] = $input[$name];
                } else {
                    $dependencies[] = $this->getDefaultValueOrFail($parameter);
                }
            }
        }

        return $class->newInstanceArgs($dependencies);
    }

    /**
     * Returns Default Value for parameter if it exists Or Fail
     *
     * @ReflectionParameter $parameter
     * @return mixed
     */
    private function getDefaultValueOrFail($parameter)
    {
        if (! $parameter->isDefaultValueAvailable()) {
            throw new InvalidArgumentException("Unable to map input to command: {$parameter->getName()}");
        }

        return $parameter->getDefaultValue();
    }

}
