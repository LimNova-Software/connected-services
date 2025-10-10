<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\ValueObjects;

use LimNova\ApiManager\Tests\TestCase;
use LimNova\ApiManager\ValueObjects\ApiResponse;

final class ApiResponseTest extends TestCase
{
    public function test_constructor(): void
    {
        $response = new ApiResponse(
            statusCode: 200,
            headers: ['Content-Type' => 'application/json'],
            body: '{"message": "success"}',
            data: ['message' => 'success'],
            success: true
        );

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(['Content-Type' => 'application/json'], $response->headers);
        $this->assertSame('{"message": "success"}', $response->body);
        $this->assertSame(['message' => 'success'], $response->data);
        $this->assertTrue($response->success);
    }

    public function test_is_success(): void
    {
        $successResponse = new ApiResponse(200, success: true);
        $this->assertTrue($successResponse->isSuccess());

        $errorResponse = new ApiResponse(400, success: false);
        $this->assertFalse($errorResponse->isSuccess());
    }

    public function test_is_error(): void
    {
        $successResponse = new ApiResponse(200, success: true);
        $this->assertFalse($successResponse->isError());

        $errorResponse = new ApiResponse(400, success: false);
        $this->assertTrue($errorResponse->isError());
    }

    public function test_get_header(): void
    {
        $response = new ApiResponse(
            200,
            ['Content-Type' => 'application/json', 'X-Rate-Limit' => '100']
        );

        $this->assertSame('application/json', $response->getHeader('Content-Type'));
        $this->assertSame('100', $response->getHeader('X-Rate-Limit'));
        $this->assertNull($response->getHeader('Non-Existent'));
    }

    public function test_has_header(): void
    {
        $response = new ApiResponse(200, ['Content-Type' => 'application/json']);

        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertFalse($response->hasHeader('Non-Existent'));
    }

    public function test_get_data(): void
    {
        $response = new ApiResponse(
            200,
            body: '{"user": {"name": "John", "age": 30}}',
            data: ['user' => ['name' => 'John', 'age' => 30]]
        );

        $this->assertSame(['user' => ['name' => 'John', 'age' => 30]], $response->getData());
        $this->assertSame(['name' => 'John', 'age' => 30], $response->getData('user'));
        $this->assertNull($response->getData('non-existent'));
    }

    public function test_has_data(): void
    {
        $response = new ApiResponse(200, body: '{"user": {"name": "John"}}', data: ['user' => ['name' => 'John']]);

        $this->assertTrue($response->hasData('user'));
        $this->assertFalse($response->hasData('non-existent'));
    }

    public function test_get_error_message(): void
    {
        $response = new ApiResponse(400, error: 'Bad Request');
        $this->assertSame('Bad Request', $response->getErrorMessage());

        $responseWithErrorData = new ApiResponse(
            400,
            body: '{"error": {"message": "Validation failed"}}',
            data: ['error' => ['message' => 'Validation failed']]
        );
        $this->assertSame('Validation failed', $responseWithErrorData->getErrorMessage());

        $responseWithMessage = new ApiResponse(
            400,
            body: '{"message": "Something went wrong"}',
            data: ['message' => 'Something went wrong']
        );
        $this->assertSame('Something went wrong', $responseWithMessage->getErrorMessage());
    }

    public function test_to_array(): void
    {
        $response = new ApiResponse(
            200,
            ['Content-Type' => 'application/json'],
            '{"message": "success"}',
            ['message' => 'success'],
            true,
            null
        );

        $expected = [
            'status_code' => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => '{"message": "success"}',
            'data' => ['message' => 'success'],
            'success' => true,
            'error' => null,
        ];

        $this->assertSame($expected, $response->toArray());
    }

    public function test_parse_data_from_json(): void
    {
        $response = new ApiResponse(
            200,
            body: '{"users": [{"id": 1, "name": "John"}]}',
            data: ['users' => [['id' => 1, 'name' => 'John']]]
        );

        $this->assertSame(['users' => [['id' => 1, 'name' => 'John']]], $response->data);
    }

    public function test_parse_data_from_invalid_json(): void
    {
        $response = new ApiResponse(200, body: 'invalid json');

        $this->assertSame([], $response->data);
    }
}
