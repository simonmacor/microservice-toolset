# JSON RPC Client

The JSON-RPC client complies with the [2.0 specification](https://www.jsonrpc.org/specification) of the standard.

## Usage

### Example with file adapter

File example:
```json
{
  "service_name": {
    "address": "http://localhost:2324",
    "secret": "test_secret"
  }
}
```
Code:
```php
//create Client

$client = new \MicroserviceToolset\JsonRpc\Client(
    new \GuzzleHttp\Client(),
    new \MicroserviceToolset\ServicesDiscovery\Adapter\File(
        new \MicroserviceToolset\ServicesDiscovery\FileConfig('your_filepath')
    ),
    new \Monolog\Logger(), //  \Psr\Log\LoggerInterface
    new \MicroserviceToolset\Context("correlation_id", "principal")
);


// call service
$client->callService(
    "service_name", 
    "method", 
    ['param1' => "value1", "param2" => "value2"],
    2
);
```

In the example above, you can see that our logger is a [Monolog](https://github.com/Seldaek/monolog) Logger. Microservice Toolset offers a specific **Handler** to ensure that the context, which by default contains a **correlation ID** and **[the principal](https://en.wikipedia.org/wiki/Principal_(computer_security))**, is present in the logs. For more details, see [Logging](logging.md).

This client implements method version management. It appends a `.v{version_number}` to the end of the method passed as a parameter to `callService()`.

