<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesRegistry;

use GuzzleHttp\Client;

class ApiConfig extends Config
{
    public function __construct(string $serverAddress, Client $client)
    {
        parent::__construct($serverAddress, $client);
    }
}