<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc;

class Request
{
    const JSON_RPC_VERSION = "2.0";

    public function __construct(
        private string $id,
        private string $method,
        private array $params = [],
        private string $jsonrpc = self::JSON_RPC_VERSION,
    )
    {
    }

    public function toArray(): array
    {
        return [
            "jsonrpc" => $this->jsonrpc,
            "method" => $this->method,
            "params" => $this->params,
            "id" => $this->id,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}