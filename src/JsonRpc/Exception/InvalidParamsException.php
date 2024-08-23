<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\JsonRpcError;

class InvalidParamsException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::InvalidParams->message(), (int)JsonRpcError::InvalidParams->code());
    }
}