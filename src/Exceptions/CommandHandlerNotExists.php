<?php

namespace Victormln\LaravelTactician\Exceptions;


use Exception;
use Throwable;

final class CommandHandlerNotExists extends Exception
{

    private function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function with(string $commandHandlerFullPath): self
    {
        return new self(\sprintf('CommandHandler not found in %s', $commandHandlerFullPath));
    }

}
