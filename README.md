# ApiManager Package

A unified interface for various API integrations with support for Google, ExactOnline and more. Built with PHP 8.4 features, SOLID principles, and modern design patterns.

## Features

- **Unified API Interface**: Consistent interface for different API providers
- **Factory Pattern**: Easy connector instantiation and registration
- **Dependency Injection**: Full IoC container support
- **Type Safety**: PHP 8.4 features with strict typing and PHPStan level 8
- **Laravel Integration**: Service Provider, Facade, and configuration support
- **Extensible**: Easy to add new API connectors
- **Testable**: Comprehensive test suite with 90%+ coverage
- **Modern PHP**: Read-only classes, enums, union types, and more

## Installation

```bash
composer require limnova/api-manager
```

## Laravel Integration

### Service Provider Registration

Add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    LimNova\ApiManager\Providers\ApiManagerServiceProvider::class,
],
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=api-manager-config
```

### Environment Variables

Add to your `.env` file:

```env
# Google API
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REFRESH_TOKEN=your-refresh-token

# ExactOnline API
EXACT_ONLINE_CLIENT_ID=your-client-id
EXACT_ONLINE_CLIENT_SECRET=your-client-secret
EXACT_ONLINE_DIVISION=your-division
```

## Usage

### Basic Usage

```php
use LimNova\ApiManager\ApiManager;

// Google API
$google = ApiManager::google([
    'credentials' => [
        'client_id' => 'your-client-id',
        'client_secret' => 'your-client-secret',
        'refresh_token' => 'your-refresh-token',
    ]
]);

// ExactOnline API
$exactOnline = ApiManager::exactonline([
    'credentials' => [
        'client_id' => 'your-client-id',
        'client_secret' => 'your-client-secret',
        'division' => 'your-division',
    ]
]);
```

### HTTP Methods

```php
// GET request
$response = $google->get('/api/v1/users');

// POST request
$response = $google->post('/api/v1/users', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// PUT request
$response = $google->put('/api/v1/users/1', [
    'name' => 'Jane Doe'
]);

// PATCH request
$response = $google->patch('/api/v1/users/1', [
    'email' => 'jane@example.com'
]);

// DELETE request
$response = $google->delete('/api/v1/users/1');
```

### Custom Requests

```php
use LimNova\ApiManager\ValueObjects\ApiRequest;
use LimNova\ApiManager\Enums\HttpMethod;

$request = new ApiRequest(
    method: HttpMethod::POST,
    url: '/api/v1/data',
    headers: ['Content-Type' => 'application/json'],
    data: ['key' => 'value'],
    query: ['param' => 'value']
);

$response = $google->request($request);
```

### Response Handling

```php
$response = $google->get('/api/v1/users');

if ($response->isSuccess()) {
    $users = $response->getData('users');
    $total = $response->getData('total');
} else {
    $error = $response->getErrorMessage();
    $statusCode = $response->getStatusCode();
}
```

### Laravel Facade

```php
use LimNova\ApiManager\Facades\ApiManager;

// Using the facade
$response = ApiManager::get('google', '/api/v1/users');
$response = ApiManager::post('exactonline', '/api/v1/contacts', $data);
```

### Static Methods

```php
// Direct static calls
$response = ApiManager::get('google', '/api/v1/users', [], [
    'credentials' => [
        'client_id' => 'your-client-id',
        'client_secret' => 'your-client-secret',
    ]
]);
```

## Custom Connectors

### Creating a Custom Connector

```php
<?php

declare(strict_types=1);

namespace App\Connectors;

use LimNova\ApiManager\Connectors\BaseConnector;
use LimNova\ApiManager\ValueObjects\ConnectorConfig;

final class CustomConnector extends BaseConnector
{
    protected function performAuthentication(): bool
    {
        // Implement your authentication logic
        $apiKey = $this->config->getCredential('api_key');
        
        if (!$apiKey) {
            return false;
        }

        // Perform authentication
        return true;
    }

    public function customMethod(): string
    {
        return 'Custom functionality';
    }
}
```

### Registering Custom Connectors

```php
use LimNova\ApiManager\ApiManager;
use App\Connectors\CustomConnector;

// Register the connector
ApiManager::register('custom', CustomConnector::class);

// Use the connector
$custom = ApiManager::connector('custom', [
    'credentials' => [
        'api_key' => 'your-api-key'
    ]
]);
```

## Configuration

### Default Configuration

```php
// config/api-manager.php
return [
    'default' => env('API_MANAGER_DEFAULT_CONNECTOR', 'google'),

    'connectors' => [
        'google' => [
            'base_url' => env('GOOGLE_API_BASE_URL', 'https://www.googleapis.com'),
            'credentials' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
            ],
            'options' => [
                'timeout' => env('GOOGLE_API_TIMEOUT', 30),
                'retries' => env('GOOGLE_API_RETRIES', 3),
            ],
        ],
    ],
];
```

## Error Handling

```php
use LimNova\ApiManager\Exceptions\AuthenticationException;
use LimNova\ApiManager\Exceptions\HttpException;
use LimNova\ApiManager\Exceptions\ConnectorNotFoundException;

try {
    $response = ApiManager::get('google', '/api/v1/users');
} catch (AuthenticationException $e) {
    // Handle authentication errors
    logger()->error('Authentication failed: ' . $e->getMessage());
} catch (HttpException $e) {
    // Handle HTTP errors
    $statusCode = $e->getStatusCode();
    $response = $e->getResponse();
} catch (ConnectorNotFoundException $e) {
    // Handle connector not found
    logger()->error('Connector not found: ' . $e->getMessage());
}
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test
./vendor/bin/phpunit tests/Unit/ApiManagerTest.php
```

### Test Coverage

```bash
# Generate coverage report
composer test-coverage

# View coverage in browser
open coverage/index.html
```

## Code Quality

### Static Analysis

```bash
# Run PHPStan
composer phpstan

# Run with different levels
./vendor/bin/phpstan analyse --level=8
```

### Code Formatting

```bash
# Format code with Laravel Pint
composer format

# Check formatting
composer format-check
```

### Code Refactoring

```bash
# Run Rector for code upgrades
composer rector

# Dry run to see changes
./vendor/bin/rector process --dry-run
```

## Architecture

### Design Patterns

- **Factory Pattern**: `ConnectorFactory` for creating connectors
- **Strategy Pattern**: Different connectors for different APIs
- **Dependency Injection**: IoC container integration
- **Value Objects**: Immutable data structures
- **Read-only Classes**: Immutable connector instances

### SOLID Principles

- **Single Responsibility**: Each class has one responsibility
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Connectors are interchangeable
- **Interface Segregation**: Focused interfaces for different concerns
- **Dependency Inversion**: Depend on abstractions, not concretions

## API Reference

### Enums

- `HttpMethod`: HTTP method enumeration
- `HttpStatusCode`: HTTP status code enumeration
- `ConnectorType`: Available connector types

### Value Objects

- `ApiRequest`: Immutable request object
- `ApiResponse`: Immutable response object
- `ConnectorConfig`: Connector configuration

### Exceptions

- `ApiManagerException`: Base exception class
- `AuthenticationException`: Authentication failures
- `HttpException`: HTTP request failures
- `ConnectorNotFoundException`: Connector not found
- `InvalidConfigurationException`: Configuration errors
- `ValidationException`: Validation failures

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For support and questions, please open an issue on GitHub or contact the development team.