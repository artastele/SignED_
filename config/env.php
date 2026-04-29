<?php
/**
 * Environment Configuration Loader
 * Loads environment variables from .env file
 */

class Env
{
    private static $loaded = false;
    private static $vars = [];

    /**
     * Load environment variables from .env file
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(__DIR__) . '/.env';
        }

        if (!file_exists($path)) {
            // Try config/.env
            $path = __DIR__ . '/.env';
        }

        if (!file_exists($path)) {
            // Fall back to example environment file if real .env is not present
            $examplePath = dirname(__DIR__) . '/.env.example';
            if (file_exists($examplePath)) {
                $path = $examplePath;
            } else {
                $examplePath = __DIR__ . '/.env.example';
                if (file_exists($examplePath)) {
                    $path = $examplePath;
                }
            }
        }

        if (!file_exists($path)) {
            throw new Exception('.env file not found. Please copy .env.example to .env and configure it.');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                self::$vars[$key] = $value;
                
                // Also set as environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        if (isset(self::$vars[$key])) {
            return self::$vars[$key];
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Check if environment variable exists
     */
    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$vars[$key]) || isset($_ENV[$key]) || isset($_SERVER[$key]);
    }
}
