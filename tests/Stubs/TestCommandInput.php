<?php

namespace Victormln\LaravelTactician\Tests\Stubs;


class TestCommandInput
{
    public $property;

    public function __construct($property)
    {
        $this->property = $property;
    }
}
