<?php

namespace Victormln\LaravelTactician\Tests\Stubs;

class TestCommand {

    private $property;

    private $propertyTwo;

    public function __construct($property = null, $propertyTwo = "First Name")
    {
        $this->property = $property;
        $this->propertyTwo = $propertyTwo;
    }

    public function property()
    {
        return $this->property;
    }

    public function propertyTwo(): string
    {
        return $this->propertyTwo;
    }

}
