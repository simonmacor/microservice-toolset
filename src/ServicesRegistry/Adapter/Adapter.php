<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesRegistry\Adapter;

use MicroserviceToolset\ServicesRegistry\Config;
use MicroserviceToolset\ServicesRegistry\ServiceConfiguration;

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