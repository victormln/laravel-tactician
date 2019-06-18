<?php

namespace Victormln\LaravelTactician\Tests\Locator;

use League\Tactician\Exception\MissingHandlerException;
use ReflectionException;
use Victormln\LaravelTactician\Tests\TestCase;

/**
 * Class TestLaravelLocator
 * @package Victormln\LaravelTactician\Tests\Locator
 */
class TestLaravelLocator extends TestCase{

    /**
     * It resolves the locator
     */
    public function test_it_resolves_the_laravel_locator(): void
    {
        $this->assertInstanceOf('Victormln\LaravelTactician\Locator\LocatorInterface',
            app('Victormln\LaravelTactician\Locator\LaravelLocator'));
    }

    /**
     * Throws exception if no handler for a command has been added
     */
    public function test_it_throws_exception_when_locator_from_laravel_container_is_not_found(): void
    {
        $this->expectException(MissingHandlerException::class);
        $locator = app('Victormln\LaravelTactician\Locator\LocatorInterface');
        $handler = $locator->getHandlerForCommand('TestCommand');
    }

    /**
     * Throws exception if laravel container can't resolve the handler class
     */
    public function test_it_throws_exception_when_locator_is_not_resolve_from_laravel_container(): void
    {
        $this->expectException(ReflectionException::class);
        $locator = app('Victormln\LaravelTactician\Locator\LocatorInterface');
        $locator->addHandler('SomeCommandHandler',
            'Victormln\LaravelTactician\Tests\Stubs\TestCommand');
    }

    /**
     * It is able to resolve the locator from the container
     */
    public function test_it_is_able_to_resolve_handler_from_laravel_container(): void
    {
        $locator = app('Victormln\LaravelTactician\Locator\LocatorInterface');
        $locator->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler',
            'Victormln\LaravelTactician\Tests\Stubs\TestCommand');
        $handler = $locator->getHandlerForCommand('Victormln\LaravelTactician\Tests\Stubs\TestCommand');
        $this->assertInstanceOf('Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler', $handler);
    }

    /**
     * Add more than one command => handler to the bus
     * @group failing
     */
    public function test_it_maps_array_commands(): void
    {
        $locator = app('Victormln\LaravelTactician\Locator\LocatorInterface');
        $locator->addHandlers([
            'Victormln\LaravelTactician\Tests\Stubs\TestCommand' => 'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler',
            'Victormln\LaravelTactician\Tests\Stubs\TestCommandInput' => 'Victormln\LaravelTactician\Tests\Stubs\TestCommandSecondHandler'
        ]);
        $handler = $locator->getHandlerForCommand('Victormln\LaravelTactician\Tests\Stubs\TestCommand');
        $handler2 = $locator->getHandlerForCommand('Victormln\LaravelTactician\Tests\Stubs\TestCommandInput');
        $this->assertInstanceOf('Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler', $handler);
        $this->assertInstanceOf('Victormln\LaravelTactician\Tests\Stubs\TestCommandSecondHandler', $handler2);
    }

}
