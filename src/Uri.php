<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    protected $scheme = '';
    protected $userInfo = '';
    protected $host = '';
    protected $port;
    protected $path = '';
    protected $query = '';
    protected $fragment = '';

    /**
     * The array of standart TCP or UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = [

        'http' => 80,
        'https' => 443

    ];

    /**
     * Check is the TCP or UDP port standart for the given scheme.
     *
     * @param  int  $port  The TCP or UDP port.
     * @param  string  $scheme  The scheme component of the URI.
     * @return bool
     */
    protected static function isStandartPort($port, $scheme)
    {
        return array_key_exists($scheme, static::$standartPorts) && $port === static::$standartPorts[$scheme];
    }

    /**
     * Create a new URI instance.
     *
     * @param  string  $uri
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($uri = '')
    {
        $parts = parse_url($uri);

        if (false === $parts) {
            throw new InvalidArgumentException("Unable to parse the URI: {$uri}!");
        }

        $userInfo = $this->buildUserInfo(
            ! empty($parts['user']) ? $parts['user'] : '', ! empty($parts['pass']) ? $parts['pass'] : null
        );

        $this->applyComponent('scheme', ! empty($parts['scheme']) ? $parts['scheme'] : '');
        $this->applyComponent('userInfo', $userInfo);
        $this->applyComponent('host', ! empty($parts['host']) ? $parts['host'] : '');
        $this->applyComponent('port', ! empty($parts['port']) ? $parts['port'] : null);
        $this->applyComponent('path', ! empty($parts['path']) ? $parts['path'] : '');
        $this->applyComponent('query', ! empty($parts['query']) ? $parts['query'] : '');
        $this->applyComponent('fragment', ! empty($parts['fragment']) ? $parts['fragment'] : '');
    }

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
        $authority = $this->getHost();

        if ($authority) {
            $userInfo = $this->getUserInfo();

            if ($userInfo) {
                $authority = $userInfo.'@'.$authority;
            }

            $port = $this->getPort();

            if ($port) {
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
        return static::isStandartPort($this->port, $this->scheme) ? null : $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritDoc}
     */
    public function withScheme($scheme)
    {
        $new = clone $this;

        $new->applyComponent('scheme', $scheme);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $new = clone $this;

        $new->applyComponent(
            'userInfo', $this->buildUserInfo($user, $password)
        );

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        $new = clone $this;

        $new->applyComponent('host', $host);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        $new = clone $this;

        $new->applyComponent('port', $port);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        $new = clone $this;

        $new->applyComponent('path', $path);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        $new = clone $this;

        $new->applyComponent('query', $query);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        $new = clone $this;

        $new->applyComponent('fragment', $fragment);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
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

        if ($authority) {
            $path = '/'.ltrim($path, '/');
        }

        if (! $authority && 0 === strpos($path, '/')) {
            $path = '/'.ltrim($path, '/');
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
     * Build a user information component of the URI.
     *
     * @param  string  $user  The user of the user information
     *      component of the URI.
     * @param  string|null  $password  The password of the user information
     *      component of the URI.
     * @return string
     */
    protected function buildUserInfo($user, $password = null)
    {
        return ($user && $password) ? $user.':'.$password : $user;
    }

    /**
     * Apply a component to the URI.
     *
     * @param  string  $name  The URI component name.
     * @param  mixed  $value  The URI component value.
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function applyComponent($name, $value)
    {
        if ('scheme' === $name && $value) {
            if (! Rfc3986::isSchemeValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('host' === $name && $value) {
            if (! Rfc3986::isHostValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('port' === $name && is_int($value)) {
            if (! Rfc3986::isPortValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('path' === $name && $value) {
            if (! Rfc3986::isPathValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if (('query' === $name || 'fragment' === $name) && $value) {
            if (! Rfc3986::isQueryOrFragmentValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        $this->{$name} = $value;
    }

    /**
     * Throw an exception if a URI component is invalid.
     *
     * @param  string  $name  The URI component name.
     * @param  mixed  $value  The URI component value.
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function throwInvalidComponentException($name, $value)
    {
        throw new InvalidArgumentException("Invalid {$name} component of the URI: {$value}!");
    }
}
