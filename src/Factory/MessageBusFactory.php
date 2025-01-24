<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Middleware\HandleMessageMiddlewareFactory;
use TMV\Laminas\Messenger\Factory\Middleware\SendMessageMiddlewareFactory;

use function array_key_exists;
use function array_map;
use function array_merge;
use function sprintf;

/**
 * @psalm-api
 */
final class MessageBusFactory
{
    /** @var string */
    private $busName;

    public function __construct(string $busName = 'messenger.bus.default')
    {
        $this->busName = $busName;
    }

    public function __invoke(ContainerInterface $container): MessageBusInterface
    {
        /** @var array<string, mixed> $busConfig */
        $busConfig = $container->get('config')['messenger']['buses'][$this->busName] ?? [];

        $includeDefaults = (bool) ($busConfig['default_middleware'] ?? true);

        /** @var string[] $middleware */
        $middleware = $busConfig['middleware'] ?? [];

        /** @var MiddlewareInterface[] $middleware */
        $middleware = array_map([$container, 'get'], $middleware);

        if ($includeDefaults) {
            $middleware = array_merge(
                [
                    new AddBusNameStampMiddleware($this->busName),
                    new RejectRedeliveredMessageMiddleware(),
                    new DispatchAfterCurrentBusMiddleware(),
                    new FailedMessageProcessingMiddleware(),
                ],
                $middleware,
                [
                    (new SendMessageMiddlewareFactory($this->busName))($container),
                    (new HandleMessageMiddlewareFactory($this->busName))($container),
                ]
            );
        }

        return new MessageBus($middleware);
    }

    /**
     * @psalm-api
     *
     * @param array<int, mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): MessageBusInterface
    {
        if (! array_key_exists(0, $arguments) || ! $arguments[0] instanceof ContainerInterface) {
            throw new InvalidArgumentException(sprintf(
                'The first argument must be of type %s',
                ContainerInterface::class
            ));
        }

        return (new static($name))($arguments[0]);
    }
}
