<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\ConfigProvider;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

abstract class AbstractDoctrineMiddlewareFactory
{
    protected ?string $name;

    /**
     * @final
     */
    public function __construct(?string $connectionName = null)
    {
        $this->name = $connectionName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    abstract public function __invoke(ContainerInterface $container): MiddlewareInterface;

    /**
     * @return array{logger?: string|null}
     */
    protected function getConfig(ContainerInterface $container): array
    {
        /** @var array{logger?: string|null} $config */
        $config = $container->get('config')[ConfigProvider::CONFIG_KEY] ?? [];

        return $config;
    }

    /**
     * @return array{connection_registry?: string|null, manager_registry: string}
     */
    protected function getDoctrineConfig(ContainerInterface $container): array
    {
        /** @var array{connection_registry?: string|null, manager_registry: string} $config */
        $config = $this->getConfig($container)['doctrine'];

        return $config;
    }

    protected function getManagerRegistry(ContainerInterface $container): ManagerRegistry
    {
        $config = $this->getDoctrineConfig($container);
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $container->get($config['manager_registry']);

        return $managerRegistry;
    }

    public static function __set_state(array $data): static
    {
        return new static($data['name'] ?? null);
    }

    /**
     * @psalm-api
     *
     * @param array<int, mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): MiddlewareInterface
    {
        if (! array_key_exists(0, $arguments) || ! $arguments[0] instanceof ContainerInterface) {
            throw new InvalidArgumentException(sprintf(
                'The first argument must be of type %s',
                ContainerInterface::class
            ));
        }

        return (new static($name))($arguments[0]);
    }
}
