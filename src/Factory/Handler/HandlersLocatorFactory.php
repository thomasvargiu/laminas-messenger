<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Handler;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use TMV\Laminas\Messenger\Handler\ContainerHandlersLocator;

final class HandlersLocatorFactory
{
    /** @var string */
    private $busName;

    public function __construct(string $busName = 'messenger.bus.default')
    {
        $this->busName = $busName;
    }

    public function __invoke(ContainerInterface $container): HandlersLocatorInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];
        /** @var string[][]|array<string, array<string|callable>> $handlerDescriptors */
        $handlerDescriptors = $config['messenger']['buses'][$this->busName]['handlers'] ?? [];

        return new ContainerHandlersLocator($container, $handlerDescriptors);
    }
}
