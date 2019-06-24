<?php

namespace Victormln\LaravelTactician;

use League\Tactician\CommandBus;
use League\Tactician\Plugins\LockingMiddleware;
use League\Tactician\Handler\CommandHandlerMiddleware;
use Victormln\LaravelTactician\Exceptions\CommandHandlerNotExists;
use Victormln\LaravelTactician\Locator\LocatorInterface;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use Victormln\LaravelTactician\Services\GetCommandHandlerNameService;
use Victormln\LaravelTactician\Services\GetCommandHandlerService;

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
        MethodNameInflector $methodNameInflector,
        CommandNameExtractor $commandNameExtractor,
        LocatorInterface $handlerLocator
    ) {
        $this->methodNameInflector = $methodNameInflector;
        $this->commandNameExtractor = $commandNameExtractor;
        $this->handlerLocator = $handlerLocator;
    }

    /**
     * Dispatch a command
     *
     * @param  object $command    Command to be dispatched
     * @param  array  $middleware Array of middleware class name to add to the stack, they are resolved from the laravel container
     * @throws CommandHandlerNotExists
     * @return mixed
     */
    public function dispatch($command, array $middleware = [])
    {
        if(!$this->handlerLocator->handlers()
            || !$this->commandHasABindedCommandHandler($command)) {
            $this->bindCommandWitHisCommandHandler($command);
        }

        return $this->handleTheCommand($command, $middleware);
    }

    private function getCommandAndCommandHandlerNames($commandClass): array
    {
        return (new GetCommandHandlerNameService())->execute($commandClass);
    }

    private function commandHasABindedCommandHandler($command): bool
    {
        [$commandFullName, $commandHandlerFullName] = $this->getCommandAndCommandHandlerNames($command);
        $currentCommandAndHisHandlers = $this->handlerLocator->handlers();
        if(is_array($currentCommandAndHisHandlers)
            && !empty($currentCommandAndHisHandlers[$commandFullName])) {
            return true;
        }

        return false;
    }

    private function bindCommandWitHisCommandHandler($commandClass)
    {
        [$commandFullName, $commandHandlerFullName] = $this->getCommandAndCommandHandlerNames($commandClass);

        $this->addHandler($commandFullName, $commandHandlerFullName);
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
     * @param  $middleware
     * @return mixed
     */
    protected function handleTheCommand($command, array $middleware)
    {
        $this->bus = new CommandBus(
            array_merge(
                [new LockingMiddleware()],
                $this->resolveMiddleware($middleware),
                [new CommandHandlerMiddleware($this->commandNameExtractor, $this->handlerLocator, $this->methodNameInflector)]
            )
        );

        return $this->bus->handle($command);
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

}
