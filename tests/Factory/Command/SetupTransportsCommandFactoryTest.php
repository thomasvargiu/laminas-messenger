<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Command;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\SetupTransportsCommand;
use TMV\Laminas\Messenger\Factory\Command\SetupTransportsCommandFactory;

class SetupTransportsCommandFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'transports' => [],
            ],
        ]);

        $factory = new SetupTransportsCommandFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(SetupTransportsCommand::class, $service);
    }
}
