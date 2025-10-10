<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Tests\Unit\Enums;

use LimNova\ApiManager\Enums\ConnectorType;
use LimNova\ApiManager\Tests\TestCase;

final class ConnectorTypeTest extends TestCase
{
    public function testGetDefaultBaseUrl(): void
    {
        $this->assertSame('https://www.googleapis.com', ConnectorType::GOOGLE->getDefaultBaseUrl());
        $this->assertSame('https://start.exactonline.nl', ConnectorType::EXACT_ONLINE->getDefaultBaseUrl());
        $this->assertSame('https://api.salesforce.com', ConnectorType::SALESFORCE->getDefaultBaseUrl());
        $this->assertSame('https://api.hubapi.com', ConnectorType::HUBSPOT->getDefaultBaseUrl());
        $this->assertSame('https://hooks.zapier.com', ConnectorType::ZAPIER->getDefaultBaseUrl());
    }

    public function testGetRequiredConfig(): void
    {
        $this->assertSame(['client_id', 'client_secret'], ConnectorType::GOOGLE->getRequiredConfig());
        $this->assertSame(['client_id', 'client_secret', 'division'], ConnectorType::EXACT_ONLINE->getRequiredConfig());
        $this->assertSame(['client_id', 'client_secret', 'username', 'password'], ConnectorType::SALESFORCE->getRequiredConfig());
        $this->assertSame(['access_token'], ConnectorType::HUBSPOT->getRequiredConfig());
        $this->assertSame(['webhook_url'], ConnectorType::ZAPIER->getRequiredConfig());
    }

    public function testValues(): void
    {
        $this->assertSame('google', ConnectorType::GOOGLE->value);
        $this->assertSame('exactonline', ConnectorType::EXACT_ONLINE->value);
        $this->assertSame('salesforce', ConnectorType::SALESFORCE->value);
        $this->assertSame('hubspot', ConnectorType::HUBSPOT->value);
        $this->assertSame('zapier', ConnectorType::ZAPIER->value);
    }
}
