<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset;

class Context
{
    public function __construct(
        private string $id,
        private string $principal
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
}