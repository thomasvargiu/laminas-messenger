# laminas-messenger

[![Latest Stable Version](https://poser.pugx.org/thomasvargiu/laminas-messenger/v/stable)](https://packagist.org/packages/thomasvargiu/laminas-messenger)
[![Total Downloads](https://poser.pugx.org/thomasvargiu/laminas-messenger/downloads)](https://packagist.org/packages/thomasvargiu/laminas-messenger)
[![License](https://poser.pugx.org/thomasvargiu/laminas-messenger/license)](https://packagist.org/packages/thomasvargiu/laminas-messenger)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/laminas-messenger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/laminas-messenger/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/thomasvargiu/laminas-messenger/badges/build.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/laminas-messenger/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/laminas-messenger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/laminas-messenger/?branch=master)

Factories to use the Symfony Messenger in Laminas and Mezzio applications (ex zend-framework and zend-expressive)

## Usage

You need to add console commands to your application. The following command services are already configured for you:

- `Symfony\Component\Messenger\Command\ConsumeMessagesCommand`
- `Symfony\Component\Messenger\Command\SetupTransportsCommand`
- `Symfony\Component\Messenger\Command\FailedMessagesRemoveCommand`
- `Symfony\Component\Messenger\Command\FailedMessagesRetryCommand`
- `Symfony\Component\Messenger\Command\FailedMessagesShowCommand`
- `Symfony\Component\Messenger\Command\StopWorkersCommand` (see note below)

To use the `Symfony\Component\Messenger\Command\StopWorkersCommand` command you should set a `CacheItemPoolInterface`
implementation (see below).

A default message bus is already configured for you with the following service name: `messenger.bus.default`.
You can read the [Symfony documentation](https://symfony.com/doc/current/components/messenger.html) to know
how to use it.

## Configuration

This is an example configuration:

```php
use TMV\Laminas\Messenger\Factory;
use Symfony\Component\Messenger;

return [
    'dependencies' => [
        'factories' => [
            'messenger.bus.foo' => [Factory\MessageBusFactory::class, 'messenger.bus.foo'], // the name must be the same as the bus configuration key
            'messenger.transport.async' => [Factory\Transport\TransportFactory::class, 'messenger.transport.async'], // the name must be the same as the transport configuration key
        ],
    ],
    'messenger' => [
        'failure_transport' => null, // your failure transport service name (optional)
        'logger' => null, // your custom logger service name (optional)
        'default_serializer' => SFMessenger\Transport\Serialization\PhpSerializer::class, // default messenger serializer, it should be a service name
        'cache_pool_for_restart_signal' => null, // CacheItemPoolInterface service name implementation if you want to use stop workers command
        'transport_factories' => [
            // here you can add your custom transport factories services
        ],
        'buses' => [
            'messenger.bus.foo' => [ // bus service name, it should be registered as a service with the same name
                'default_middleware' => true, // if you want to include default middleware (default: true)
                'middleware' => [ // your custom middleware service names
                    My\FooMiddleware::class,
                ],
                'allow_no_handler' => false, // allow no handlers (default: false)
                'handlers' => [ // your handlers
                    My\FooMessageType::class => [
                        My\FooMessageHandler::class,
                    ],
                ],
                'routes' => [
                    My\FooMessageType::class => ['messenger.transport.async'], // route message types to this transport
                ],
            ],
        ],
        'transports' => [
            'messenger.transport.async' => [
                'dsn' => 'amqp://guest:guest@rabbitmq:5672',
                'serializer' => Messenger\Transport\Serialization\PhpSerializer::class, // custom serializer service
                'options' => [
                    'exchange' => [
                        'name' => 'messenger_events',
                    ],
                    'queues' => [
                        'messenger_events' => [],
                    ],
                ],
                'retry_strategy' => [
                    'max_retries' => 3,
                    'delay' => 1000,
                    'multiplier' => 2,
                    'max_delay' => 0,
                ],
            ],
        ],
    ],
];
```
