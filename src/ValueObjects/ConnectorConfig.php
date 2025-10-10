<?php

declare(strict_types=1);

namespace LimNova\ApiManager\ValueObjects;

use LimNova\ApiManager\Enums\ConnectorType;

readonly class ConnectorConfig
{
    public function __construct(
        public ConnectorType $type,
        public string $baseUrl,
        public array $credentials = [],
        public array $options = [],
        public int $timeout = 30,
        public int $retries = 3,
        public array $defaultHeaders = []
    ) {}

    public function getCredential(string $key): mixed
    {
        return $this->credentials[$key] ?? null;
    }

    public function hasCredential(string $key): bool
    {
        return array_key_exists($key, $this->credentials);
    }

    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function getHeader(string $name): ?string
    {
        return $this->defaultHeaders[$name] ?? null;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->defaultHeaders);
    }

    public function withCredential(string $key, mixed $value): self
    {
        return new self(
            type: $this->type,
            baseUrl: $this->baseUrl,
            credentials: [...$this->credentials, $key => $value],
            options: $this->options,
            timeout: $this->timeout,
            retries: $this->retries,
            defaultHeaders: $this->defaultHeaders
        );
    }

    public function withOption(string $key, mixed $value): self
    {
        return new self(
            type: $this->type,
            baseUrl: $this->baseUrl,
            credentials: $this->credentials,
            options: [...$this->options, $key => $value],
            timeout: $this->timeout,
            retries: $this->retries,
            defaultHeaders: $this->defaultHeaders
        );
    }

    public function withHeader(string $name, string $value): self
    {
        return new self(
            type: $this->type,
            baseUrl: $this->baseUrl,
            credentials: $this->credentials,
            options: $this->options,
            timeout: $this->timeout,
            retries: $this->retries,
            defaultHeaders: [...$this->defaultHeaders, $name => $value]
        );
    }

    public function validate(): bool
    {
        $required = $this->type->getRequiredConfig();

        foreach ($required as $key) {
            if (! $this->hasCredential($key)) {
                return false;
            }
        }

        return true;
    }

    public function getMissingCredentials(): array
    {
        $required = $this->type->getRequiredConfig();
        $missing = [];

        foreach ($required as $key) {
            if (! $this->hasCredential($key)) {
                $missing[] = $key;
            }
        }

        return $missing;
    }
}
