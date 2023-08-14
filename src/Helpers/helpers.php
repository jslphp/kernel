<?php

use Symfony\Component\HttpFoundation\ParameterBag;

require __DIR__ . '/shortcuts.php';

/**
 * Check if a class or object implements a specific interface
 *
 * @param string|object $subject
 * @param string $interface
 *
 * @return bool
 */
function hasInterface(string|object $subject, string $interface): bool
{
    return is_file(className($subject)) && in_array($interface, class_uses($subject));
}


/**
 * Get a subset of values from an array or paramter bag
 *
 * @param array|ParameterBag $source
 * @param array $keys If any item is key => value, that value will be used as default if key is missing
 * @param bool $missingAsNull Default: false If no default is set, return null for missing keys
 *
 * @return array
 */
function subset(array|ParameterBag $source, array $keys, bool $missingAsNull = false): array
{
    $source = is_array($source) ? $source : $source->all();

    $target = [];
    foreach ($keys as $key => $value) {
        if (is_int($key) && key_exists($value, $source)) {
            $target[$value] = $source[$value];
            continue;
        }

        $target[$key] = key_exists($key, $source)
            ? $source[$key]
            : $value;
    }

    return $target;
}


/**
 * Redirect the request
 *
 * @param string $location
 * @param int $responseCode
 *
 * @return void
 */
function redirect(string $location, int $responseCode = 302): void
{
    header("Location: {$location}", true, $responseCode);
    exit;
}


/**
 * Get the fully qualified class name
 *
 * @param string|object $classOrObject
 *
 * @return string
 */
function className(string|object $classOrObject): string
{
    return is_object($classOrObject) ? $classOrObject::class : $classOrObject;
}


/**
 * Escape HTML
 *
 * @param mixed $string Must be stringable
 *
 * @return string
 */
function _e($string): string
{
    return htmlentities((string)$string);
}


/**
 * Dump elements
 *
 * @param mixed ...$data
 *
 * @return void
 */
function dump(...$data): void
{
    $caller = debug_backtrace(limit: 2)[1];
    $line = $caller['file'] . ':' . $caller['line'];

    echo '<pre style="padding: 20px; background-color: #222; color: #ff5; font: 13px monospace; border-radius: 5px">';
    echo '<div style="font-size: 12px; color: #888; margin-bottom: 10px; padding-bottom: 10px; border-bottom: solid 1px #333">' . $line . '</div>';
    var_dump(...$data);
    echo '</pre>';
}


/**
 * Dump elements and exit
 *
 * @param mixed ...$data
 *
 * @return void
 */
function dd(...$data): void
{
    dump(...$data);
    exit;
}
