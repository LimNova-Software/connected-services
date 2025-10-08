<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Facades;

use Illuminate\Support\Facades\Facade;
use LimNova\ApiManager\Contracts\ApiConnectorInterface;
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\ValueObjects\ApiResponse;

/**
 * @method static ApiConnectorInterface google(array $config = [])
 * @method static ApiConnectorInterface exactonline(array $config = [])
 * @method static ApiConnectorInterface connector(string $name, array $config = [])
 * @method static void register(string $name, string $connectorClass)
 * @method static bool isRegistered(string $name)
 * @method static array getRegisteredConnectors()
 * @method static ApiResponse request(string $connector, ApiRequest $request, array $config = [])
 * @method static ApiResponse get(string $connector, string $endpoint, array $headers = [], array $config = [])
 * @method static ApiResponse post(string $connector, string $endpoint, array $data = [], array $headers = [], array $config = [])
 * @method static ApiResponse put(string $connector, string $endpoint, array $data = [], array $headers = [], array $config = [])
 * @method static ApiResponse patch(string $connector, string $endpoint, array $data = [], array $headers = [], array $config = [])
 * @method static ApiResponse delete(string $connector, string $endpoint, array $headers = [], array $config = [])
 * @method static void clearCache()
 */
final class ApiManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LimNova\ApiManager\ApiManager::class;
    }
}
