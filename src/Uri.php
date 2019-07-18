<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * @var string The scheme component of the URI (without ":" character).
     */
    protected $scheme = '';

    /**
     * @var string The user component of the URI.
     */
    protected $user = '';

    /**
     * @var string|null The password component of the URI.
     */
    protected $password;

    /**
     * @var string The host component of the URI.
     */
    protected $host = '';

    /**
     * @var int|null The port component of the URI.
     */
    protected $port;

    /**
     * @var string The path component of the URI.
     */
    protected $path = '';

    /**
     * @var string The query component of the URI (without "?" character).
     */
    protected $query = '';

    /**
     * @var string The fragment component of the URI (without "#" character).
     */
    protected $fragment = '';

    /**
     * The array of standart TCP or UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = [

        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'sftp' => 22

    ];

    /**
     * The "RFC 3986" sub-delimiters.
     *
     * @var string
     */
    protected static $subDelimChars = '!$&\'()*+,;=';

    /**
     * The "RFC 3986" unreserved characters.
     *
     * @var string
     */
    protected static $unreservedChars = 'a-zA-Z0-9\-._~';

    /**
     * Check is the TCP or UDP port is standart for the given scheme.
     *
     * @param  int  $port
     * @param  string  $scheme
     * @return bool
     */
    public static function isStandartPort($port, $scheme)
    {
        return array_key_exists($scheme, static::$standartPorts) && $port === static::$standartPorts[$scheme];
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
        $authority = $this->host;

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
        $userInfo = $this->user;

        if ($userInfo && $this->password) {
            $userInfo .= ':'.$this->password;
        }

        return $userInfo;
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
        if (null === $this->port) {
            return $this->port;
        }

        $port = (int) $this->port;

        return $this->isStandartPort($port, $this->scheme) ? null : $port;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return preg_replace_callback('/(?:[^'.static::$unreservedChars.static::$subDelimChars.'\%\:\@\/]++|%(?![a-fA-F0-9]{2}))/', function ($matches) {
            return rawurlencode($matches[0]);
        }, $this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return preg_replace_callback('/(?:[^'.static::$unreservedChars.static::$subDelimChars.'\%\:\@\/\?]++|%(?![a-fA-F0-9]{2}))/', function ($matches) {
            return rawurlencode($matches[0]);
        }, $this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return preg_replace_callback('/(?:[^'.static::$unreservedChars.static::$subDelimChars.'\%\:\@\/\?]++|%(?![a-fA-F0-9]{2}))/', function ($matches) {
            return rawurlencode($matches[0]);
        }, $this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function withScheme($scheme)
    {
        $clone = clone $this;

        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;

        $clone->user = $user;
        $clone->password = $user ? $password : null;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        $clone = clone $this;

        $clone->host = $host;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        if (null !== $port) {
            $port = $port;

            if (1 > $port || 65535 < $port) {
                throw new InvalidArgumentException(
                    "Invalid port: {$port}! "
                    ."TCP or UDP port must be between 1 and 65535."
                );
            }
        }

        $clone = clone $this;

        $clone->port = $port;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        $clone = clone $this;

        $clone->path = $path;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        $clone = clone $this;

        $clone->query = $query;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        $clone = clone $this;

        $clone->fragment = $fragment;

        return $clone;
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

        if ($authority && 0 !== strpos($path, '/')) {
            $path = '/'.$path;
        } else if (! $authority && 0 === strpos($path, '/')) {
            $path . '/'.ltrim($path, '/');
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
}
