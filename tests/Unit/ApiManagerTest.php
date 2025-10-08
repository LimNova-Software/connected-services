<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit;

use LimNova\ApiManager\ApiManager;
use LimNova\ApiManager\Core\ConnectorFactory;
use LimNova\ApiManager\Exceptions\ConnectorNotFoundException;
use LimNova\ApiManager\Tests\TestCase;

final class ApiManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ApiManager::clearCache();
    }

    public function test_google_connector(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $connector = ApiManager::google($config);

        $this->assertInstanceOf(\LimNova\ApiManager\Connectors\GoogleConnector::class, $connector);
    }

    public function test_exact_online_connector(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
                'division' => '123456',
            ],
        ];

        $connector = ApiManager::exactonline($config);

        $this->assertInstanceOf(\LimNova\ApiManager\Connectors\ExactOnlineConnector::class, $connector);
    }

    public function test_connector_method(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $connector = ApiManager::connector('google', $config);

        $this->assertInstanceOf(\LimNova\ApiManager\Connectors\GoogleConnector::class, $connector);
    }

    public function test_connector_not_found(): void
    {
        $this->expectException(ConnectorNotFoundException::class);
        $this->expectExceptionMessage("Connector 'nonexistent' not found");

        ApiManager::connector('nonexistent', []);
    }

    public function test_register_connector(): void
    {
        ApiManager::register('test', \LimNova\ApiManager\Connectors\GoogleConnector::class);

        $this->assertTrue(ApiManager::isRegistered('test'));
    }

    public function test_is_registered(): void
    {
        $this->assertTrue(ApiManager::isRegistered('google'));
        $this->assertTrue(ApiManager::isRegistered('exactonline'));
        $this->assertFalse(ApiManager::isRegistered('nonexistent'));
    }

    public function test_get_registered_connectors(): void
    {
        $connectors = ApiManager::getRegisteredConnectors();

        $this->assertContains('google', $connectors);
        $this->assertContains('exactonline', $connectors);
    }

    public function test_clear_cache(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
        ];

        $connector1 = ApiManager::google($config);
        $connector2 = ApiManager::google($config);

        $this->assertSame($connector1, $connector2);

        ApiManager::clearCache();

        $connector3 = ApiManager::google($config);

        $this->assertNotSame($connector1, $connector3);
    }

    public function test_set_factory(): void
    {
        $factory = new ConnectorFactory;
        ApiManager::setFactory($factory);

        $reflection = new \ReflectionClass(ApiManager::class);
        $property = $reflection->getProperty('factory');
        $property->setAccessible(true);
        $actualFactory = $property->getValue();

        $this->assertSame($factory, $actualFactory);
    }
}
