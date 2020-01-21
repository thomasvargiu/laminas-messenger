<?php

declare(strict_types=1);

namespace TMV\Messenger;

class Module
{
    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();
        $config = $provider();
        $config['service_manager'] = $provider->getDependencies();
        unset($config['dependencies']);

        return $config;
    }
}
