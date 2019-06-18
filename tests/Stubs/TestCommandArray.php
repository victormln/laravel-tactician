<?php

namespace Victormln\LaravelTactician\Tests\Stubs;


class TestCommandArray {

    private $data;

    public function __construct(array $data = [
        'DefaultPropertyOne' => 'John',
        'DefaultPropertyTwo' => 'Doe'
    ])
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }

}
