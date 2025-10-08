<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Connectors;

use LimNova\ApiManager\Enums\HttpMethod;
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

final class GoogleConnector extends BaseConnector
{
    private ?string $accessToken = null;

    protected function performAuthentication(): bool
    {
        $clientId = $this->config->getCredential('client_id');
        $clientSecret = $this->config->getCredential('client_secret');
        $refreshToken = $this->config->getCredential('refresh_token');

        if (! $clientId || ! $clientSecret) {
            return false;
        }

        if ($refreshToken) {
            return $this->refreshAccessToken($clientId, $clientSecret, $refreshToken);
        }

        return $this->getAccessToken($clientId, $clientSecret);
    }

    private function refreshAccessToken(string $clientId, string $clientSecret, string $refreshToken): bool
    {
        $response = $this->httpClient->send(new ApiRequest(
            method: HttpMethod::POST,
            url: 'https://oauth2.googleapis.com/token',
            data: [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]
        ));

        if ($response->isSuccess()) {
            $this->accessToken = $response->getData('access_token');

            return true;
        }

        return false;
    }

    private function getAccessToken(string $clientId, string $clientSecret): bool
    {
        $authCode = $this->config->getCredential('auth_code');

        if (! $authCode) {
            return false;
        }

        $response = $this->httpClient->send(new ApiRequest(
            method: HttpMethod::POST,
            url: 'https://oauth2.googleapis.com/token',
            data: [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $authCode,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->config->getCredential('redirect_uri') ?? 'urn:ietf:wg:oauth:2.0:oob',
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

        return $this->httpClient->send($request);
    }

    public function getAuthUrl(string $scope, ?string $redirectUri = null): string
    {
        $clientId = $this->config->getCredential('client_id');
        $redirectUri = $redirectUri ?? $this->config->getCredential('redirect_uri') ?? 'urn:ietf:wg:oauth:2.0:oob';

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query($params);
    }

    public function getCurrentAccessToken(): ?string
    {
        return $this->accessToken;
    }
}
