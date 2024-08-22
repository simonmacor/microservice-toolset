<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\Exception;
use RuntimeException;
class ServiceNotFound extends RuntimeException
{
    public function __construct($serviceName)
    {
        parent::__construct(sprintf('No configuration found for the service "%s"', $serviceName));
    }
}