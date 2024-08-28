<?php

declare(strict_types=1);

namespace MicroserviceToolset\Tests\ServicesDiscovery;

use GuzzleHttp\Client;
use MicroserviceToolset\ServicesDiscovery\ApiConfig;
use MicroserviceToolset\ServicesDiscovery\Config;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ApiConfigTest extends TestCase
{
    use ProphecyTrait;
    public function testApiConfigIsInstanceOfConfig(): void
    {

        $this->assertInstanceOf(
            Config::class,
            new ApiConfig('address', $this->prophesize(Client::class)->reveal())
        );
    }
}