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
     * @param string $host The host component of the URI.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function validateHostComponent($host)
    {
        
    }

    /**
     * Validate a port component of the URI.
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

        if (null !== $uri) {
            $scheme = $uri->getScheme();
            $authority = $uri->getAuthority();

            if ('' !== $scheme && ':' === $path[0]) {
                throw new InvalidArgumentException(
                    "The path component of the URI is not valid: {$path}! "
                    ."Path of a URI with a scheme component must not begin with a colon."
                );
            }

            if ('' !== $authority && '/' !== $path[0]) {
                throw new InvalidArgumentException(
                    "The path component of the URI is not valid: {$path}! "
                    ."Path of a URI with an authority must be empty or begin with a slash."
                );
            }

            if ('' === $authority && 0 === strpos($path, '//')) {
                throw new InvalidArgumentException(
                    "The path component of the URI is not valid: {$path}! "
                    ."Path of a URI without an authority must not begin with more than one slash."
                );
            }
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
