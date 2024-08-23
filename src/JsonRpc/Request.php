<?php

declare(strict_types=1);

namespace MicroserviceToolset\JsonRpc;

class Request
{
    public const JSON_RPC_VERSION = "2.0";

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        private readonly string $id,
        private readonly string $method,
        private readonly array $params = [],
        private readonly string $jsonrpc = self::JSON_RPC_VERSION,
    )
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            "jsonrpc" => $this->jsonrpc,
            "method" => $this->method,
            "params" => $this->params,
            "id" => $this->id,
        ];
    }

    public function toJson(): null|string
    {
        $json = json_encode($this->toArray());
        if ($json){
            return $json;
        } else {
            return null;
        }
    }
}