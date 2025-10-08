<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Exceptions;

use LimNova\ApiManager\ValueObjects\ApiResponse;

class HttpException extends ApiManagerException
{
    public function __construct(
        public readonly ApiResponse $response,
        string $message = 'HTTP request failed.'
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->response->statusCode;
    }

    public function getResponse(): ApiResponse
    {
        return $this->response;
    }
}
