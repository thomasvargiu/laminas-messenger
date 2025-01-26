<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Middleware\DoctrineCloseConnectionMiddleware;

/**
 * @psalm-api
 */
final class DoctrineCloseConnectionMiddlewareFactory extends AbstractDoctrineMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        /** @var ManagerRegistry $manager */
        $manager = $container->get(ManagerRegistry::class);

        return new DoctrineCloseConnectionMiddleware(
            $manager,
            $this->connectionName ?? $manager->getDefaultConnectionName(),
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
}
