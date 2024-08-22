<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc\Exception;

use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;

class InvalidParamsException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::InvalidParams->message(), JsonRpcError::InvalidParams->code());
    }
}