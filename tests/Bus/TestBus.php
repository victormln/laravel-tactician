<?php

namespace Victormln\LaravelTactician\Tests\Bus;

use Victormln\LaravelTactician\Tests\TestCase;

/**
 * Class TestBus
 * @package Victormln\LaravelTactician\Tests\Bus
 */
class TestBus extends TestCase{

    /**
     * Test if the class can handle a command
     */
    public function test_it_handles_a_command(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
                         'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $this->assertInstanceOf('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
                 $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommand', [], []));
    }


    public function test_it_accepts_prebuilt_command_objects(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
                         'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $this->assertInstanceOf('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
                 $bus->dispatch(app('Victormln\LaravelTactician\Tests\Stubs\TestCommand'), [], []));
    }

    /**
     * Test if a a middleware can be applied to the stack
     */
    public function test_it_applies_a_middleware(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
                         'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $commandHandled = $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommand', [], [
            'Victormln\LaravelTactician\Tests\Stubs\Middleware'
        ]);
        $this->assertEquals('Handled', $commandHandled->addedPropertyInMiddleware);
    }

    /**
     * Test if the bus is able to map a property in the command
     */
    public function test_it_maps_input_to_command(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommand',
                         'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $commandHandled = $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommand', [
            'property' => 'John Doe'
        ], [
            'Victormln\LaravelTactician\Tests\Stubs\Middleware'
        ]);
        $this->assertEquals('John Doe', $commandHandled->property);
    }

    /**
     * Test the InvalidArgumentException
     * @expectedException InvalidArgumentException
     */
    public function test_it_trows_exception_if_input_can_not_be_mapped_to_the_command(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommandInput',
                         'Victormln\LaravelTactician\Tests\Stubs\TestCommandHandler');
        $commandHandled = $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommandInput', [

        ], [
            'Victormln\LaravelTactician\Tests\Stubs\Middleware'
        ]);
    }

    /**
     * Test if the bus is able to map a array in the command
     */
    public function test_it_maps_array_to_command(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommandArray',
                         'Victormln\LaravelTactician\Tests\Stubs\TestCommandArrayHandler');

        /* Make Command with a set of properties */
        $contentA = $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommandArray', [
            'property' => 'John',
            'propertyTwo' => 'Doe'
        ], []);

        $this->assertArrayHasKey('property', $contentA);
        $this->assertArrayHasKey('propertyTwo', $contentA);
        $this->assertSame([
            'property' => 'John',
            'propertyTwo' => 'Doe'
        ], $contentA);

        /* Make same Command with a different set of properties */
        $contentB = $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommandArray', [
            'propertyThree' => 'Richard',
            'propertyFour' => 'Roe',
            'propertyFive' => 'Jr.',
        ], []);

        $this->assertArrayHasKey('propertyThree', $contentB);
        $this->assertArrayHasKey('propertyFour', $contentB);
        $this->assertArrayHasKey('propertyFive', $contentB);
        $this->assertSame([
            'propertyThree' => 'Richard',
            'propertyFour' => 'Roe',
            'propertyFive' => 'Jr.',
        ], $contentB);
    }

    /**
     * Test if the bus is able to map an null array in the command to default value in __construct
     */
    public function test_it_maps_null_array_in_command_to_default_value_in_construct(): void
    {
        $bus = app('Victormln\LaravelTactician\CommandBusInterface');
        $bus->addHandler('Victormln\LaravelTactician\Tests\Stubs\TestCommandArray',
            'Victormln\LaravelTactician\Tests\Stubs\TestCommandArrayHandler');

        /* Make Command with an empty set of properties */
        $content = $bus->dispatch('Victormln\LaravelTactician\Tests\Stubs\TestCommandArray', [], []);

        $this->assertArrayHasKey('DefaultPropertyOne', $content);
        $this->assertArrayHasKey('DefaultPropertyTwo', $content);
        $this->assertSame([
            'DefaultPropertyOne' => 'John',
            'DefaultPropertyTwo' => 'Doe'
        ], $content);
    }
}
