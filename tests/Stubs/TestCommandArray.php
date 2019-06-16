<?php

namespace Victormln\LaravelTactician\Tests\Stubs;


class TestCommandArray {

    public $data;

    public function __construct(array $data = [
        'DefaultPropertyOne' => 'John',
        'DefaultPropertyTwo' => 'Doe'
    ])
    {
        $this->data = $data;
    }

}
