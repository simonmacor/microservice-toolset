<?php

declare(strict_types=1);

namespace MicroserviceToolset\Tests\ServicesRegistry\Adapter;

use GuzzleHttp\Client;
use MicroserviceToolset\ServicesRegistry\Adapter\Adapter;
use MicroserviceToolset\ServicesRegistry\Adapter\Consul;
use MicroserviceToolset\ServicesRegistry\AdapterException;
use MicroserviceToolset\ServicesRegistry\ApiConfig;
use MicroserviceToolset\ServicesRegistry\ServiceConfiguration;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ConsulTest extends TestCase
{
    use ProphecyTrait;
    public function testConsulIsInstanceOfAdapter(): void
    {
        $config = $this->prophesize(ApiConfig::class);
        $config->getAddress()->willReturn('localhost');
        $config->getClient()->willReturn($this->prophesize(Client::class)->reveal());

        $this->assertInstanceOf(Adapter::class, new Consul($config->reveal()));
    }

    public function testInvalidConsulConfigOptionIsProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option provided for consul client: testOption, testOption2');

        $guzzleClient = $this->prophesize(Client::class)->reveal();

        $config = $this->prophesize(ApiConfig::class);
        $config->getClient()->willReturn($guzzleClient);
        $config->getAddress()->willReturn('localhost');
        new Consul($config->reveal(), ['testOption' => 'test', 'testOption2' => 'test']);

    }

    public function testGetServiceByNameWhenCatalogContainsOneService(): void
    {
        $httpStream = $this->prophesize(StreamInterface::class);
        $httpStream->__toString()->willReturn('[
  {
    "ID": "40e4a748-2192-161a-0510-9bf59fe950b5",
    "Node": "t2.320",
    "Address": "localhost",
    "Datacenter": "dc1",
    "TaggedAddresses": {
      "lan": "192.168.10.10",
      "wan": "10.0.10.10"
    },
    "NodeMeta": {
      "somekey": "somevalue"
    },
    "CreateIndex": 51,
    "ModifyIndex": 51,
    "ServiceAddress": "172.17.0.3",
    "ServiceEnableTagOverride": false,
    "ServiceID": "32a2a47f7992:nodea:5000",
    "ServiceName": "wtesteb",
    "ServicePort": 5000,
    "ServiceMeta": {
      "secret": "test"
    },
    "ServiceTaggedAddresses": {
      "lan": {
        "address": "172.17.0.3",
        "port": 5000
      },
      "wan": {
        "address": "198.18.0.1",
        "port": 512
      }
    },
    "ServiceTags": ["test"],
    "ServiceProxy": {
      "DestinationServiceName": "",
      "DestinationServiceID": "",
      "LocalServiceAddress": "",
      "LocalServicePort": 0,
      "Config": {},
      "Upstreams": null
    },
    "ServiceConnect": {
      "Native": false,
      "Proxy": null
    },
    "Namespace": "default"
  }
]
');

        $httpResponse = $this->prophesize(ResponseInterface::class);
        $httpResponse->getStatusCode()->willReturn(200);
        $httpResponse->getHeaderLine(Argument::any())->willReturn('test');
        $httpResponse->getBody()->willReturn($httpStream->reveal());

        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient
            ->send(Argument::type(RequestInterface::class), Argument::type('array'))
            ->willReturn($httpResponse->reveal());

        $config = $this->prophesize(ApiConfig::class);
        $config->getClient()->willReturn($guzzleClient);
        $config->getAddress()->willReturn('localhost');

        $testedInstance = new Consul($config->reveal());
        $result = $testedInstance->getServiceByName('test');

        $this->assertInstanceOf(ServiceConfiguration::class, $result);
        $this->assertSame('localhost', $result->getAddress());
        $this->assertSame('test', $result->getSecret());
    }

    public function testGetServiceByNameFromCollectionWithTheLightestWeight(): void
    {
        $httpStream = $this->prophesize(StreamInterface::class);
        $httpStream->__toString()->willReturn('[
  {
    "ID": "40e4a748-2192-161a-0510-9bf59fe950b5",
    "Node": "t2.320",
    "Address": "localhost",
    "Datacenter": "dc1",
    "TaggedAddresses": {
      "lan": "192.168.10.10",
      "wan": "10.0.10.10"
    },
    "NodeMeta": {
      "somekey": "somevalue"
    },
    "CreateIndex": 51,
    "ModifyIndex": 51,
    "ServiceAddress": "172.17.0.3",
    "ServiceEnableTagOverride": false,
    "ServiceID": "32a2a47f7992:nodea:5000",
    "ServiceName": "wtesteb",
    "ServicePort": 5000,
    "ServiceMeta": {
      "secret": "test"
    },
    "ServiceTaggedAddresses": {
      "lan": {
        "address": "172.17.0.3",
        "port": 5000
      },
      "wan": {
        "address": "198.18.0.1",
        "port": 512
      }
    },
    "ServiceTags": ["test"],
    "ServiceProxy": {
      "DestinationServiceName": "",
      "DestinationServiceID": "",
      "LocalServiceAddress": "",
      "LocalServicePort": 0,
      "Config": {},
      "Upstreams": null
    },
    "ServiceConnect": {
      "Native": false,
      "Proxy": null
    },
    "ServiceWeights": {
      "Passing": 1,
      "Warning": 2
    },
    "Namespace": "default"
  },
  {
    "ID": "40e4a748-2192-161a-0510-9bf59fe950b6",
    "Node": "t2.320",
    "Address": "localhost2",
    "Datacenter": "dc1",
    "TaggedAddresses": {
      "lan": "192.168.10.10",
      "wan": "10.0.10.10"
    },
    "NodeMeta": {
      "somekey": "somevalue"
    },
    "CreateIndex": 51,
    "ModifyIndex": 51,
    "ServiceAddress": "172.17.0.3",
    "ServiceEnableTagOverride": false,
    "ServiceID": "32a2a47f7992:nodea:5000",
    "ServiceName": "wtesteb",
    "ServicePort": 5000,
    "ServiceMeta": {
      "secret": "test"
    },
    "ServiceTaggedAddresses": {
      "lan": {
        "address": "172.17.0.3",
        "port": 5000
      },
      "wan": {
        "address": "198.18.0.1",
        "port": 512
      }
    },
    "ServiceTags": ["test"],
    "ServiceProxy": {
      "DestinationServiceName": "",
      "DestinationServiceID": "",
      "LocalServiceAddress": "",
      "LocalServicePort": 0,
      "Config": {},
      "Upstreams": null
    },
    "ServiceConnect": {
      "Native": false,
      "Proxy": null
    },
    "ServiceWeights": {
      "Passing": 1,
      "Warning": 1
    },
    "Namespace": "default"
  }
]
');

        $httpResponse = $this->prophesize(ResponseInterface::class);
        $httpResponse->getStatusCode()->willReturn(200);
        $httpResponse->getHeaderLine(Argument::any())->willReturn('test');
        $httpResponse->getBody()->willReturn($httpStream->reveal());

        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient
            ->send(Argument::type(RequestInterface::class), Argument::type('array'))
            ->willReturn($httpResponse->reveal());

        $config = $this->prophesize(ApiConfig::class);
        $config->getClient()->willReturn($guzzleClient);
        $config->getAddress()->willReturn('localhost');

        $testedInstance = new Consul($config->reveal());
        $result = $testedInstance->getServiceByName('test');

        $this->assertInstanceOf(ServiceConfiguration::class, $result);
        $this->assertSame('localhost2', $result->getAddress());
        $this->assertSame('test', $result->getSecret());
    }
}