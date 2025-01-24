<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Retry;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\MultiplierRetryStrategy;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;

use function is_string;

/**
 * @psalm-api
 */
final class RetryStrategyFactory
{
    /** @var string */
    private $transportName;

    public function __construct(string $transportName)
    {
        $this->transportName = $transportName;
    }

    public function __invoke(ContainerInterface $container): RetryStrategyInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];

        /** @var array<string, mixed>|string $retryConfig */
        $retryConfig = $config['messenger']['transports'][$this->transportName]['retry_strategy'] ?? [];

        if (is_string($retryConfig)) {
            return $container->get($retryConfig);
        }

        return new MultiplierRetryStrategy(
            (int) ($retryConfig['max_retries'] ?? 3),
            (int) ($retryConfig['delay'] ?? 1_000),
            (float) ($retryConfig['multiplier'] ?? 2.),
            (int) ($retryConfig['max_delay'] ?? 0)
        );
    }
}
