<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset\JsonRpc;

enum JsonRpcError
{
    case ParseError;
    case InvalidRequest;
    case MethodNotFound;
    case InvalidParams;
    case InternalError;
    case ServerError;

    public function message(): string
    {
        return match ($this) {
            self::ParseError => 'Parse error',
            self::InvalidRequest => 'Invalid Request',
            self::MethodNotFound => 'Method not found',
            self::InvalidParams => 'Invalid params',
            self::InternalError => 'Internal error',
            self::ServerError => 'Server error',
        };
    }

    /**
     * @return int|array<int>
     */
    public function code(): int|array
    {
        return match ($this) {
            self::ParseError => -32700,
            self::InvalidRequest => -32600,
            self::MethodNotFound => -32601,
            self::InvalidParams => -32602,
            self::InternalError => -32603,
            self::ServerError => range(-32000, -32099),
        };
    }
}