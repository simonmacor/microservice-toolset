<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery\Adapter;

use LinkORB\Component\Etcd\Client;
use LinkORB\Component\Etcd\Exception\KeyNotFoundException;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\AdapterException;
use MicroserviceToolset\ServicesDiscovery\ApiConfig;
use MicroserviceToolset\ServicesDiscovery\ServiceConfiguration;

class Etcd extends Adapter
{
    use ServicesExtractionTrait;
    private Client $etcdClient;

    public function __construct(ApiConfig $config)
    {
        $this->etcdClient = new Client($config->getAddress());
        parent::__construct($config);
    }

    public function getServiceByName(string $serviceName): ?ServiceConfiguration
    {
        try {
            $content = $this->etcdClient->get($serviceName);

            if (!empty($content)) {
                $service = sprintf('{"' . $serviceName . '": %s}', $content);
                $serviceData = $this->extractServiceConfigurationFromJson($service);

                return new ServiceConfiguration(
                    $serviceData[$serviceName]['address'],
                    $serviceData[$serviceName]['secret']
                );
            } else {
                return null;
            }
        } catch (KeyNotFoundException|\JsonException $exception) {
            throw new AdapterException($exception->getMessage());
        }
    }
}