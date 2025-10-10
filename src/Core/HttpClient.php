<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LimNova\ApiManager\Contracts\HttpClientInterface;
use LimNova\ApiManager\Exceptions\HttpException;
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

final class HttpClient implements HttpClientInterface
{
    private Client $client;

    public function __construct(
        private string $baseUrl = '',
        private array $defaultHeaders = [],
        private int $timeout = 30,
        private int $retries = 3
    ) {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'headers' => $this->defaultHeaders,
        ]);
    }

    public function send(ApiRequest $request): ApiResponse
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->retries) {
            try {
                $response = $this->client->request(
                    $request->method->value,
                    $request->getFullUrl(),
                    [
                        'headers' => $request->headers,
                        'json' => $request->hasBody() ? $request->data : null,
                        'query' => $request->query,
                        'timeout' => $request->timeout,
                    ]
                );

                $statusCode = $response->getStatusCode();
                $body = (string) $response->getBody();
                $data = $this->parseJsonData($body);

                return new ApiResponse(
                    statusCode: $statusCode,
                    headers: $response->getHeaders(),
                    body: $body,
                    data: $data,
                    success: $statusCode >= 200 && $statusCode < 300
                );
            } catch (GuzzleException $e) {
                $lastException = $e;
                $attempts++;

                if ($attempts >= $this->retries) {
                    break;
                }

                usleep(100000 * $attempts);
            }
        }

        throw new HttpException(
            response: new ApiResponse(
                statusCode: 0,
                body: '',
                success: false,
                error: $lastException?->getMessage() ?? 'Unknown error'
            ),
            message: 'HTTP request failed after '.$this->retries.' attempts: '.($lastException?->getMessage() ?? 'Unknown error')
        );
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => $this->timeout,
            'headers' => $this->defaultHeaders,
        ]);
    }

    public function setDefaultHeaders(array $headers): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'headers' => $headers,
        ]);
    }

    public function setTimeout(int $timeout): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $timeout,
            'headers' => $this->defaultHeaders,
        ]);
    }

    public function setRetries(int $retries): void
    {
        // This would require recreating the client with new retry settings
        // For now, we'll store it as a property for future use
    }

    private function parseJsonData(string $body): array
    {
        if (empty($body)) {
            return [];
        }

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }
}
