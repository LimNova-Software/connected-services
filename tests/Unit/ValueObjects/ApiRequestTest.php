<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\ValueObjects;

use LimNova\ApiManager\Enums\HttpMethod;
use LimNova\ApiManager\Tests\TestCase;
use LimNova\ApiManager\ValueObjects\ApiRequest;

final class ApiRequestTest extends TestCase
{
    public function test_constructor(): void
    {
        $request = new ApiRequest(
            method: HttpMethod::GET,
            url: 'https://api.example.com/test',
            headers: ['Authorization' => 'Bearer token'],
            data: ['key' => 'value'],
            query: ['param' => 'value'],
            timeout: 60,
            retries: 5
        );

        $this->assertSame(HttpMethod::GET, $request->method);
        $this->assertSame('https://api.example.com/test', $request->url);
        $this->assertSame(['Authorization' => 'Bearer token'], $request->headers);
        $this->assertSame(['key' => 'value'], $request->data);
        $this->assertSame(['param' => 'value'], $request->query);
        $this->assertSame(60, $request->timeout);
        $this->assertSame(5, $request->retries);
    }

    public function test_with_header(): void
    {
        $request = new ApiRequest(HttpMethod::GET, 'https://api.example.com/test');
        $newRequest = $request->withHeader('Content-Type', 'application/json');

        $this->assertSame(['Content-Type' => 'application/json'], $newRequest->headers);
        $this->assertNotSame($request, $newRequest);
    }

    public function test_with_headers(): void
    {
        $request = new ApiRequest(HttpMethod::GET, 'https://api.example.com/test');
        $newRequest = $request->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        $this->assertSame([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $newRequest->headers);
    }

    public function test_with_data(): void
    {
        $request = new ApiRequest(HttpMethod::POST, 'https://api.example.com/test');
        $newRequest = $request->withData(['name' => 'John']);

        $this->assertSame(['name' => 'John'], $newRequest->data);
    }

    public function test_with_query(): void
    {
        $request = new ApiRequest(HttpMethod::GET, 'https://api.example.com/test');
        $newRequest = $request->withQuery(['page' => 1]);

        $this->assertSame(['page' => 1], $newRequest->query);
    }

    public function test_get_full_url(): void
    {
        $request = new ApiRequest(
            HttpMethod::GET,
            'https://api.example.com/test',
            query: ['page' => 1, 'limit' => 10]
        );

        $this->assertSame('https://api.example.com/test?page=1&limit=10', $request->getFullUrl());
    }

    public function test_get_full_url_without_query(): void
    {
        $request = new ApiRequest(HttpMethod::GET, 'https://api.example.com/test');

        $this->assertSame('https://api.example.com/test', $request->getFullUrl());
    }

    public function test_has_body(): void
    {
        $getRequest = new ApiRequest(HttpMethod::GET, 'https://api.example.com/test');
        $this->assertFalse($getRequest->hasBody());

        $postRequest = new ApiRequest(
            HttpMethod::POST,
            'https://api.example.com/test',
            data: ['key' => 'value']
        );
        $this->assertTrue($postRequest->hasBody());

        $postRequestEmpty = new ApiRequest(HttpMethod::POST, 'https://api.example.com/test');
        $this->assertFalse($postRequestEmpty->hasBody());
    }

    public function test_get_body(): void
    {
        $request = new ApiRequest(
            HttpMethod::POST,
            'https://api.example.com/test',
            data: ['name' => 'John', 'age' => 30]
        );

        $expected = json_encode(['name' => 'John', 'age' => 30], JSON_THROW_ON_ERROR);
        $this->assertSame($expected, $request->getBody());
    }

    public function test_get_body_empty(): void
    {
        $request = new ApiRequest(HttpMethod::GET, 'https://api.example.com/test');

        $this->assertSame('', $request->getBody());
    }
}
