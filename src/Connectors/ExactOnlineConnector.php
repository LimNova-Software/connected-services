<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Connectors;

use LimNova\ApiManager\Enums\HttpMethod;
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

final class ExactOnlineConnector extends BaseConnector
{
    private ?string $accessToken = null;

    private ?string $division = null;

    protected function performAuthentication(): bool
    {
        $clientId = $this->config->getCredential('client_id');
        $clientSecret = $this->config->getCredential('client_secret');
        $this->division = $this->config->getCredential('division');

        if (! $clientId || ! $clientSecret || ! $this->division) {
            return false;
        }

        return $this->getAccessToken($clientId, $clientSecret);
    }

    private function getAccessToken(string $clientId, string $clientSecret): bool
    {
        $response = $this->httpClient->send(new ApiRequest(
            method: HttpMethod::POST,
            url: 'https://start.exactonline.nl/api/oauth2/token',
            headers: [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            data: [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]
        ));

        if ($response->isSuccess()) {
            $this->accessToken = $response->getData('access_token');

            return true;
        }

        return false;
    }

    public function request(ApiRequest $request): ApiResponse
    {
        if (! $this->isAuthenticated()) {
            $this->authenticate();
        }

        if ($this->accessToken) {
            $request = $request->withHeader('Authorization', 'Bearer '.$this->accessToken);
        }

        if ($this->division) {
            $request = $request->withHeader('X-Exact-Online-Division', $this->division);
        }

        return $this->httpClient->send($request);
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function getCurrentAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getAuthUrl(?string $redirectUri = null): string
    {
        $clientId = $this->config->getCredential('client_id');
        $redirectUri = $redirectUri ?? $this->config->getCredential('redirect_uri') ?? 'urn:ietf:wg:oauth:2.0:oob';

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
        ];

        return 'https://start.exactonline.nl/api/oauth2/auth?'.http_build_query($params);
    }
}
