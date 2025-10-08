<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\Enums;

use LimNova\ApiManager\Enums\HttpMethod;
use LimNova\ApiManager\Tests\TestCase;

final class HttpMethodTest extends TestCase
{
    public function test_is_read_only(): void
    {
        $this->assertTrue(HttpMethod::GET->isReadOnly());
        $this->assertTrue(HttpMethod::HEAD->isReadOnly());
        $this->assertTrue(HttpMethod::OPTIONS->isReadOnly());
        $this->assertFalse(HttpMethod::POST->isReadOnly());
        $this->assertFalse(HttpMethod::PUT->isReadOnly());
        $this->assertFalse(HttpMethod::PATCH->isReadOnly());
        $this->assertFalse(HttpMethod::DELETE->isReadOnly());
    }

    public function test_allows_body(): void
    {
        $this->assertFalse(HttpMethod::GET->allowsBody());
        $this->assertFalse(HttpMethod::HEAD->allowsBody());
        $this->assertFalse(HttpMethod::OPTIONS->allowsBody());
        $this->assertFalse(HttpMethod::DELETE->allowsBody());
        $this->assertTrue(HttpMethod::POST->allowsBody());
        $this->assertTrue(HttpMethod::PUT->allowsBody());
        $this->assertTrue(HttpMethod::PATCH->allowsBody());
    }

    public function test_values(): void
    {
        $this->assertSame('GET', HttpMethod::GET->value);
        $this->assertSame('POST', HttpMethod::POST->value);
        $this->assertSame('PUT', HttpMethod::PUT->value);
        $this->assertSame('PATCH', HttpMethod::PATCH->value);
        $this->assertSame('DELETE', HttpMethod::DELETE->value);
        $this->assertSame('HEAD', HttpMethod::HEAD->value);
        $this->assertSame('OPTIONS', HttpMethod::OPTIONS->value);
    }
}
