<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\JsonRpcError;

class InvalidRequestException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::InvalidRequest->message(), (int)JsonRpcError::InvalidRequest->code());
    }
}