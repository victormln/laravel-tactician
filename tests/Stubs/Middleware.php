<?php

namespace Victormln\LaravelTactician\Tests\Stubs;

use League\Tactician\Middleware as TacticianMiddleware;

class Middleware implements TacticianMiddleware{

    public function execute($command, callable $next)
    {
        $command->addedPropertyInMiddleware = true;
        return $next($command);
    }

}
