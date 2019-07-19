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
        $parts = parse_uri($uri);

        if (false === $parts) {
            throw new InvalidArgumentException("Unable to parse the URI: {$uri}!");
        }

        $user = ! empty($parts['user']) ? $parts['user'] : '';
        $password = ! empty($parts['pass']) ? $parts['pass'] : null;

        $userInfo = $user;

        if ($userInfo && $password) {
            $userInfo .= ':'.$password;
        }

        $this->applyComponent('scheme', ! empty($parts['scheme']) ? $parts['scheme'] : '');
        $this->applyComponent('userInfo', $userInfo);
        $this->applyComponent('host', ! empty($parts['host']) ? $parts['host'] : '');
        $this->applyComponent('port', ! empty($parts['port']) ? $parts['port'] : null);
        $this->applyComponent('path', ! empty($parts['path']) ? $parts['path'] : '');
        $this->applyComponent('query', ! empty($parts['query']) ? $parts['query'] : '');
        $this->applyComponent('fragment', ! empty($parts['fragment']) ? $parts['fragment'] : '');
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
