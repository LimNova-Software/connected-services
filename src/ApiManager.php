<?php

declare(strict_types=1);

namespace LimNova\ApiManager;

use LimNova\ApiManager\Contracts\ApiConnectorInterface;
use LimNova\ApiManager\Core\ConnectorFactory;
use LimNova\ApiManager\Exceptions\ConnectorNotFoundException;
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

final class ApiManager
{
    private static ?ConnectorFactory $factory = null;

    private static array $connectors = [];

    public static function google(array $config = []): ApiConnectorInterface
    {
        return self::getConnector('google', $config);
    }

    public static function exactonline(array $config = []): ApiConnectorInterface
    {
        return self::getConnector('exactonline', $config);
    }

    public static function connector(string $name, array $config = []): ApiConnectorInterface
    {
        return self::getConnector($name, $config);
    }

    public static function register(string $name, string $connectorClass): void
    {
        self::getFactory()->register($name, $connectorClass);
    }

    public static function isRegistered(string $name): bool
    {
        return self::getFactory()->isRegistered($name);
    }

    public static function getRegisteredConnectors(): array
    {
        return self::getFactory()->getRegisteredConnectors();
    }

    public static function request(string $connector, ApiRequest $request, array $config = []): ApiResponse
    {
        $connectorInstance = self::getConnector($connector, $config);

        return $connectorInstance->request($request);
    }

    public static function get(string $connector, string $endpoint, array $headers = [], array $config = []): ApiResponse
    {
        $connectorInstance = self::getConnector($connector, $config);

        return $connectorInstance->get($endpoint, $headers);
    }

    public static function post(string $connector, string $endpoint, array $data = [], array $headers = [], array $config = []): ApiResponse
    {
        $connectorInstance = self::getConnector($connector, $config);

        return $connectorInstance->post($endpoint, $data, $headers);
    }

    public static function put(string $connector, string $endpoint, array $data = [], array $headers = [], array $config = []): ApiResponse
    {
        $connectorInstance = self::getConnector($connector, $config);

        return $connectorInstance->put($endpoint, $data, $headers);
    }

    public static function patch(string $connector, string $endpoint, array $data = [], array $headers = [], array $config = []): ApiResponse
    {
        $connectorInstance = self::getConnector($connector, $config);

        return $connectorInstance->patch($endpoint, $data, $headers);
    }

    public static function delete(string $connector, string $endpoint, array $headers = [], array $config = []): ApiResponse
    {
        $connectorInstance = self::getConnector($connector, $config);

        return $connectorInstance->delete($endpoint, $headers);
    }

    public static function clearCache(): void
    {
        self::$connectors = [];
    }

    public static function setFactory(ConnectorFactory $factory): void
    {
        self::$factory = $factory;
    }

    private static function getConnector(string $name, array $config = []): ApiConnectorInterface
    {
        $cacheKey = $name.'_'.md5(serialize($config));

        if (isset(self::$connectors[$cacheKey])) {
            return self::$connectors[$cacheKey];
        }

        try {
            $connector = self::getFactory()->create($name, $config);
            self::$connectors[$cacheKey] = $connector;

            return $connector;
        } catch (ConnectorNotFoundException $e) {
            throw new ConnectorNotFoundException("Connector '{$name}' not found. Available connectors: ".implode(', ', self::getRegisteredConnectors()));
        }
    }

    private static function getFactory(): ConnectorFactory
    {
        if (self::$factory === null) {
            self::$factory = new ConnectorFactory;
        }

        return self::$factory;
    }
}
