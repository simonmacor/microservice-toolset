<?php

declare(strict_types=1);

namespace MicroserviceToolset\Tests\ServicesDiscovery\Adapter;

use LinkORB\Component\Etcd\Client;
use LinkORB\Component\Etcd\Exception\KeyNotFoundException;
use MicroserviceToolset\ServicesDiscovery\Adapter\Adapter;
use MicroserviceToolset\ServicesDiscovery\Adapter\Etcd;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\AdapterException;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\InvalidFormatException;
use MicroserviceToolset\ServicesDiscovery\ApiConfig;
use MicroserviceToolset\ServicesDiscovery\ServiceConfiguration;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class EtcdTest extends TestCase
{
    use ProphecyTrait;

    public function testEtcdIsInstanceOfAdapter(): void
    {
        $config = $this->prophesize(ApiConfig::class);
        $config->getAddress()->willReturn('localhost');
        $this->assertInstanceOf(Adapter::class, new Etcd($config->reveal()));
    }

    public function testEtcdServiceNotFound(): void
    {
        $this->expectException(AdapterException::class);
        $config = $this->prophesize(ApiConfig::class);
        $config->getAddress()->willReturn('http://test');

        $client = $this->prophesize(Client::class);
        $client->get('test')->willThrow(new KeyNotFoundException());

        $reflectionProperty = new \ReflectionProperty(Etcd::class, 'etcdClient');

        $testedInstance = new Etcd($config->reveal());

        $reflectionProperty->setValue($testedInstance, $client->reveal());

        $testedInstance->getServiceByName('test');
    }

    public function testEtcdAdapterThrowInvalidFormatException(): void
    {
        $this->expectException(InvalidFormatException::class);
        $config = $this->prophesize(ApiConfig::class);
        $config->getAddress()->willReturn('http://test');


        $client = $this->prophesize(Client::class);
        $client->get('test')->willReturn('{"test":"http://test", "secret": "test"}');

        $reflectionProperty = new \ReflectionProperty(Etcd::class, 'etcdClient');

        $testedInstance = new Etcd($config->reveal());

        $reflectionProperty->setValue($testedInstance, $client->reveal());

        $testedInstance->getServiceByName('test');
    }

    public function testEtcdAdapterReturnServiceConfiguration(): void
    {
        $config = $this->prophesize(ApiConfig::class);
        $config->getAddress()->willReturn('http://test');


        $client = $this->prophesize(Client::class);
        $client->get('test')->willReturn('{"address":"http://test", "secret": "test"}');

        $reflectionProperty = new \ReflectionProperty(Etcd::class, 'etcdClient');

        $testedInstance = new Etcd($config->reveal());

        $reflectionProperty->setValue($testedInstance, $client->reveal());

        $result = $testedInstance->getServiceByName('test');
        $this->assertInstanceOf(ServiceConfiguration::class, $result);
        $this->assertSame('http://test', $result->getAddress());
        $this->assertSame('test', $result->getSecret());
    }
}