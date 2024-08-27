<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\Error;

class ServerErrorException extends JsonRpcException
{
    public function __construct(int $code)
    {
        parent::__construct(Error::ServerError->message(), $code);
    }
}