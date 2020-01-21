<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Middleware;

use function array_key_exists;
use Psr\Container\ContainerInterface;
use function sprintf;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Messenger\Exception\InvalidArgumentException;

abstract class AbstractDoctrineMiddlewareFactory
{
    /** @var string */
    protected $connectionName;

    final public function __construct(string $connectionName = 'orm_default')
    {
        $this->connectionName = $connectionName;
    }

    abstract public function __invoke(ContainerInterface $container): MiddlewareInterface;

    /**
     * @param string $name
     * @param array<int, mixed> $arguments
     *
     * @return MiddlewareInterface
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
