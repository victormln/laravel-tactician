<?php

namespace Victormln\LaravelTactician\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Create a new Tactician Command Handler
 * @package Victormln\LaravelTactician\Commands
 */
class MakeTacticianHandlerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:tactician:handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Tactician Command Handler';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/commandHandler.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return "$rootNamespace\\CommandBus\\Handlers";
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return parent::getPath($name.'Handler');
    }
}
