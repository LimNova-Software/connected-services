<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Integration;

use LimNova\ApiManager\ApiManager;
use LimNova\ApiManager\Enums\HttpMethod;
use LimNova\ApiManager\Tests\TestCase;
use LimNova\ApiManager\ValueObjects\ApiRequest;

final class ApiManagerIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ApiManager::clearCache();
    }

    public function test_google_connector_integration(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
                'refresh_token' => 'test-refresh-token',
            ],
        ];

        $connector = ApiManager::google($config);

        $this->assertFalse($connector->isAuthenticated());
        $this->assertSame('https://www.googleapis.com', $connector->getBaseUrl());
    }

    public function test_exact_online_connector_integration(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
                'division' => '123456',
            ],
        ];

        $connector = ApiManager::exactonline($config);

        $this->assertFalse($connector->isAuthenticated());
        $this->assertSame('https://start.exactonline.nl', $connector->getBaseUrl());
    }

    public function test_request_method(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $request = new ApiRequest(
            HttpMethod::GET,
            'https://api.example.com/test'
        );

        $this->expectException(\Exception::class);

        ApiManager::request('google', $request, $config);
    }

    public function test_http_methods(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $this->expectException(\Exception::class);

        ApiManager::get('google', '/test', [], $config);
    }

    public function test_post_method(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $this->expectException(\Exception::class);

        ApiManager::post('google', '/test', ['data' => 'value'], [], $config);
    }

    public function test_put_method(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $this->expectException(\Exception::class);

        ApiManager::put('google', '/test', ['data' => 'value'], [], $config);
    }

    public function test_patch_method(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $this->expectException(\Exception::class);

        ApiManager::patch('google', '/test', ['data' => 'value'], [], $config);
    }

    public function test_delete_method(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $this->expectException(\Exception::class);

        ApiManager::delete('google', '/test', [], $config);
    }
}
