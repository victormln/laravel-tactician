<?php

declare(strict_types=1);

namespace Victormln\LaravelTactician\Services;

use League\Tactician\Exception\MissingHandlerException;

final class GetCommandHandlerNameService
{
    private const HANDLER_SUFFIX = 'Handler';

    /**
     * @param string $fullCommandName
     * @return string
     * @throws MissingHandlerException
     */
    public function execute(string $fullCommandName): string
    {
        $commandHandlerName = $fullCommandName . self::HANDLER_SUFFIX;
        $this->validateIfCommandHandlerExistsOrFail($commandHandlerName);

        return $commandHandlerName;
    }

    /**
     * @param string $fullCommandHandlerName
     * @return bool
     * @throws MissingHandlerException
     */
    private function validateIfCommandHandlerExistsOrFail(string $fullCommandHandlerName): bool
    {
        if (!class_exists($fullCommandHandlerName)) {
            throw MissingHandlerException::forCommand($fullCommandHandlerName);
        }

        return true;
    }
}
