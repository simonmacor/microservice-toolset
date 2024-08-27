<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesRegistry;

use GuzzleHttp\Client;

class FileConfig extends Config
{
    public function __construct(string $filePath)
    {
        parent::__construct($filePath);
    }
}