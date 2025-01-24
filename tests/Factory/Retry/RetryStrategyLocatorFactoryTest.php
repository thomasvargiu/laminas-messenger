<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Retry;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use TMV\Laminas\Messenger\Factory\Retry\RetryStrategyLocatorFactory;

class RetryStrategyLocatorFactoryTest extends TestCase
{
    use ProphecyTrait;
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'transports' => [
                    'foo' => [
                        'retry_strategy' => 'foo_strategy',
                    ],
                ],
            ],
        ]);

        $strategy = $this->prophesize(RetryStrategyInterface::class);
        $container->get('foo_strategy')
            ->shouldBeCalled()
            ->willReturn($strategy->reveal());

        $factory = new RetryStrategyLocatorFactory();
        $service = $factory($container->reveal());

        $this->assertInstanceOf(ServiceManager::class, $service);

        $this->assertSame($strategy->reveal(), $service->get('foo'));
    }
}
