<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Exceptions;

class ValidationException extends ApiManagerException
{
    public function __construct(
        public readonly array $errors,
        string $message = 'Validation failed.'
    ) {
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }
}
