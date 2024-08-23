<?php

declare(strict_types=1);

namespace MicroserviceToolset;

class Context
{
    /**
     * @param string $id
     * @param string $principal
     * @param array<string, mixed> $extra
     */
    public function __construct(
        private readonly string $id,
        private readonly string $principal,
        private readonly array $extra,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrincipal(): string
    {
        return $this->principal;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }
}