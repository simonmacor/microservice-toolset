<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\Error;

class InvalidRequestException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(Error::InvalidRequest->message(), (int)Error::InvalidRequest->code());
    }
}