<?php

namespace Victormln\LaravelTactician\Providers;

use Illuminate\Support\ServiceProvider;
use Victormln\LaravelTactician\Commands\MakeTacticianCommand;
use Victormln\LaravelTactician\Commands\MakeTacticianCommandCommand;
use Victormln\LaravelTactician\Commands\MakeTacticianHandlerCommand;

/**
 * Class LaravelTacticianServiceProvider
 *
 * @package Victormln\LaravelTactician\Providers
 */
class LaravelTacticianServiceProvider extends ServiceProvider
{

    /**
     * Do the bindings so any implementation can be swapped
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->app->bind('Victormln\LaravelTactician\Locator\LocatorInterface', config('laravel-tactician.locator'));
        $this->app->bind('League\Tactician\Handler\MethodNameInflector\MethodNameInflector', config('laravel-tactician.inflector'));
        $this->app->bind('League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor', config('laravel-tactician.extractor'));
        $this->app->bind('Victormln\LaravelTactician\CommandBusInterface', config('laravel-tactician.bus'));

        // Register Command Generator
        $this->app->singleton(
            'laravel-tactician.make.command', function ($app) {
                return new MakeTacticianCommandCommand($app['files']);
            }
        );
        $this->commands('laravel-tactician.make.command');

        // Register Handler Generator
        $this->app->singleton(
            'laravel-tactician.make.handler', function ($app) {
                return new MakeTacticianHandlerCommand($app['files']);
            }
        );
        $this->commands('laravel-tactician.make.handler');

        // Register Comman+Handler Generator Command
        $this->app->singleton(
            'laravel-tactician.make.tactician', function () {
                return new MakeTacticianCommand();
            }
        );
        $this->commands('laravel-tactician.make.tactician');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
            __DIR__.'/../../config/config.php' => config_path('laravel-tactician.php'),
            ]
        );
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'laravel-tactician'
        );
    }
}
