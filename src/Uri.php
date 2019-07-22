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
                $authority = $userInfo.'@'.$authority;
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

    public function withScheme($scheme)
    {
        $new = clone $this;

        $new->scheme = $scheme;

        return $new;
    }

    public function withUserInfo($user, $password = null)
    {
        $new = clone $this;

        $new->userInfo = $this->composeUserInfo($user, $password);

        return $new;
    }

    public function withHost($host)
    {
        $new = clone $this;

        $new->host = $host;

        return $new;
    }

    public function withPort($port)
    {
        $new = clone $this;

        $new->port = $port;

        return $new;
    }

    public function withPath($path)
    {
        $new = clone $this;

        $new->path = $path;

        return $new;
    }

    public function withQuery($query)
    {
        $new = clone $this;

        $new->query = $query;

        return $new;
    }

    public function withFragment($fragment)
    {
        $new = clone $this;

        $new->fragment = $fragment;

        return $new;
    }

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        $uri = '';

        if ($scheme) {
            $uri .= $scheme.':';
        }

        if ($authority) {
            $uri .= '//'.$authority;
        }

        $uri .= $path;

        if ($query) {
            $uri .= '?'.$query;
        }

        if ($fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * Compose a user information component of the URI.
     *
     * @param string $user The user component of the URI.
     * @param string|null $password The password component of the URI.
     * @return string
     */
    protected function composeUserInfo($user, $password = null)
    {
        $userInfo = $user;

        if ($userInfo && $password) {
            $userInfo .= ':'.$password;
        }

        return $userInfo;
    }
}
