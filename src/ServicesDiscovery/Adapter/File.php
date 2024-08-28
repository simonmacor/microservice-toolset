<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery\Adapter;

use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\FileNotFoundException;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\InvalidFormatException;
use MicroserviceToolset\ServicesDiscovery\FileConfig;
use MicroserviceToolset\ServicesDiscovery\ServiceConfiguration;

class File extends Adapter
{
    use ServicesExtractionTrait;

    public function __construct(FileConfig $config)
    {
        parent::__construct($config);
    }

    public function getServiceByName(string $serviceName): ?ServiceConfiguration
    {
        $fileContent = $this->getFileContent();

        if (isset($fileContent[$serviceName])) {
            return new ServiceConfiguration(
                $fileContent[$serviceName]['address'],
                $fileContent[$serviceName]['secret']
            );
        } else {
            return null;
        }
    }

    /**
     * @return array<string, string[]>
     */
    private function getFileContent(): array
    {
        $filepath = $this->getConfig()->getAddress();
        $fileContent = file_get_contents($filepath);
        if (empty($fileContent)) {
            throw new FileNotFoundException($filepath);
        }

        return $this->extractServiceConfigurationFromJson($fileContent);
    }
}