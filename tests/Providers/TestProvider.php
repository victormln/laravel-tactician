<?php

namespace Victormln\LaravelTactician\Tests\Providers;


use Victormln\LaravelTactician\Tests\TestCase;

/**
 * Class TestProvider
 * @package Victormln\LaravelTactician\Tests\Providers
 */
class TestProvider extends TestCase{

    /**
     * It loads the service Provider
     */
    public function test_it_loads_service_provider(): void
    {
        $this->assertInstanceOf('Victormln\LaravelTactician\Providers\LaravelTacticianServiceProvider',
            app()->getProvider('Victormln\LaravelTactician\Providers\LaravelTacticianServiceProvider'));
    }

    /**
     * It registers a locator
     */
    public function test_it_registers_locator(): void
    {
        $this->assertInstanceOf('Victormln\LaravelTactician\Locator\LocatorInterface',
            app('Victormln\LaravelTactician\Locator\LocatorInterface'));
    }

    public function test_it_registers_inflector(): void
    {
        $this->assertInstanceOf('League\Tactician\Handler\MethodNameInflector\MethodNameInflector',
            app('League\Tactician\Handler\MethodNameInflector\MethodNameInflector'));
    }

    /**
     * It registers the extractor
     */
    public function test_it_registers_extractor(): void
    {
        $this->assertInstanceOf('League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor',
            app('League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor'));
    }

}
