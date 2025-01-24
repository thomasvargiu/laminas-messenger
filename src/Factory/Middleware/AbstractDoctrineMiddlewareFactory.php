<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

use function array_key_exists;
use function sprintf;

abstract class AbstractDoctrineMiddlewareFactory
{
    protected string $connectionName;

    final public function __construct(string $connectionName = 'orm_default')
    {
        $this->connectionName = $connectionName;
    }

    abstract public function __invoke(ContainerInterface $container): MiddlewareInterface;

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
