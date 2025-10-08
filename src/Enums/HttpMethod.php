<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Enums;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';

    public function isReadOnly(): bool
    {
        return match ($this) {
            self::GET, self::HEAD, self::OPTIONS => true,
            default => false,
        };
    }

    public function allowsBody(): bool
    {
        return match ($this) {
            self::POST, self::PUT, self::PATCH => true,
            default => false,
        };
    }
}
