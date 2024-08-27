<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\Error;

class MethodNotFoundException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(Error::MethodNotFound->message(), (int)Error::MethodNotFound->code());
    }
}