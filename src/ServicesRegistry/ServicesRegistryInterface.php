<?php

namespace SimonMacor\MicroserviceToolset\ServicesRegistry;

interface ServicesRegistryInterface
{
    public function  getConfigurationByServiceName(string $serviceName): ?ServiceConfiguration;
}