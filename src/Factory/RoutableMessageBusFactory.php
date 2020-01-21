<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory;

use function array_key_exists;
use Psr\Container\ContainerInterface;
use function sprintf;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use TMV\Messenger\Exception\InvalidArgumentException;

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
     * @param string $name
     * @param array<int, mixed> $arguments
     *
     * @return MessageBusInterface
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
