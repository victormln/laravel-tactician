<?php

namespace Victormln\LaravelTactician\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Victormln\LaravelTactician\Providers\LaravelTacticianServiceProvider'];
    }

    public function test_assert_true()
    {
        $this->assertTrue(true);
    }

}
