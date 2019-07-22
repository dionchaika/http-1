<?php

namespace Lazy\Http;

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
     * The array of standart TCP or UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

    /**
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        $authority = $host;

        if ($authority) {
            if ($userInfo) {
                $authority = $userInfo.'@'.$authority;
            }

            if (null !== $port) {
                $authority .= ':'.$port;
            }
        }

        return $authority;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return strtolower($this->host);
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return static::isStandartPortForScheme($this->port, $this->scheme) ? null : $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->composeComponents(
            $this->getScheme(),
            $this->getAuthority(),
            $this->getPath(),
            $this->getQuery(),
            $this->getFragment()
        );
    }

    /**
     * Compose a user information component of the URI.
     *
     * @param string $user The URI user.
     * @param string|null $password The URI password.
     *
     * @return string
     */
    protected function composeUserInfo($user, $password = null)
    {
        return ($user && $password) ? $user.':'.$password : $user;
    }

    /**
     * Compose the URI components into a string.
     *
     * @param string $scheme The scheme component of the URI.
     * @param string $authority The authority of the URI.
     * @param string $path The path component of the URI.
     * @param string $query The query component of the URI.
     * @param string $fragment The fragment component of the URI.
     *
     * @return string
     */
    protected function composeComponents($scheme, $authority, $path, $query, $fragment)
    {
        $uri = '';

        if ($scheme) {
            $uri .= $scheme.':';
        }

        if ($authority) {
            $uri .= '//'.$authority;
        }

        $uri .= '/'.ltrim($path, '/');

        if ($query) {
            $uri .= '?'.$query;
        }

        if ($fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * Is the TCP or UDP port standart for the given scheme component of the URI.
     *
     * @param int $port The TCP or UDP port.
     * @param string $scheme The scheme component of the URI.
     *
     * @return bool
     */
    public static function isStandartPortForScheme($port, $scheme = 'http')
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }
}
