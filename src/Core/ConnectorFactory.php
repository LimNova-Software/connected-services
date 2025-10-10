<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Core;

use LimNova\ApiManager\Connectors\ExactOnlineConnector;
use LimNova\ApiManager\Connectors\GoogleConnector;
use LimNova\ApiManager\Contracts\ApiConnectorInterface;
use LimNova\ApiManager\Contracts\FactoryInterface;
use LimNova\ApiManager\Enums\ConnectorType;
use LimNova\ApiManager\Exceptions\ConnectorNotFoundException;
use LimNova\ApiManager\ValueObjects\ConnectorConfig;

final class ConnectorFactory implements FactoryInterface
{
    private array $connectors = [];

    public function __construct()
    {
        $this->registerDefaultConnectors();
    }

    public function create(string $connector, array $config = []): ApiConnectorInterface
    {
        if (! $this->isRegistered($connector)) {
            throw new ConnectorNotFoundException($connector);
        }

        $connectorClass = $this->connectors[$connector];
        $connectorConfig = $this->createConfig($connector, $config);

        /** @var ApiConnectorInterface $connectorInstance */
        $connectorInstance = new $connectorClass($connectorConfig);

        return $connectorInstance;
    }

    public function register(string $name, string $connectorClass): void
    {
        if (! is_subclass_of($connectorClass, ApiConnectorInterface::class)) {
            throw new \InvalidArgumentException('Connector class must implement ApiConnectorInterface');
        }

        $this->connectors[$name] = $connectorClass;
    }

    public function isRegistered(string $name): bool
    {
        return array_key_exists($name, $this->connectors);
    }

    public function getRegisteredConnectors(): array
    {
        return array_keys($this->connectors);
    }

    private function registerDefaultConnectors(): void
    {
        $this->connectors = [
            'google' => GoogleConnector::class,
            'exactonline' => ExactOnlineConnector::class,
        ];
    }

    private function createConfig(string $connector, array $config): ConnectorConfig
    {
        $type = ConnectorType::from($connector);
        $baseUrl = $config['base_url'] ?? $type->getDefaultBaseUrl();

        return new ConnectorConfig(
            type: $type,
            baseUrl: $baseUrl,
            credentials: $config['credentials'] ?? [],
            options: $config['options'] ?? [],
            timeout: $config['timeout'] ?? 30,
            retries: $config['retries'] ?? 3,
            defaultHeaders: $config['default_headers'] ?? []
        );
    }
}
