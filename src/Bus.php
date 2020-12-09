<?php

namespace Victormln\LaravelTactician;

use RuntimeException;
use League\Tactician\CommandBus;
use League\Tactician\Plugins\LockingMiddleware;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Exception\MissingHandlerException;
use Victormln\LaravelTactician\Locator\LocatorInterface;
use Victormln\LaravelTactician\Services\GetCommandHandlerNameService;
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
     * @throws MissingHandlerException|RuntimeException
     * @return mixed
     */
    public function dispatch(object $command, array $middleware = [])
    {
        $fullCommandName = $this->getFullCommandNameOrFail($command);
        if(!$this->handlerLocator->handlers() || !$this->commandHasABoundedCommandHandler($fullCommandName)) {
            $this->bindCommandWitHisCommandHandler($fullCommandName, $this->getCommandHandlerName($fullCommandName));
        }

        return $this->handleTheCommand($command, $middleware);
    }

    /**
     * @param object $commandClass
     * @return string
     * @throws RuntimeException
     */
    private function getFullCommandNameOrFail(object $commandClass): string
    {
        $fullCommandName = get_class($commandClass);
        if (!$fullCommandName) {
            throw new RuntimeException('Invalid Command ' . $commandClass. ' given');
        }

        return $fullCommandName;
    }

    /**
     * @param string $fullCommandName
     * @return string
     * @throws MissingHandlerException
     */
    protected function getCommandHandlerName(string $fullCommandName): string
    {
        return (new GetCommandHandlerNameService())->execute($fullCommandName);
    }

    private function commandHasABoundedCommandHandler(string $fullCommandName): bool
    {
        $currentCommandAndHisHandlers = $this->handlerLocator->handlers();

        return is_array($currentCommandAndHisHandlers)
            && !empty($currentCommandAndHisHandlers[$fullCommandName]);
    }

    private function bindCommandWitHisCommandHandler(string $fullCommandName, string $fullCommandHandlerName): void
    {
        $this->addHandler($fullCommandName, $fullCommandHandlerName);
    }

    public function addHandler(string $command, string $handler): void
    {
        $this->handlerLocator->addHandler($handler, $command);
    }

    protected function handleTheCommand(object $command, array $middleware)
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

    protected function resolveMiddleware(array $middleware): array
    {
        $m = [];
        foreach ($middleware as $class) {
            $m[] = app($class);
        }

        return $m;
    }

}
