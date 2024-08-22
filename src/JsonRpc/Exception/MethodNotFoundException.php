<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc\Exception;

use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;

class MethodNotFoundException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::MethodNotFound->message(), JsonRpcError::MethodNotFound->code());
    }
}