<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\Core;

use LimNova\ApiManager\Core\ConnectorFactory;
use LimNova\ApiManager\Exceptions\ConnectorNotFoundException;
use LimNova\ApiManager\Tests\TestCase;

final class ConnectorFactoryTest extends TestCase
{
    private ConnectorFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new ConnectorFactory();
    }

    public function testCreateGoogleConnector(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ]
        ];

        $connector = $this->factory->create('google', $config);

        $this->assertInstanceOf(\LimNova\ApiManager\Connectors\GoogleConnector::class, $connector);
    }

    public function testCreateExactOnlineConnector(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
                'division' => '123456',
            ]
        ];

        $connector = $this->factory->create('exactonline', $config);

        $this->assertInstanceOf(\LimNova\ApiManager\Connectors\ExactOnlineConnector::class, $connector);
    }

    public function testCreateWithInvalidConnector(): void
    {
        $this->expectException(ConnectorNotFoundException::class);
        $this->expectExceptionMessage("Connector 'invalid' not found or not registered.");

        $this->factory->create('invalid', []);
    }

    public function testRegisterConnector(): void
    {
        $this->factory->register('test', \LimNova\ApiManager\Connectors\GoogleConnector::class);

        $this->assertTrue($this->factory->isRegistered('test'));
    }

    public function testRegisterInvalidConnector(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Connector class must implement ApiConnectorInterface");

        $this->factory->register('test', \stdClass::class);
    }

    public function testIsRegistered(): void
    {
        $this->assertTrue($this->factory->isRegistered('google'));
        $this->assertTrue($this->factory->isRegistered('exactonline'));
        $this->assertFalse($this->factory->isRegistered('non-existent'));
    }

    public function testGetRegisteredConnectors(): void
    {
        $connectors = $this->factory->getRegisteredConnectors();

        $this->assertContains('google', $connectors);
        $this->assertContains('exactonline', $connectors);
    }

    public function testCreateWithCustomBaseUrl(): void
    {
        $config = [
            'base_url' => 'https://custom.api.com',
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ]
        ];

        $connector = $this->factory->create('google', $config);

        $this->assertSame('https://custom.api.com', $connector->getBaseUrl());
    }

    public function testCreateWithOptions(): void
    {
        $config = [
            'credentials' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
            ],
            'options' => [
                'timeout' => 60,
                'retries' => 5,
            ]
        ];

        $connector = $this->factory->create('google', $config);

        $this->assertInstanceOf(\LimNova\ApiManager\Connectors\GoogleConnector::class, $connector);
    }
}
