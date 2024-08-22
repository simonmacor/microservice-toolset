<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc\Exception;

use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;

class InternalErrorException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::InternalError->message(), (int)JsonRpcError::InternalError->code());
    }
}