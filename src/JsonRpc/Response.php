<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc;

use Psr\Http\Message\ResponseInterface;

class Response
{
    private function __construct(
        private string $jsonrpc = "2.0",
        private ?string $id = null,
        private $result = null,
    ) {
    }

    public static function fromDecodedJson(array $decodedJson): self
    {
        return new self(id: $decodedJson['id'], result: $decodedJson['result']);
    }
    public function toArray(): array
    {
        return [
            "jsonrpc" => $this->jsonrpc,
            "result" => $this->result,
            "id" => $this->id,
        ];
    }
}