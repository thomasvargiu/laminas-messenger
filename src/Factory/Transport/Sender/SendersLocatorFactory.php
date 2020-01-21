<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Transport\Sender;

use function class_exists;
use function interface_exists;
use Psr\Container\ContainerInterface;
use function sprintf;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;
use TMV\Messenger\Exception\LogicException;

final class SendersLocatorFactory
{
    /** @var string */
    private $busName;

    public function __construct(string $busName = 'messenger.bus.default')
    {
        $this->busName = $busName;
    }

    public function __invoke(ContainerInterface $container): SendersLocatorInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];
        /** @var string[][]|array<string, string[]> $routing */
        $routing = $config['messenger']['buses'][$this->busName]['routes'] ?? [];

        foreach ($routing as $message => $messageConfiguration) {
            if ('*' !== $message && ! class_exists((string) $message) && ! interface_exists((string) $message, false)) {
                throw new LogicException(sprintf('Invalid Messenger routing configuration: class or interface "%s" not found.', $message));
            }
        }

        return new SendersLocator($routing, $container);
    }
}
