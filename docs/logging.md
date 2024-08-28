# Logging

Microservice Toolset uses the [Monolog](https://github.com/Seldaek/monolog) library. To ensure that the context is always present in our logs, a [Monolog Handler](https://github.com/Seldaek/monolog/blob/main/doc/04-extending.md) is available.

## Usage

```php

// create context
$context = new \MicroserviceToolset\Context(
    "correlation_id",
    "principal",
    ['extraData1' => "data1"]
);

$handler = new \MicroserviceToolset\Logger\ContextHandler($context);

$logger = new \Monolog\Logger();
$logger->setHandlers([$handler]);

```