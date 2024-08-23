<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\JsonRpcError;

class MethodNotFoundException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::MethodNotFound->message(), (int)JsonRpcError::MethodNotFound->code());
    }
}