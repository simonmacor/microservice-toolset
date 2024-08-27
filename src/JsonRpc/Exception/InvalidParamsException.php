<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\Error;

class InvalidParamsException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(Error::InvalidParams->message(), (int)Error::InvalidParams->code());
    }
}