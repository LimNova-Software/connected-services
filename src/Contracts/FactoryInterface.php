<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Contracts;

interface FactoryInterface
{
    public function create(string $connector, array $config = []): ApiConnectorInterface;

    public function register(string $name, string $connectorClass): void;

    public function isRegistered(string $name): bool;

    public function getRegisteredConnectors(): array;
}
