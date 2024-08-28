<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery;


use GuzzleHttp\Client;

abstract class Config
{
    public function __construct(protected string $address, protected ?Client $client = null)
    {
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }
}