<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc\Exception;

use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;

class ParseErrorException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(JsonRpcError::ParseError->message(), (int)JsonRpcError::ParseError->code());
    }
}