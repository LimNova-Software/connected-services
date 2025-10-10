<?php

declare(strict_types=1);

namespace LimNova\ApiManager\ValueObjects;

use LimNova\ApiManager\Enums\HttpMethod;

readonly class ApiRequest
{
    public function __construct(
        public HttpMethod $method,
        public string $url,
        public array $headers = [],
        public array $data = [],
        public array $query = [],
        public int $timeout = 30,
        public int $retries = 3
    ) {}

    public function withHeader(string $name, string $value): self
    {
        return new self(
            method: $this->method,
            url: $this->url,
            headers: [...$this->headers, $name => $value],
            data: $this->data,
            query: $this->query,
            timeout: $this->timeout,
            retries: $this->retries
        );
    }

    public function withHeaders(array $headers): self
    {
        return new self(
            method: $this->method,
            url: $this->url,
            headers: [...$this->headers, ...$headers],
            data: $this->data,
            query: $this->query,
            timeout: $this->timeout,
            retries: $this->retries
        );
    }

    public function withData(array $data): self
    {
        return new self(
            method: $this->method,
            url: $this->url,
            headers: $this->headers,
            data: [...$this->data, ...$data],
            query: $this->query,
            timeout: $this->timeout,
            retries: $this->retries
        );
    }

    public function withQuery(array $query): self
    {
        return new self(
            method: $this->method,
            url: $this->url,
            headers: $this->headers,
            data: $this->data,
            query: [...$this->query, ...$query],
            timeout: $this->timeout,
            retries: $this->retries
        );
    }

    public function getFullUrl(): string
    {
        if (empty($this->query)) {
            return $this->url;
        }

        return $this->url.'?'.http_build_query($this->query);
    }

    public function hasBody(): bool
    {
        return $this->method->allowsBody() && ! empty($this->data);
    }

    public function getBody(): string
    {
        if (! $this->hasBody()) {
            return '';
        }

        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }
}
