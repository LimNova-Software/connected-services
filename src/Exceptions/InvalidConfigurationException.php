<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Exceptions;

class InvalidConfigurationException extends ApiManagerException
{
    public function __construct(string $message = 'Invalid configuration provided.')
    {
        parent::__construct($message);
    }
}
