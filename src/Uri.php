<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string The scheme component of the URI. */
    protected $scheme = '';

    /** @var string The user information component of the URI. */
    protected $userInfo = '';

    /** @var string The host component of the URI. */
    protected $host = '';

    /** @var int|null The port component of the URI. */
    protected $port;

    /** @var string The path component of the URI. */
    protected $path = '';

    /** @var string The query component of the URI. */
    protected $query = '';

    /** @var string The fragment component of the URI. */
    protected $fragment = '';

    /** @var array The array of standart TCP or UDP ports */
    protected static $standartPorts = [

        'http' => 80,
        'https' => 443

    ];

    /**
     * Is the TCP or UDP port standart for the given scheme.
     *
     * @param int $port The TCP or UDP port.
     * @param string $scheme The scheme component of the URI.
     * @return bool
     */
    protected static function isStandartPort($port, $scheme)
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }

    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    public function getAuthority()
    {
        $authority = $this->getHost();

        if ($authority) {
            $userInfo = $this->getUserInfo();

            if ($userInfo) {
                $authority .= $userInfo.'@'.$authority;
            }

            $port = $this->getPort();

            if (null !== $port) {
                $authority .= ':'.$port;
            }
        }

        return $authority;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function getHost()
    {
        return strtolower($this->host);
    }

    public function getPort()
    {
        return static::isStandartPort($this->port, $this->scheme) ? null : $this->port;
    }

    public function getPath()
    {
        return rawurlencode(rawurldecode($this->path));
    }

    public function getQuery()
    {
        return rawurlencode(rawurldecode($this->query));
    }

    public function getFragment()
    {
        return rawurlencode(rawurldecode($this->fragment));
    }
}
