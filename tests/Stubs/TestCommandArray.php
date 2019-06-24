<?php

namespace Victormln\LaravelTactician\Tests\Stubs;


class TestCommandArray {

    private $data;

    public function __construct(array $data = [
        'defaultPropertyOne' => 'John',
        'defaultPropertyTwo' => 'Doe'
    ]) {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }

}
