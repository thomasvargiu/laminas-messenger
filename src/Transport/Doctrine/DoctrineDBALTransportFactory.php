<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Transport\Doctrine;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

use function sprintf;
use function strpos;

class DoctrineDBALTransportFactory implements TransportFactoryInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $registry)
    {
        $this->container = $registry;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $configuration = Connection::buildConfiguration($dsn, $options);

        try {
            $driverConnection = $this->container->get($configuration['connection']);
        } catch (InvalidArgumentException $e) {
            throw new TransportException(sprintf('Could not find Doctrine connection from Messenger DSN "%s".', $dsn), 0, $e);
        }

        $connection = new Connection($configuration, $driverConnection);

        return new DoctrineTransport($connection, $serializer);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'doctrinedbal://');
    }
}
