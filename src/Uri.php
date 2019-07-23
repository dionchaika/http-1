<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * The scheme component of the URI.
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * The user information component of the URI.
     *
     * @var string
     */
    protected $userInfo = '';

    /**
     * The host component of the URI.
     *
     * @var string
     */
    protected $host = '';

    /**
     * The port component of the URI.
     *
     * @var int|null
     */
    protected $port;

    /**
     * The path component of the URI.
     *
     * @var string
     */
    protected $path = '';

    /**
     * The query component of the URI.
     *
     * @var string
     */
    protected $query = '';

    /**
     * The fragment component of the URI.
     *
     * @var string
     */
    protected $fragment = '';

    /**
     * The array of standart TCP and UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

    /**
     * Is the TCP or UDP port standart for the given scheme component of the URI.
     *
     * @param int $port The TCP or UDP port.
     * @param string $scheme The scheme component of the URI.
     *
     * @return bool Returns true if the TCP or UDP port standart for the given scheme component of the URI.
     */
    public static function isStandartPortForScheme($port, $scheme)
    {
        return isset(static::$standartPorts) && $port === static::$standartPorts[$scheme];
    }

    /**
     * Compose a user information component of the URI into a string.
     *
     * @param string $user The URI user.
     * @param string $password The URI password.
     *
     * @return string The user information component of the URI as a string.
     */
    protected function composeUserInfo($user, $password = null)
    {
        return ('' !== $user && null !== $password && '' !== $password) ? $user.':'.$password : $user;
    }
}
