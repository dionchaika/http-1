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
     * The user component of the URI.
     *
     * @var string
     */
    protected $user = '';

    /**
     * The password component of the URI.
     *
     * @var string|null
     */
    protected $password;

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
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * The "RFC 3986" unreserved characters.
     *
     * @var string
     */
    protected static $unreserved = 'a-zA-Z0-9\-._~';

    /**
     * Create a new URI instance.
     *
     * @param  string  $uri  The URI string.
     */
    public function __construct($uri)
    {
        $parts = parse_url($uri);

        if (false === $parts) {
            throw new InvalidArgumentException("Unable to parse the URI: {$uri}!");
        }

        $this->scheme = ! empty($parts['scheme']) ? $parts['scheme'] : '';
        $this->user = ! empty($parts['user']) ? $parts['user'] : '';
        $this->password = ! empty($parts['pass']) ? $parts['pass'] : null;
        $this->host = ! empty($parts['host']) ? $parts['host'] : '';
        $this->port = ! empty($parts['port']) ? $this->validatePort($parts['port']) : null;
        $this->path = ! empty($parts['path']) ? $parts['path'] : '';
        $this->query = ! empty($parts['query']) ? $parts['query'] : '';
        $this->fragment = ! empty($parts['fragment']) ? $parts['fragment'] : '';
    }

    /**
     * Check is the TCP or UDP port is standart for the given scheme.
     *
     * @param  int  $port  The TCP or UDP port.
     * @param  string  $scheme  The scheme component of the URI.
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
            return null;
        }

        return static::isStandartPort($this->port, $this->scheme) ? null : $this->port;
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
        $clone = clone $this;

        $clone->port = $this->validatePort($port);

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

        if ($authority || 0 === strpos($path, '/')) {
            $path = '/'.rtrim($path, '/');
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
     * Validate a port component of the URI.
     *
     * @param  int|null  $port  The port component of the URI.
     * @return int|null
     */
    protected function validatePort($port)
    {
        if (null !== $port && (1 > $port || 65535 < $port)) {
            throw new InvalidArgumentException(
                "Invalid port: {$port}! "
                ."TCP or UDP port must be between 1 and 65535."
            );
        }

        return $port;
    }
}
