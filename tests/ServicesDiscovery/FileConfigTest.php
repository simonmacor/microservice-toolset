<?php

declare(strict_types=1);

namespace MicroserviceToolset\Tests\ServicesDiscovery;

use MicroserviceToolset\ServicesDiscovery\Config;
use MicroserviceToolset\ServicesDiscovery\FileConfig;
use PHPUnit\Framework\TestCase;

class FileConfigTest extends TestCase
{
    public function testFileConfigIsInstanceOfConfig(): void
    {
        $this->assertInstanceOf(Config::class, new FileConfig('test'));
    }

    public function testFileConfigClientIsNull(): void
    {
        $this->assertNull((new FileConfig('test'))->getClient());
    }
}