<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TMV\Laminas\Messenger\Exception\RuntimeException;

final class UnsupportedTransportFactory implements TransportFactoryInterface
{
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        throw new RuntimeException('Unsupported transport');
    }

    public function supports(string $dsn, array $options): bool
    {
        return false;
    }
}
