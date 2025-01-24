<?php
declare(strict_types=1);

namespace TMV\Laminas\Messenger;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * @internal
 */
class ServiceProvider implements ServiceProviderInterface
{

    /**
     * @var array<string, callable>
     */
    protected array $factories;

    /**
     * @param array<string, callable> $factories
     */
    public function __construct(array $factories) {
        $this->factories = $factories;
    }

    public function get(string $id): mixed
    {
        if (! array_key_exists($id, $this->factories)) {
            throw new ServiceNotFoundException(sprintf("Service %s not found", $id));
        }
        return $this->factories[$id]();
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->factories);
    }

    public function getProvidedServices(): array
    {
        return $this->providedServices;
    }
}
