[![CI](https://github.com/simonmacor/microservice-toolset/actions/workflows/ci.yml/badge.svg)](https://github.com/simon/actions/workflows/ci.yml) [![Coverage Status](https://coveralls.io/repos/github/simonmacor/microservice-toolset/badge.svg)](https://coveralls.io/github/simonmacor/microservice-toolset)
# Microservice toolset
Microservice Toolset is a PHP library designed to support microservices architecture by providing a set of tools including a logger and an HTTP client that complies to the JSON-RPC standard.
Each service call generates a log entry for both the request sent and the response received. These entries include a context (which can be extended) that contains a correlation ID and a principal by default. 
Microservice Toolset is compatible with Etcd and Consul for service registration and discovery.

## Requirements
- PHP 8.2 or higher
- Dependencies:
  - [dcarbone/php-consul-api](https://github.com/dcarbone/php-consul-api): ^2.0
  - [linkorb/etcd-php](https://github.com/linkorb/etcd-php): ^1.3
  - [monolog/monolog](https://github.com/Seldaek/monolog): ^3.7

## Features

* [JSON-RPC 2.0](https://www.jsonrpc.org/specification) Compliant HTTP Client: Easily make JSON-RPC calls between services.
* Advanced Logging: Built-in logger for structured logging.
* Service Discovery: Integrates with Etcd, Consul or json file for service registration and discovery.

## Installation

You can install the package via Composer

```bash
composer require simonmacor/microservice-toolset
```

## Documentation

* [JSON-RPC client](docs/json_rpc.md)
* [Logging](docs/logging.md)
* [Service discovery](docs/service_discovery.md)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) file for details.

### Third-Party Licenses

This project includes library [php-consul-api](https://github.com/dcarbone/php-consul-api) that are licensed under the Apache License 2.0.
You can find a copy of the Apache License 2.0 [here](https://www.apache.org/licenses/LICENSE-2.0).

### License Compatibility

While the majority of the code in this project is under the MIT License, the parts that are derived from or include code licensed under the Apache License 2.0 will continue to be governed by the terms of that license.

Please ensure that you adhere to the requirements of both licenses when using, modifying, or distributing the code.

