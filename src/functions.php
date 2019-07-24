<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;

use function rawurlencode as php_rawurlencode;

if (! function_exists('filter_uri_scheme')) {
    /**
     * Filter a URI scheme component according to "RFC 3986".
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
}

if (! function_exists('filter_uri_host')) {
    /**
     * Filter a URI host component according to "RFC 3986".
     *
     * https://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @param string $host
     * @return string
     * @throws InvalidArgumentException
     */
    function filter_uri_host($host)
    {
        $valid = true;

        if (0 === stripos($host, '[v')) {
            $valid = false;
            trigger_error('Address mechanism is not supported.');
        }

        $validIpV4 = false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        $validIpV6 = false !== filter_var(trim($host, '[]'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

        $pattern = '/^(?:[A-Za-z0-9\-._~!$&\'()*+,;=]|\%[A-Fa-f0-9]{2})*$/';

        if ($valid && ('' === $host || $validIpV4 || $validIpV6 || preg_match($pattern, $host))) {
            return $host;
        }

        throw new InvalidArgumentException("URI host component is not valid: {$host}!");
    }
}

if (! function_exists('filter_uri_port')) {
    /**
     * Filter a URI port component according to "RFC 3986".
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
}

if (! function_exists('filter_uri_path')) {
    /**
     * Filter a URI path component according to "RFC 3986".
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
}

if (! function_exists('filter_uri_query')) {
    /**
     * Filter a URI query component according to "RFC 3986".
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @param string $query
     * @return string
     * @throws InvalidArgumentException
     */
    function filter_uri_query($query)
    {
        $pattern = '/^(?:[\/A-Za-z0-9\-._~!$&\'()*+,;=:@?]|\%[A-Fa-f0-9]{2})*$/';

        if ('' === $query || preg_match($pattern, $query)) {
            return $query;
        }

        throw new InvalidArgumentException("URI query component is not valid: {$query}!");
    }
}

if (! function_exists('rawurlencode_path')) {
    /**
     * URL-encode a path component according to "RFC 3986".
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @param string $path
     * @return string
     */
    function rawurlencode_path($path)
    {
        $pattern = '/(?:[^\/A-Za-z0-9\-._~!$&\'()*+,;=:@%]++|\%(?![A-Fa-f0-9]{2}))*$/';

        $callback = function ($matches) {
            return rawurlencode($matches[0]);
        };

        return preg_replace_callback($pattern, $callback, $path);
    }
}

if (! function_exists('rawurlencode_query')) {
    /**
     * URL-encode a query component according to "RFC 3986".
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @param string $query
     * @return string
     */
    function rawurlencode_query($query)
    {
        $pattern = '/(?:[^\/A-Za-z0-9\-._~!$&\'()*+,;=:@?%]++|\%(?![A-Fa-f0-9]{2}))*$/';

        $callback = function ($matches) {
            return rawurlencode($matches[0]);
        };

        return preg_replace_callback($pattern, $callback, $query);
    }
}
