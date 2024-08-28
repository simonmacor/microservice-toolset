<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery;

class FileConfig extends Config
{
    public function __construct(string $filePath)
    {
        parent::__construct($filePath);
    }
}