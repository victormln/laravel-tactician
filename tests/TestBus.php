<?php

namespace Victormln\LaravelTactician\Tests;

use Victormln\LaravelTactician\Exceptions\CommandHandlerNotExists;
use Victormln\LaravelTactician\Tests\Stubs\TestCommandInput;
use Victormln\LaravelTactician\Tests\TestCase;
use Victormln\LaravelTactician\Tests\Stubs\TestCommand;

/**
 * Class TestBus
 * @package Victormln\LaravelTactician\Tests\Bus
 */
class TestBus extends TestCase
{

    /**
     * Test if the class can handle a command
     */
    public function test_it_handles_a_command(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $this->assertInstanceOf(
            TestCommand::class,
            $bus->dispatch(new TestCommand())
        );
    }

    public function test_it_accepts_prebuilt_command_objects(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $this->assertInstanceOf(
            TestCommand::class,
            $bus->dispatch(app('Victormln\LaravelTactician\Tests\Stubs\TestCommand'))
        );
    }

    /**
     * Test if a a middleware can be applied to the stack
     */
    public function test_it_applies_a_middleware(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $commandHandled = $bus->dispatch(
            new TestCommand(),
            ['Victormln\LaravelTactician\Tests\Stubs\Middleware']
        );
        $this->assertTrue($commandHandled->addedPropertyInMiddleware);
    }

    /**
     * Test the CommandHandlerNotExists
     */
    public function test_it_throws_exception_if_input_can_not_be_mapped_to_the_command(): void
    {
        $this->expectException(CommandHandlerNotExists::class);
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $commandHandler = $bus->dispatch(new TestCommandInput('HELLO'));
    }

    /**
     * Test if can add a handler manually and can be handled
     */
    public function test_it_handles_a_command_adding_manually_the_handler(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
            'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $this->assertInstanceOf(
            TestCommand::class,
            $bus->dispatch(new TestCommand())
        );
    }
}
