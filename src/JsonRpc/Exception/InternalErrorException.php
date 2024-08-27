<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\Error;

class InternalErrorException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(Error::InternalError->message(), (int)Error::InternalError->code());
    }
}