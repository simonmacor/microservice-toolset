<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc;

use Psr\Http\Message\ResponseInterface;

class Response
{
    private function __construct(
        private readonly string $jsonrpc = "2.0",
        private readonly mixed $id = null,
        private readonly mixed $result = null,
    ) {
    }

    /**
     * @param array<string, mixed> $decodedJson
     */
    public static function fromDecodedJson(array $decodedJson): self
    {
        return new self(id: $decodedJson['id'], result: $decodedJson['result']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            "jsonrpc" => $this->jsonrpc,
            "result" => $this->result,
            "id" => $this->id,
        ];
    }
}