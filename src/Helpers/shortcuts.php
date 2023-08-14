<?php

/**
 * Short cuts to some common method calls on common instances
 */

use Jsl\Kernel\Kernel;
use Jsl\Kernel\Request\RequestInterface;

/**
 * Get a named route
 *
 * @param string $name
 * @param array $args
 *
 * @return string
 */
function route(string $name, array $args = []): string
{
    return Kernel::getKernel()->router->getNamedRoute($name, $args);
}

/**
 * @return RequestInterface
 */
function request(): RequestInterface
{
    return Kernel::getKernel()->request;
}

/**
 * Get data from the request (parsed body)
 *
 * @param array|string $key If array, subset() will be used
 * @param array $fallback Only used if $key is a string
 *
 * @return mixed
 */
function fromRequest(array|string $key, mixed $fallback = null): mixed
{
    if (is_array($key)) {
        return subset(request()->request, $key);
    }

    return request()->request->get($key, $fallback);
}


/**
 * Get data from the query string
 *
 * @param array|string $key If array, subset() will be used
 * @param array $fallback Only used if $key is a string
 *
 * @return mixed
 */
function fromQueryString(array|string $key, mixed $fallback = null): mixed
{
    if (is_array($key)) {
        return subset(request()->query, $key);
    }

    return request()->query->get($key, $fallback);
}
