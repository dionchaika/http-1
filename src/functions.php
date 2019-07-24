<?php

namespace Lazy\Http;

use InvalidArgumentException;

/**
 * Filter a URI scheme component as described in "RFC 3986".
 *
 * @see https://tools.ietf.org/html/rfc3986#section-3.1
 *
 * @param string $scheme
 * @return string
 * @throws InvalidArgumentException
 */
function filter_uri_scheme($scheme)
{
    $pattern = '/^[a-z][a-z0-9+\-.]*$/i';

    if ('' === $scheme || preg_match($pattern, $scheme)) {
        return $scheme;
    }

    throw new InvalidArgumentException("URI scheme component is not valid: {$scheme}!");
}

/**
 * Filter a URI port component as described in "RFC 3986".
 *
 * @see https://tools.ietf.org/html/rfc3986#section-3.2.3
 *
 * @param int|null $port
 * @return int|null
 * @throws InvalidArgumentException
 */
function filter_uri_port($port)
{
    if (null === $port || (is_int($port) && 0 < $port && 65536 > $port)) {
        return $port;
    }

    trigger_error('TCP or UDP port must be between 1 and 65535!');

    throw new InvalidArgumentException("URI port component is not valid: {$port}!");
}
