<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Contracts;

interface ConfigurableInterface
{
    public function setConfig(array $config): void;

    public function getConfig(): array;

    public function hasConfig(string $key): bool;

    public function getConfigValue(string $key, mixed $default = null): mixed;
}
