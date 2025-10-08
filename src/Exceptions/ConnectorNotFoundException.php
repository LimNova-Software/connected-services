<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Exceptions;

class ConnectorNotFoundException extends ApiManagerException
{
    public function __construct(string $connector)
    {
        parent::__construct("Connector '{$connector}' not found or not registered.");
    }
}
