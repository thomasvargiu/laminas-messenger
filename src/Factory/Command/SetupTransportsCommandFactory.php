<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Command;

use function array_keys;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\SetupTransportsCommand;

final class SetupTransportsCommandFactory
{
    public function __invoke(ContainerInterface $container): SetupTransportsCommand
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $transportNames = array_keys($config['messenger']['transports'] ?? []);

        return new SetupTransportsCommand($container, $transportNames);
    }
}
