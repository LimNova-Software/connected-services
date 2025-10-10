<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Contracts;

use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

interface HttpClientInterface
{
    public function send(ApiRequest $request): ApiResponse;

    public function setBaseUrl(string $baseUrl): void;

    public function setDefaultHeaders(array $headers): void;

    public function setTimeout(int $timeout): void;

    public function setRetries(int $retries): void;
}
