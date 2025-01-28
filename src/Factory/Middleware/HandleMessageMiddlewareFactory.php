<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Handler\HandlersLocatorFactory;

use function array_key_exists;
use function sprintf;

final class HandleMessageMiddlewareFactory
{
    private string $busName;

    public function __construct(string $busName = 'messenger.bus.default')
    {
        $this->busName = $busName;
    }

    public function __invoke(ContainerInterface $container): HandleMessageMiddleware
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];
        /** @var string|null $logger */
        $logger = $config['messenger']['logger'] ?? null;
        $allowNoHandlers = (bool) ($config['messenger']['buses'][$this->busName]['allow_no_handler'] ?? false);

        $handlersLocator = (new HandlersLocatorFactory($this->busName))($container);
        $middleware = new HandleMessageMiddleware($handlersLocator, $allowNoHandlers);

        if ($logger && $container->has($logger)) {
            $middleware->setLogger($container->get($logger));
        }

        return $middleware;
    }

    public static function __set_state(array $data): self
    {
        return new self($data['busName'] ?? null);
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
