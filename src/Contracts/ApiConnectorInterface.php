<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Contracts;

use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

interface ApiConnectorInterface
{
    public function get(string $endpoint, array $headers = []): ApiResponse;

    public function post(string $endpoint, array $data = [], array $headers = []): ApiResponse;

    public function put(string $endpoint, array $data = [], array $headers = []): ApiResponse;

    public function patch(string $endpoint, array $data = [], array $headers = []): ApiResponse;

    public function delete(string $endpoint, array $headers = []): ApiResponse;

    public function request(ApiRequest $request): ApiResponse;

    public function authenticate(): bool;

    public function isAuthenticated(): bool;

    public function getBaseUrl(): string;

    public function getConfig(): array;
}
