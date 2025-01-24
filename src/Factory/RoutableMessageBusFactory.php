<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

use function array_key_exists;
use function sprintf;

/**
 * @psalm-api
 */
final class RoutableMessageBusFactory
{
    /** @var string */
    private $fallbackBusName;

    public function __construct(string $fallbackBusName = 'messenger.bus.default')
    {
        $this->fallbackBusName = $fallbackBusName;
    }

    public function __invoke(ContainerInterface $container): MessageBusInterface
    {
        /** @var null|MessageBusInterface $fallbackBus */
        $fallbackBus = $container->has($this->fallbackBusName) ? $container->get($this->fallbackBusName) : null;

        return new RoutableMessageBus($container, $fallbackBus);
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
