<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactory as SFTransportFactory;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

use function array_key_exists;
use function is_array;
use function is_string;
use function sprintf;

/**
 * @psalm-api
 */
final class TransportFactory
{
    /** @var string */
    private $transportNameOrDsn;

    public function __construct(string $transportNameOrDsn)
    {
        $this->transportNameOrDsn = $transportNameOrDsn;
    }

    public function __invoke(ContainerInterface $container): TransportInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];

        $transportConfig = $config['messenger']['transports'][$this->transportNameOrDsn] ?? null;

        $dsn = null;
        $options = [];
        $serializerName = $config['messenger']['default_serializer'] ?? null;

        if (is_array($transportConfig)) {
            $dsn = $transportConfig['dsn'] ?? null;
            $options = $transportConfig['options'] ?? [];
            $serializerName = $transportConfig['serializer'] ?? $serializerName;
        } elseif (is_string($transportConfig)) {
            $dsn = $transportConfig;
        }

        if (null === $dsn) {
            $dsn = $this->transportNameOrDsn;
        }

        $transportFactory = $container->get(SFTransportFactory::class);
        /** @var SerializerInterface $serializer */
        $serializer = $serializerName
            ? $container->get($serializerName)
            : new PhpSerializer();

        return $transportFactory->createTransport($dsn, $options, $serializer);
    }

    /**
     * @psalm-api
     *
     * @param array<int, mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): TransportInterface
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
