<?php

declare(strict_types=1);

namespace LimNova\ApiManager\ValueObjects;

use LimNova\ApiManager\Enums\HttpStatusCode;

readonly class ApiResponse
{
    public function __construct(
        public int $statusCode,
        public array $headers = [],
        public string $body = '',
        public array $data = [],
        public bool $success = false,
        public ?string $error = null
    ) {
        // Properties are set via constructor parameters
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return ! $this->success;
    }

    public function getStatusCode(): HttpStatusCode
    {
        return HttpStatusCode::from($this->statusCode);
    }

    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        $headers = array_change_key_case($this->headers, CASE_LOWER);

        return $headers[$name] ?? null;
    }

    public function hasHeader(string $name): bool
    {
        return $this->getHeader($name) !== null;
    }

    public function getData(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->data;
        }

        return $this->data[$key] ?? null;
    }

    public function hasData(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function getErrorMessage(): ?string
    {
        if ($this->error) {
            return $this->error;
        }

        if ($this->isError() && isset($this->data['error']['message'])) {
            return $this->data['error']['message'];
        }

        if ($this->isError() && isset($this->data['message'])) {
            return $this->data['message'];
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode,
            'headers' => $this->headers,
            'body' => $this->body,
            'data' => $this->data,
            'success' => $this->success,
            'error' => $this->error,
        ];
    }
}
