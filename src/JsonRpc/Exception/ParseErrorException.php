<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc\Exception;

use MicroserviceToolset\JsonRpc\Error;

class ParseErrorException extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct(Error::ParseError->message(), (int)Error::ParseError->code());
    }
}