<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Middleware\AddBusNameStampMiddlewareFactory;

class AddBusNameStampMiddlewareFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $factory = [AddBusNameStampMiddlewareFactory::class, 'bus_name'];

        $container = $this->prophesize(ContainerInterface::class);

        $service = $factory($container->reveal());

        $this->assertInstanceOf(AddBusNameStampMiddleware::class, $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [AddBusNameStampMiddlewareFactory::class, 'bus_name'];
        $factory('foo');
    }
}
