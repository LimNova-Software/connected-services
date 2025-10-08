<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Exceptions;

class AuthenticationException extends ApiManagerException
{
    public function __construct(string $message = 'Authentication failed.')
    {
        parent::__construct($message);
    }
}
