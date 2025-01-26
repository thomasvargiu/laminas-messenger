<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Middleware\DoctrinePingConnectionMiddleware;

final class DoctrinePingConnectionMiddlewareFactory extends AbstractDoctrineMiddlewareFactory
{
    private string $query;

    public function __construct(string $connectionName = 'orm_default', string $query = 'SELECT 1;')
    {
        parent::__construct($connectionName);
        $this->query = $query;
    }

    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        /** @var ManagerRegistry $manager */
        $manager = $container->get(ManagerRegistry::class);

        return new DoctrinePingConnectionMiddleware(
            $manager,
            $this->connectionName ?? $manager->getDefaultConnectionName(),
            $this->query
        );
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

        return (new self($name))($arguments[0]);
    }

    /**
     * @param array{connectionName: string, query: string} $data
     */
    public static function __set_state(array $data): self
    {
        return new self(
            $data['connectionName'] ?? 'orm_default',
            $data['query'] ?? 'SELECT 1;',
        );
    }
}
