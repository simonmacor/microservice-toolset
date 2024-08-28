# Service Discovery

Microservice Toolset offers multiple options for service discovery.
It works with Consul, Etcd, or a JSON file.

## Usage

### Consul

```php
$config = new \MicroserviceToolset\ServicesDiscovery\ApiConfig(
    "http://localhost",
    new \GuzzleHttp\Client()
);
 
 new \MicroserviceToolset\ServicesDiscovery\Adapter\Consul($config);
```
Consul adapter use [dcarbone/php-consul-api](https://github.com/dcarbone/php-consul-api) library

### Etcd

```php
$config = new \MicroserviceToolset\ServicesDiscovery\ApiConfig("http://localhost");
new \MicroserviceToolset\ServicesDiscovery\Adapter\Consul($config);
```

Etcd adapter use [linkorb/etcd-php](https://github.com/linkorb/etcd-php) library

### JSON file

file example:
```json
{
  "service_name": {
    "address": "http://localhost:2324",
    "secret": "test_secret"
  }
}
```

Adapter creation
```php
$config = new \MicroserviceToolset\ServicesDiscovery\FileConfig('your_filepath');
new \MicroserviceToolset\ServicesDiscovery\Adapter\File($config)
```