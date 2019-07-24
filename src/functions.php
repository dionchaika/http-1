<?php

namespace Lazy\Http;

use InvalidArgumentException;

/**
 * Filter a URI scheme component as described in the "RFC 3986".
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
 * Filter a URI port component as described in the "RFC 3986".
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

    trigger_error('TCP or UDP port MUST be between 1 and 65535.');

    throw new InvalidArgumentException("URI port component is not valid: {$port}!");
}

/**
 * Filter a URI path component as described in the "RFC 3986".
 *
 * @see https://tools.ietf.org/html/rfc3986#section-3.3
 *
 * @param string $path
 * @param string $scheme Optional URI scheme component.
 * @param string $authority Optional URI authority component.
 * @return string
 * @throws InvalidArgumentException
 */
function filter_uri_path($path, $scheme = '', $authority = '')
{
    $valid = true;

    if ('' !== $scheme && 0 === strpos($path, ':')) {
        $valid = false;

        trigger_error(
            'Path of a URI with a scheme component MUST NOT start with a colon (:).'
        );
    } else if ('' !== $authority && 0 !== strpos($path, '/')) {
        $valid = false;

        trigger_error(
            'Path of a URI with an authority component MUST be empty or start with a slash (/).'
        );
    } else if ('' === $authority && 0 === strpos($path, '//')) {
        $valid = false;

        trigger_error(
            'Path of a URI without an authority component MUST NOT start with two slashes (//).'
        );
    }

    $pattern = '/^(?:[\/A-Za-z0-9\-._~!$&\'()*+,;=:@]|\%[A-Fa-f0-9]{2})*$/';

    if ($valid && ('' === $path || '/' === $path || preg_match($pattern, $path))) {
        return $path;
    }

    throw new InvalidArgumentException("URI path component is not valid: {$path}!");
}
