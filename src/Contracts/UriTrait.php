<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

trait UriTrait
{
    /**
     * The "RFC 3986" sub-delimiters.
     *
     * @var string
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * The "RFC 3986" unreserved characters.
     *
     * @var string
     */
    protected static $unreserved = 'A-Za-z0-9\-._~';

    /**
     * The array of standart TCP and UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

    /**
     * Is the TCP or UDP port
     * standart for the given scheme component of the URI.
     *
     * @param int|null $port The TCP or UDP port.
     * @param string $scheme The scheme component of the URI.
     *
     * @return bool
     */
    public static function isStandartPort($port, $scheme)
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }

    /**
     * Validate a scheme component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @param string $scheme The scheme component of the URI.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function validateSchemeComponent($scheme)
    {
        if ('' === $scheme) {
            return $scheme;
        }

        if (! preg_match('/^[A-Za-z][A-Za-z0-9+\-.]*$/', $scheme)) {
            throw new InvalidArgumentException(
                "The scheme component of the URI is not valid: {$scheme}!"
            );
        }

        return $scheme;
    }

    /**
     * Validate a user information component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.1
     *
     * @param string $userInfo The user information component of the URI.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function validateUserInfoComponent($userInfo)
    {
        if ('' === $userInfo) {
            return $userInfo;
        }

        if (! preg_match('/^(?:['.static::$unreserved.static::$subDelims.':]|\%[A-Fa-f0-9]{2})*$/', $userInfo)) {
            throw new InvalidArgumentException(
                "The user information component of the URI is not valid: {$userInfo}!"
            );
        }

        return $userInfo;
    }

    /**
     * Validate a host component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @param string $host The host component of the URI.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function validateHostComponent($host)
    {
        if ('' === $host) {
            return $host;
        }

        if (! preg_match('/^(?:['.static::$unreserved.static::$subDelims.']|\%[A-Fa-f0-9]{2})*$/', $host)) {
            throw new InvalidArgumentException(
                "The host component of the URI is not valid: {$host}!"
            );
        }

        return $host;
    }

    /**
     * Validate a port component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.3
     *
     * @param int|null $port The port component of the URI.
     *
     * @return int|null
     *
     * @throws InvalidArgumentException
     */
    protected static function validatePortComponent($port)
    {
        if (null === $port) {
            return $port;
        }

        $opts = [

            'options' => [

                'min_range' => 1,
                'max_range' => 65535

            ],
            'flags' => FILTER_FLAG_ALLOW_HEX | FILTER_FLAG_ALLOW_OCTAL

        ];

        if (false === filter_var($port, FILTER_VALIDATE_INT, $opts)) {
            throw new InvalidArgumentException(
                "The port component of the URI is not valid: {$port}! "
                ."TCP or UDP port must be in the range from 1 to 65535."
            );
        }

        return $port;
    }

    /**
     * Validate a path component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @param string $path The path component of the URI.
     * @param UriInterface $uri An optional URI instance.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function validatePathComponent($path, UriInterface $uri = null)
    {
        if ('' === $path) {
            return $path;
        }

        if (! preg_match('/^(?:[\/'.static::$unreserved.static::$subDelims.':@]|\%[A-Fa-f0-9]{2})*$/', $path)) {
            throw new InvalidArgumentException(
                "The path component of the URI is not valid: {$path}!"
            );
        }

        return $path;
    }

    /**
     * Validate a query or a fragment component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @param string $queryOrFragment The query or the fragment component of the URI.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function validateQueryOrFragmentComponent($queryOrFragment)
    {
        if ('' === $queryOrFragment) {
            return $queryOrFragment;
        }

        if (! preg_match('/^(?:[\/'.static::$unreserved.static::$subDelims.':@?]|\%[A-Fa-f0-9]{2})*$/', $queryOrFragment)) {
            throw new InvalidArgumentException(
                "The query or the fragment component of the URI is not valid: {$queryOrFragment}!"
            );
        }

        return $queryOrFragment;
    }
}
