<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc\Exception;

use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;

class ServerErrorException extends JsonRpcException
{
    public function __construct(int $code)
    {
        parent::__construct(JsonRpcError::ServerError->message(), $code);
    }
}