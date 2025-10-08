<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\Enums;

use LimNova\ApiManager\Enums\HttpStatusCode;
use LimNova\ApiManager\Tests\TestCase;

final class HttpStatusCodeTest extends TestCase
{
    public function test_is_success(): void
    {
        $this->assertTrue(HttpStatusCode::OK->isSuccess());
        $this->assertTrue(HttpStatusCode::CREATED->isSuccess());
        $this->assertTrue(HttpStatusCode::ACCEPTED->isSuccess());
        $this->assertTrue(HttpStatusCode::NO_CONTENT->isSuccess());
        $this->assertFalse(HttpStatusCode::BAD_REQUEST->isSuccess());
        $this->assertFalse(HttpStatusCode::UNAUTHORIZED->isSuccess());
        $this->assertFalse(HttpStatusCode::INTERNAL_SERVER_ERROR->isSuccess());
    }

    public function test_is_client_error(): void
    {
        $this->assertFalse(HttpStatusCode::OK->isClientError());
        $this->assertTrue(HttpStatusCode::BAD_REQUEST->isClientError());
        $this->assertTrue(HttpStatusCode::UNAUTHORIZED->isClientError());
        $this->assertTrue(HttpStatusCode::FORBIDDEN->isClientError());
        $this->assertTrue(HttpStatusCode::NOT_FOUND->isClientError());
        $this->assertFalse(HttpStatusCode::INTERNAL_SERVER_ERROR->isClientError());
    }

    public function test_is_server_error(): void
    {
        $this->assertFalse(HttpStatusCode::OK->isServerError());
        $this->assertFalse(HttpStatusCode::BAD_REQUEST->isServerError());
        $this->assertTrue(HttpStatusCode::INTERNAL_SERVER_ERROR->isServerError());
        $this->assertTrue(HttpStatusCode::BAD_GATEWAY->isServerError());
        $this->assertTrue(HttpStatusCode::SERVICE_UNAVAILABLE->isServerError());
    }

    public function test_is_error(): void
    {
        $this->assertFalse(HttpStatusCode::OK->isError());
        $this->assertTrue(HttpStatusCode::BAD_REQUEST->isError());
        $this->assertTrue(HttpStatusCode::UNAUTHORIZED->isError());
        $this->assertTrue(HttpStatusCode::INTERNAL_SERVER_ERROR->isError());
    }

    public function test_get_message(): void
    {
        $this->assertSame('OK', HttpStatusCode::OK->getMessage());
        $this->assertSame('Created', HttpStatusCode::CREATED->getMessage());
        $this->assertSame('Bad Request', HttpStatusCode::BAD_REQUEST->getMessage());
        $this->assertSame('Unauthorized', HttpStatusCode::UNAUTHORIZED->getMessage());
        $this->assertSame('Internal Server Error', HttpStatusCode::INTERNAL_SERVER_ERROR->getMessage());
    }
}
