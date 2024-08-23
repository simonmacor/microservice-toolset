<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\JsonRpcError;

class ServerErrorException extends JsonRpcException
{
    public function __construct(int $code)
    {
        parent::__construct(JsonRpcError::ServerError->message(), $code);
    }
}