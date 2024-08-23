<?php

declare(strict_types=1);

namespace MicroserviceToolset\Exception;

class NotJsonRpcResponseReceived extends \RuntimeException
{
    public function __construct(string $message = "The response received is not a json-rpc response.")
    {
        parent::__construct($message);
    }
}