<?php

declare(strict_types=1);


namespace Victormln\LaravelTactician\Services;


use Victormln\LaravelTactician\Exceptions\CommandHandlerNotExists;

final class GetCommandHandlerNameService
{

    public function execute($commandClass): array
    {
        $commandFullName = $this->getNameOfClass($commandClass);
        $commandHandlerFullName = $commandFullName . 'Handler';
        if (!class_exists($commandHandlerFullName)) {
            throw CommandHandlerNotExists::with($commandHandlerFullName);
        }

        return [
            $commandFullName,
            $commandHandlerFullName
        ];
    }

    private function getNameOfClass($commandClass): string
    {
        $reflectionCommand = new \ReflectionObject($commandClass);

        return $reflectionCommand->getNamespaceName() . '\\' . $reflectionCommand->getShortName();
    }
}
