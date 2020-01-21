<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Retry;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\MultiplierRetryStrategy;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use TMV\Laminas\Messenger\Factory\Retry\RetryStrategyFactory;

class RetryStrategyFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'transports' => [
                    'foo' => [
                        'retry_strategy' => [
                            'max_retries' => 99,
                            'delay' => 5000,
                            'multiplier' => 3.5,
                            'max_delay' => 5,
                        ],
                    ],
                ],
            ],
        ]);

        $factory = new RetryStrategyFactory('foo');

        /** @var MultiplierRetryStrategy $service */
        $service = $factory($container->reveal());

        $this->assertInstanceOf(MultiplierRetryStrategy::class, $service);
    }

    public function testFactoryGettingServiceFromContainer(): void
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

        $factory = new RetryStrategyFactory('foo');

        $service = $factory($container->reveal());

        $this->assertSame($strategy->reveal(), $service);
    }
}
