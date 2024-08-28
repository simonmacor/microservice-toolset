<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesDiscovery\Adapter;

use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\FileNotFound;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\InvalidFormatException;
use MicroserviceToolset\ServicesDiscovery\FileConfig;
use MicroserviceToolset\ServicesDiscovery\ServiceConfiguration;

class File extends Adapter
{
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
            throw new FileNotFound($filepath);
        }

        return $this->extractFileContent($fileContent);
    }

    /**
     * @return array<string, string[]>
     */
    private function extractFileContent(string $fileContent): array
    {
        /** @var array<string, array<string, string>> $jsonDecodedContent */
        $jsonDecodedContent = json_decode($fileContent, true, flags: JSON_THROW_ON_ERROR);

        /** @var array<string, string> $service */
        foreach ($jsonDecodedContent as $serviceName => $service) {
            if (!isset($service['address']) || !isset($service['secret'])) {
                throw new InvalidFormatException(
                    sprintf(
                        'the service "%s" definition must contains service "address" and "secret"',
                        $serviceName
                    )
                );
            }
        }

        return $jsonDecodedContent;
    }
}