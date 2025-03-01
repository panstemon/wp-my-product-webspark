<?php

namespace WPMyProductWebspark\Utils;

class CacheManager
{
    public static function cacheFunction(string $method, array $args, callable $callback, int $ttl = HOUR_IN_SECONDS): mixed
    {
        $key = self::generateFunctionKey($method, $args);
        $cachedValue = get_transient($key);

        if ($cachedValue) {
            return $cachedValue;
        }

        $value = $callback(...$args);
        set_transient($key, $value, $ttl);

        return $value;
    }

    private static function generateFunctionKey(string $method, array $args): string
    {
        return 'wpmpw/'.md5($method . serialize($args));
    }
}