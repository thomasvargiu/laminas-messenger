<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Command;

use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\StopWorkersCommand;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Command\StopWorkersCommandFactory;

class StopWorkersCommandFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'cache_pool_for_restart_signal' => 'messenger.cache_pool_for_restart_signal',
            ],
        ]);

        $cachePool = $this->prophesize(CacheItemPoolInterface::class);
        $container->get('messenger.cache_pool_for_restart_signal')
            ->shouldBeCalled()
            ->willReturn($cachePool->reveal());

        $factory = new StopWorkersCommandFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(StopWorkersCommand::class, $service);
    }

    public function testFactoryWithNoCachePoolShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cache_pool_for_restart_signal name');

        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'cache_pool_for_restart_signal' => null,
            ],
        ]);

        $factory = new StopWorkersCommandFactory();

        $factory($container->reveal());
    }
}
