<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery\Adapter;

use MicroserviceToolset\ServicesDiscovery\Config;
use MicroserviceToolset\ServicesDiscovery\ServiceConfiguration;

abstract class Adapter
{
    public function __construct(protected Config $config)
    {
    }

    protected function getConfig(): Config
    {
        return $this->config;
    }

    abstract public function getServiceByName(string $serviceName): ?ServiceConfiguration;
}