<?php

namespace Joselfonseca\LaravelTactician\Tests\Bus;

use Joselfonseca\LaravelTactician\Tests\TestCase;

/**
 * Class TestBus
 * @package Joselfonseca\LaravelTactician\Tests\Bus
 */
class TestBus extends TestCase{

    /**
     * Test if the class can handle a command
     */
    public function test_it_handles_a_command()
    {
        $bus = app('Joselfonseca\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand',
            'Joselfonseca\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $this->assertInstanceOf('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand', $bus->dispatch('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand', [], []));
    }

    /**
     * Throws exception if the middelware parameter is not an array
     * @expectedException Exception
     */
    public function test_it_throws_exception_if_middleware_not_an_array()
    {
        $bus = app('Joselfonseca\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand',
            'Joselfonseca\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $this->assertInstanceOf('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand', $bus->dispatch('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand', [], 'SomeClass'));
    }

    /**
     * Test if a a middleware can be applied to the stack
     */
    public function test_it_applies_a_middleware()
    {
        $bus = app('Joselfonseca\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand',
            'Joselfonseca\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $commandHandled = $bus->dispatch('Joselfonseca\LaravelTactician\Tests\Stubs\TestCommand', [], [
            'Joselfonseca\LaravelTactician\Tests\Stubs\Middleware'
        ]);
        $this->assertEquals('Handled', $commandHandled->addedPropertyInMiddleware);
    }

}