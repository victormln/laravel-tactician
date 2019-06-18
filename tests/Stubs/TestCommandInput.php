<?php

namespace Victormln\LaravelTactician\Tests\Stubs;


class TestCommandInput
{
    private $property;

    public function __construct($property)
    {
        $this->property = $property;
    }

    public function property()
    {
        return $this->property;
    }
}
