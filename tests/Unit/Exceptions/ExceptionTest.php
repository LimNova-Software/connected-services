<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\Exceptions;

use LimNova\ApiManager\Exceptions\ApiManagerException;
use LimNova\ApiManager\Exceptions\AuthenticationException;
use LimNova\ApiManager\Exceptions\ConnectorNotFoundException;
use LimNova\ApiManager\Exceptions\HttpException;
use LimNova\ApiManager\Exceptions\InvalidConfigurationException;
use LimNova\ApiManager\Exceptions\ValidationException;
use LimNova\ApiManager\ValueObjects\ApiResponse;
use LimNova\ApiManager\Tests\TestCase;

final class ExceptionTest extends TestCase
{
    public function testApiManagerException(): void
    {
        $exception = new AuthenticationException('Test message');

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertInstanceOf(ApiManagerException::class, $exception);
    }

    public function testAuthenticationException(): void
    {
        $exception = new AuthenticationException();

        $this->assertSame('Authentication failed.', $exception->getMessage());
        $this->assertInstanceOf(ApiManagerException::class, $exception);
    }

    public function testAuthenticationExceptionWithMessage(): void
    {
        $exception = new AuthenticationException('Custom auth message');

        $this->assertSame('Custom auth message', $exception->getMessage());
    }

    public function testConnectorNotFoundException(): void
    {
        $exception = new ConnectorNotFoundException('test-connector');

        $this->assertSame("Connector 'test-connector' not found or not registered.", $exception->getMessage());
        $this->assertInstanceOf(ApiManagerException::class, $exception);
    }

    public function testInvalidConfigurationException(): void
    {
        $exception = new InvalidConfigurationException();

        $this->assertSame('Invalid configuration provided.', $exception->getMessage());
        $this->assertInstanceOf(ApiManagerException::class, $exception);
    }

    public function testInvalidConfigurationExceptionWithMessage(): void
    {
        $exception = new InvalidConfigurationException('Custom config message');

        $this->assertSame('Custom config message', $exception->getMessage());
    }

    public function testHttpException(): void
    {
        $response = new ApiResponse(400, body: 'Error response');
        $exception = new HttpException($response);

        $this->assertSame('HTTP request failed.', $exception->getMessage());
        $this->assertSame(400, $exception->getStatusCode());
        $this->assertSame($response, $exception->getResponse());
        $this->assertInstanceOf(ApiManagerException::class, $exception);
    }

    public function testHttpExceptionWithMessage(): void
    {
        $response = new ApiResponse(500, body: 'Server error');
        $exception = new HttpException($response, 'Custom HTTP error');

        $this->assertSame('Custom HTTP error', $exception->getMessage());
        $this->assertSame(500, $exception->getStatusCode());
    }

    public function testValidationException(): void
    {
        $errors = ['field1' => 'Error 1', 'field2' => 'Error 2'];
        $exception = new ValidationException($errors);

        $this->assertSame('Validation failed.', $exception->getMessage());
        $this->assertSame($errors, $exception->getErrors());
        $this->assertTrue($exception->hasErrors());
        $this->assertInstanceOf(ApiManagerException::class, $exception);
    }

    public function testValidationExceptionWithMessage(): void
    {
        $errors = ['field1' => 'Error 1'];
        $exception = new ValidationException($errors, 'Custom validation message');

        $this->assertSame('Custom validation message', $exception->getMessage());
        $this->assertSame($errors, $exception->getErrors());
    }

    public function testValidationExceptionWithEmptyErrors(): void
    {
        $exception = new ValidationException([]);

        $this->assertFalse($exception->hasErrors());
    }
}
