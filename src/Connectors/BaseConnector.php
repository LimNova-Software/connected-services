<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Connectors;

use LimNova\ApiManager\Contracts\ApiConnectorInterface;
use LimNova\ApiManager\Contracts\ConfigurableInterface;
use LimNova\ApiManager\Core\HttpClient;
use LimNova\ApiManager\Enums\HttpMethod;
use LimNova\ApiManager\Exceptions\AuthenticationException;
use LimNova\ApiManager\Exceptions\InvalidConfigurationException;
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;
use LimNova\ApiManager\ValueObjects\ConnectorConfig;

abstract class BaseConnector implements ApiConnectorInterface, ConfigurableInterface
{
    protected HttpClient $httpClient;

    protected bool $authenticated = false;

    public function __construct(protected ConnectorConfig $config)
    {
        $this->validateConfig();
        $this->httpClient = new HttpClient(
            baseUrl: $this->config->baseUrl,
            defaultHeaders: $this->config->defaultHeaders,
            timeout: $this->config->timeout,
            retries: $this->config->retries
        );
    }

    public function get(string $endpoint, array $headers = []): ApiResponse
    {
        return $this->request(new ApiRequest(
            method: HttpMethod::GET,
            url: $this->buildUrl($endpoint),
            headers: $headers
        ));
    }

    public function post(string $endpoint, array $data = [], array $headers = []): ApiResponse
    {
        return $this->request(new ApiRequest(
            method: HttpMethod::POST,
            url: $this->buildUrl($endpoint),
            headers: $headers,
            data: $data
        ));
    }

    public function put(string $endpoint, array $data = [], array $headers = []): ApiResponse
    {
        return $this->request(new ApiRequest(
            method: HttpMethod::PUT,
            url: $this->buildUrl($endpoint),
            headers: $headers,
            data: $data
        ));
    }

    public function patch(string $endpoint, array $data = [], array $headers = []): ApiResponse
    {
        return $this->request(new ApiRequest(
            method: HttpMethod::PATCH,
            url: $this->buildUrl($endpoint),
            headers: $headers,
            data: $data
        ));
    }

    public function delete(string $endpoint, array $headers = []): ApiResponse
    {
        return $this->request(new ApiRequest(
            method: HttpMethod::DELETE,
            url: $this->buildUrl($endpoint),
            headers: $headers
        ));
    }

    public function request(ApiRequest $request): ApiResponse
    {
        if (! $this->isAuthenticated()) {
            $this->authenticate();
        }

        return $this->httpClient->send($request);
    }

    public function authenticate(): bool
    {
        try {
            $this->authenticated = $this->performAuthentication();

            return $this->authenticated;
        } catch (\Exception $e) {
            throw new AuthenticationException('Authentication failed: '.$e->getMessage());
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function getBaseUrl(): string
    {
        return $this->config->baseUrl;
    }

    public function getConfig(): array
    {
        return [
            'type' => $this->config->type->value,
            'base_url' => $this->config->baseUrl,
            'credentials' => $this->config->credentials,
            'options' => $this->config->options,
            'timeout' => $this->config->timeout,
            'retries' => $this->config->retries,
            'default_headers' => $this->config->defaultHeaders,
        ];
    }

    public function setConfig(array $config): void
    {
        // Read-only implementation - config cannot be changed after instantiation
    }

    public function hasConfig(string $key): bool
    {
        return $this->config->hasCredential($key) || $this->config->hasOption($key);
    }

    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->config->getCredential($key) ?? $this->config->getOption($key) ?? $default;
    }

    protected function buildUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->config->baseUrl, '/');
        $endpoint = ltrim($endpoint, '/');

        return $baseUrl.'/'.$endpoint;
    }

    protected function validateConfig(): void
    {
        if (! $this->config->validate()) {
            $missing = $this->config->getMissingCredentials();
            throw new InvalidConfigurationException(
                'Missing required configuration: '.implode(', ', $missing)
            );
        }
    }

    abstract protected function performAuthentication(): bool;
}
