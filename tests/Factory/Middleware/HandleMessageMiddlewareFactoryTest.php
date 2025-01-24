<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Middleware;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Middleware\HandleMessageMiddlewareFactory;

class HandleMessageMiddlewareFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactory(): void
    {
        $factory = [HandleMessageMiddlewareFactory::class, 'bus_name'];

        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'logger' => 'messenger.logger',
            ],
        ]);

        $logger = $this->prophesize(LoggerInterface::class);

        $container->get('messenger.logger')
            ->shouldBeCalled()
            ->willReturn($logger->reveal());

        $service = $factory($container->reveal());

        $this->assertInstanceOf(HandleMessageMiddleware::class, $service);
    }

    public function testFactoryWithoutOptionalDependencies(): void
    {
        $factory = [HandleMessageMiddlewareFactory::class, 'bus_name'];

        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([]);

        $service = $factory($container->reveal());

        $this->assertInstanceOf(HandleMessageMiddleware::class, $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [HandleMessageMiddlewareFactory::class, 'bus_name'];
        $factory('foo');
    }
}
