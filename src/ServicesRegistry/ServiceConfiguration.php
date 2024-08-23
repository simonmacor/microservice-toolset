<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesRegistry;

readonly class ServiceConfiguration
{
    public function __construct(
        private string $serviceName,
        private string $address,
        private string $secret
    )
    {
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}