<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Enums;

enum ConnectorType: string
{
    case GOOGLE = 'google';
    case EXACT_ONLINE = 'exactonline';
    case SALESFORCE = 'salesforce';
    case HUBSPOT = 'hubspot';
    case ZAPIER = 'zapier';

    public function getDefaultBaseUrl(): string
    {
        return match ($this) {
            self::GOOGLE => 'https://www.googleapis.com',
            self::EXACT_ONLINE => 'https://start.exactonline.nl',
            self::SALESFORCE => 'https://api.salesforce.com',
            self::HUBSPOT => 'https://api.hubapi.com',
            self::ZAPIER => 'https://hooks.zapier.com',
        };
    }

    public function getRequiredConfig(): array
    {
        return match ($this) {
            self::GOOGLE => ['client_id', 'client_secret'],
            self::EXACT_ONLINE => ['client_id', 'client_secret', 'division'],
            self::SALESFORCE => ['client_id', 'client_secret', 'username', 'password'],
            self::HUBSPOT => ['access_token'],
            self::ZAPIER => ['webhook_url'],
        };
    }
}
