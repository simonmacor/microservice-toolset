<?php

declare(strict_types=1);

namespace MicroserviceToolset\Tests\ServicesDiscovery\Adapter;

use MicroserviceToolset\ServicesDiscovery\Adapter\Adapter;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\FileNotFound;
use MicroserviceToolset\ServicesDiscovery\Adapter\Exception\File\InvalidFormatException;
use MicroserviceToolset\ServicesDiscovery\Adapter\File;
use MicroserviceToolset\ServicesDiscovery\FileConfig;
use MicroserviceToolset\ServicesDiscovery\ServiceConfiguration;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FileTest extends TestCase
{
    use ProphecyTrait;
    public function testFileIsAnAdapter(): void
    {
        $this->assertInstanceOf(Adapter::class, new File($this->prophesize(FileConfig::class)->reveal()));
    }

    public function testFileNotFound(): void
    {
        $config = $this->prophesize(FileConfig::class);
        $config->getAddress()->willReturn('/fichiertest.json');
        $this->expectException(FileNotFound::class);
        $this->expectExceptionMessage('File "/fichiertest.json" not found');
        $testedInstance = new File($config->reveal());
        $testedInstance->getServiceByName('test');
    }

    public function testInvalidJsonFile(): void
    {
        $config = $this->prophesize(FileConfig::class);
        $config->getAddress()->willReturn(__DIR__.'/../../data/files/file-adapter-invalid-json.json');
        $this->expectException(\JsonException::class);

        $testedInstance = new File($config->reveal());
        $testedInstance->getServiceByName('test');
    }

    public function testInvalidServiceFormat(): void
    {
        $config = $this->prophesize(FileConfig::class);
        $config->getAddress()->willReturn(__DIR__.'/../../data/files/file-adapter-invalid-service-format.json');
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('the service "test" definition must contains service "address" and "secret"');

        $testedInstance = new File($config->reveal());
        $testedInstance->getServiceByName('test');
    }

    public function testFileAdapterReturnServiceConfiguration(): void
    {
        $config = $this->prophesize(FileConfig::class);
        $config->getAddress()->willReturn(__DIR__.'/../../data/files/file-adapter-valid-service-directory.json');

        $testedInstance = new File($config->reveal());
        $result = $testedInstance->getServiceByName('test');
        $this->assertInstanceOf(ServiceConfiguration::class, $result);
        $this->assertSame('testAddress', $result->getAddress());
        $this->assertSame('testSecret', $result->getSecret());
    }
}
