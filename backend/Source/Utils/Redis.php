<?php

namespace Source\Utils;

use Predis\Client;

class Redis {
    private static ?Client $client = null;

    public static function getClient(): Client {
        if (self::$client === null)  {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host' => 'redis', # Ou localhost...
                'port' => 6379
            ]);
        }

        return self::$client;
    }

    public static function get($key): string|null {
        return Redis::getClient()->get($key);
    }

    public static function remove(string $key): void {
        Redis::getClient()->del($key);
    }

    public static function append(string $key, array|string $value, ?string $expires_in): void {
        $stringified_value = is_array($value) ? json_encode($value) : $value;
    
        if ($expires_in === null) {
            Redis::getClient()->set($key, $stringified_value);
            return;
        }

        define('SECONDS', 1000);
        Redis::getClient()->setex($key, $expires_in / SECONDS, $stringified_value);
    }
}