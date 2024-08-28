<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery;

readonly class ServiceConfiguration
{
    public function __construct(
        private string $address,
        private string $secret
    )
    {
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