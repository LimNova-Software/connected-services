<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\ValueObjects;

use LimNova\ApiManager\Enums\ConnectorType;
use LimNova\ApiManager\ValueObjects\ConnectorConfig;
use LimNova\ApiManager\Tests\TestCase;

final class ConnectorConfigTest extends TestCase
{
    public function testConstructor(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id'],
            options: ['timeout' => 60],
            timeout: 30,
            retries: 3,
            defaultHeaders: ['Accept' => 'application/json']
        );

        $this->assertSame(ConnectorType::GOOGLE, $config->type);
        $this->assertSame('https://api.example.com', $config->baseUrl);
        $this->assertSame(['client_id' => 'test-id'], $config->credentials);
        $this->assertSame(['timeout' => 60], $config->options);
        $this->assertSame(30, $config->timeout);
        $this->assertSame(3, $config->retries);
        $this->assertSame(['Accept' => 'application/json'], $config->defaultHeaders);
    }

    public function testGetCredential(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id', 'client_secret' => 'test-secret']
        );

        $this->assertSame('test-id', $config->getCredential('client_id'));
        $this->assertSame('test-secret', $config->getCredential('client_secret'));
        $this->assertNull($config->getCredential('non-existent'));
    }

    public function testHasCredential(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id']
        );

        $this->assertTrue($config->hasCredential('client_id'));
        $this->assertFalse($config->hasCredential('non-existent'));
    }

    public function testGetOption(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            options: ['timeout' => 60, 'retries' => 5]
        );

        $this->assertSame(60, $config->getOption('timeout'));
        $this->assertSame(5, $config->getOption('retries'));
        $this->assertNull($config->getOption('non-existent'));
    }

    public function testHasOption(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            options: ['timeout' => 60]
        );

        $this->assertTrue($config->hasOption('timeout'));
        $this->assertFalse($config->hasOption('non-existent'));
    }

    public function testGetHeader(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            defaultHeaders: ['Accept' => 'application/json', 'User-Agent' => 'ApiManager']
        );

        $this->assertSame('application/json', $config->getHeader('Accept'));
        $this->assertSame('ApiManager', $config->getHeader('User-Agent'));
        $this->assertNull($config->getHeader('non-existent'));
    }

    public function testHasHeader(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            defaultHeaders: ['Accept' => 'application/json']
        );

        $this->assertTrue($config->hasHeader('Accept'));
        $this->assertFalse($config->hasHeader('non-existent'));
    }

    public function testWithCredential(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id']
        );

        $newConfig = $config->withCredential('client_secret', 'test-secret');

        $this->assertSame('test-secret', $newConfig->getCredential('client_secret'));
        $this->assertNotSame($config, $newConfig);
    }

    public function testWithOption(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            options: ['timeout' => 30]
        );

        $newConfig = $config->withOption('retries', 5);

        $this->assertSame(5, $newConfig->getOption('retries'));
        $this->assertNotSame($config, $newConfig);
    }

    public function testWithHeader(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            defaultHeaders: ['Accept' => 'application/json']
        );

        $newConfig = $config->withHeader('User-Agent', 'ApiManager');

        $this->assertSame('ApiManager', $newConfig->getHeader('User-Agent'));
        $this->assertNotSame($config, $newConfig);
    }

    public function testValidate(): void
    {
        $validConfig = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id', 'client_secret' => 'test-secret']
        );

        $this->assertTrue($validConfig->validate());

        $invalidConfig = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id']
        );

        $this->assertFalse($invalidConfig->validate());
    }

    public function testGetMissingCredentials(): void
    {
        $config = new ConnectorConfig(
            type: ConnectorType::GOOGLE,
            baseUrl: 'https://api.example.com',
            credentials: ['client_id' => 'test-id']
        );

        $missing = $config->getMissingCredentials();
        $this->assertContains('client_secret', $missing);
        $this->assertNotContains('client_id', $missing);
    }
}
