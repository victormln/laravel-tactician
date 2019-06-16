Laravel Tactician
===============================

Laravel Tactician in an implementation of the Command Bus Tactician by Ross Tuck and forked from: joselfonseca/laravel-tactician

[![Build Status](https://travis-ci.org/victormln/laravel-tactician.svg)](https://travis-ci.org/victormln/laravel-tactician)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/victormln/laravel-tactician/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/victormln/laravel-tactician/?branch=master)

## Installation

To install this update your composer.json file to require

```json
    "victormln/laravel-tactician" : "1.0.*"
```

#### >= laravel5.5

ServiceProvider will be the magic for you

#### Other

Once the dependencies have been downloaded, add the service provider to your config/app.php file

```php
    'providers' => [
        ...
        Victormln\LaravelTactician\Providers\LaravelTacticianServiceProvider::class
        ...
    ]
```
You are done with the installation!

## Usage

To use the command bus you can resolve the bus from the laravel container like so

```php
    $bus = app('Victormln\LaravelTactician\CommandBusInterface');
```
Or you can inject it into a class constructor

```php

    use Victormln\LaravelTactician\CommandBusInterface;

    class MyController extends BaseController
    {

        public function __construct(CommandBusInterface $bus)
        {
            $this->bus = $bus;
        }

    }

```

**NOTE: This package is build to automatically grab the CommandHandler from the same path as the Command, so you don't have to do anything to bind the two files.**

After inject the commandBus, you can dispatch the command with the middleware.

```php
    // first parameter is the class name of the command
    // Second parameter is an array of input data to be mapped to the command
    // Third parameter is an array of middleware class names to be added to the stack
    $bus->dispatch('SomeCommand', [], []);
```

You can map the input data array of the Command's _constructor_ with plain list of arguments or the array itself. For example:

```php
    // Send parameters in an array of input data ...    
    $bus->dispatch('SomeCommand', [
        'propertyOne'   => 'One',
        'propertyTwo'   => 'Two',
        'propertyThree' => 'Three',
    ], []);
    
    // ... and recive them as individual parameters ... 
    Class SomeCommand {
        public function __construct($propertyOne = 'A', $propertyTwo = 'B', $propertyThree = 'C'){
            //...
        }
    }
    
    // ... or recive array of input data itself 
        Class SomeCommand {
            public function __construct(array $data = [
                'propertyOne'   => 'A',
                'propertyTwo'   => 'B',
                'propertyThree' => 'C',
            ]){
                //...
            }
        }
```

Of course, you can use default values!

For more information about the usage of the tactician command bus please visit [http://tactician.thephpleague.com/](http://tactician.thephpleague.com/)

## Example

Check out this example of the package implemented in a simple create order command [https://gist.github.com/victormln/24ee0e96666a06b16f92](https://gist.github.com/victormln/24ee0e96666a06b16f92)

## Bindings

You can configure the bindings for the locator, inflector, extractor and default bus publishing the config file like so

```bash
    php artisan vendor:publish
```

Then you can modify each class name and they will be resolved from the laravel container

```php
    return [
        // The locator to bind
        'locator' => 'Victormln\LaravelTactician\Locator\LaravelLocator',
        // The inflector to bind
        'inflector' => 'League\Tactician\Handler\MethodNameInflector\HandleInflector',
        // The extractor to bind
        'extractor' => 'League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor',
        // The bus to bind
        'bus' => 'Victormln\LaravelTactician\Bus'
    ];
```

## Generators

You can generate Commands and Handlers automatically using artisan

```
artisan make:tactician:command Foo
artisan make:tactician:handler Foo
```

This will create FooCommand and FooHandler and place them in the app/CommandBus/Commands and app/CommandBus/Handlers respectively

To run both at once

```
artisan make:tactician Foo
```

## Middleware included

Laravel tactician includes some useful middleware you can use in your commands

- Database Transactions: This Middleware will run the command inside a database transaction, if any exception is thrown the transaction won't be committed and the database will stay intact, you can find this middleware in `Victormln\LaravelTactician\Middleware\DatabaseTransactions`.  

## Change log

Please see the releases page [https://github.com/victormln/laravel-tactician/releases](https://github.com/victormln/laravel-tactician/releases)

## Tests

To run the test in this package, navigate to the root folder of the project and run

```bash
    composer install
```
Then

```bash
    vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email jose at ditecnologia dot com instead of using the issue tracker.

## Credits

- [Víctor Molina](https://github.com/victormln)
- Forked: from [Jose Luis Fonseca](https://github.com/joselfonseca)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
