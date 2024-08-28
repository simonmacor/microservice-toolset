<?php

namespace MicroserviceToolset\ServicesDiscovery\Adapter;

use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\InvalidFormatException;

trait ServicesExtractionTrait
{
    /**
     * @return array<string, string[]>
     */
    protected function extractServiceConfigurationFromJson(string $fileContent): array
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