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
     * @param int $port The TCP or UDP port.
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
        
    }
}
