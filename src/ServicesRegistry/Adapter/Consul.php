<?php

declare(strict_types=1);

namespace MicroserviceToolset\ServicesRegistry\Adapter;

use DCarbone\PHPConsulAPI\Catalog\CatalogService;
use DCarbone\PHPConsulAPI\Config as ConsulConfig;
use DCarbone\PHPConsulAPI\Consul as ConsulClient;
use MicroserviceToolset\ServicesRegistry\AdapterException;
use MicroserviceToolset\ServicesRegistry\ApiConfig;
use MicroserviceToolset\ServicesRegistry\ServiceConfiguration;
use DCarbone\PHPConsulAPI\Error;

class Consul extends Adapter
{

    private const HTTP_CLIENT           = 'HttpClient';
    private const HTTP_AUTH             = 'HttpAuth';
    private const WAIT_TIME             = 'WaitTime';
    private const ADDRESS               = 'Address';
    private const SCHEME                = 'Scheme';
    private const JSON_ENCODE_OPTS      = 'JSONEncodeOpts';
    private const TOKEN                 = 'Token';
    private const TOKEN_FILE            = 'TokenFile';
    private const CA_FILE               = 'CAFile';
    private const CERT_FILE             = 'CertFile';
    private const KEY_FILE              = 'KeyFile';
    private const INSECURE_SKIP_VERIFY  = 'InsecureSkipVerify';
    private readonly ConsulClient $consulClient;

    /**
     * @param array<string, mixed> $extra
     */
    public function __construct(ApiConfig $config, array $extra = [])
    {
        $extra[self::HTTP_CLIENT] = $config->getClient();
        $extra[self::ADDRESS] = $config->getAddress();

        $this->consulClient = $this->createConsulClient($extra);

        parent::__construct($config);
    }

    public function getServiceByName(string $serviceName): ?ServiceConfiguration
    {
        $response = $this->consulClient->Catalog()->Service($serviceName);
        if ($response->getErr() instanceof Error) {
            throw new AdapterException($response->getErr()->getMessage());
        }

        $catalogService = $response->getValue() ;
        if ($catalogService !== null && $catalogService !== []) {
            return $this->createServiceConfiguration($catalogService);
        } else {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createConsulClient(array $config): ConsulClient
    {
        $consulConfigOption = [
            self::HTTP_CLIENT,
            self::HTTP_AUTH,
            self::WAIT_TIME,
            self::ADDRESS,
            self::SCHEME,
            self::JSON_ENCODE_OPTS,
            self::TOKEN,
            self::TOKEN_FILE,
            self::CA_FILE,
            self::CERT_FILE,
            self::KEY_FILE,
            self::INSECURE_SKIP_VERIFY,
        ];

        $invalidOptions = array_diff(array_keys($config), $consulConfigOption);
        if ($invalidOptions !== []) {
            throw new \InvalidArgumentException('Invalid option provided for consul client: ' . implode(', ', $invalidOptions));
        }

        return new ConsulClient(new ConsulConfig($config));
    }

    /**
     * @param CatalogService[] $catalogService
     */
    private function createServiceConfiguration(array $catalogService): ServiceConfiguration
    {
        if (count($catalogService) > 1) {
            usort($catalogService, fn(CatalogService $serviceLeft, CatalogService $serviceRight): int => $serviceLeft->ServiceWeights->getWarning() <=> $serviceRight->ServiceWeights->getWarning());
        }

        $service = $catalogService[0];

        return new ServiceConfiguration(
            $service->Address,
            $service->ServiceMeta['secret'],
        );
    }

}