<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File;

class FileNotFound extends \RuntimeException
{
    public function __construct(string $filepath)
    {
        parent::__construct(sprintf('File "%s" not found', $filepath));
    }
}