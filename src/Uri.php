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
     * The pattern for the scheme component of the URI.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    protected static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';

    /**
     * The URI sub-delimiters.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * The URI unreserved characters.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    protected static $unreserved = 'a-zA-Z0-9\-._~';

    /**
     * The pattern for the URI percent-encoded characters.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.1
     */
    protected static $pctEncodedPattern = '(\%[A-Fa-f0-9]{2})';

    /**
     * Create a new URI instance.
     *
     * @param  string  $uri  The URI string.
     */
    public function __construct($uri = '')
    {
        $parts = parse_url($uri);

        if (false === $parts) {
            throw new InvalidArgumentException("Unable to parse the URI: {$uri}!");
        }

        //
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
        return preg_replace_callback('/(?:[^'.static::$unreserved.static::$subDelims.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/', function ($matches) {
            return rawurlencode($matches[0]);
        }, $this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return preg_replace_callback('/(?:[^'.static::$unreserved.static::$subDelims.'%:@\/?]++|%(?![A-Fa-f0-9]{2}))/', function ($matches) {
            return rawurlencode($matches[0]);
        }, $this->query);
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return preg_replace_callback('/(?:[^'.static::$unreserved.static::$subDelims.'%:@\/?]++|%(?![A-Fa-f0-9]{2}))/', function ($matches) {
            return rawurlencode($matches[0]);
        }, $this->fragment);
    }

    /**
     * {@inheritDoc}
     */
    public function withScheme($scheme)
    {
        $clone = clone $this;

        return $clone->applyComponent('scheme', $scheme);
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

        return $clone->applyComponent('host', $host);
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        $clone = clone $this;

        return $clone->applyComponent('port', $port);
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        $clone = clone $this;

        return $clone->applyComponent('path', $path);
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        $clone = clone $this;

        return $clone->applyComponent('query', $query);
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        $clone = clone $this;

        return $clone->applyComponent('fragment', $fragment);
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
     * Apply a component to the URI.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function applyComponent($name, $value)
    {
        if ('scheme' === $name) {
            $value = (string) $value;

            if ($value && ! preg_match(static::$schemePattern, $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('port' === $name) {
            if (null !== $value) {
                $value = (int) $value;
            }

            if (is_int($value) && (1 > $value || 65535 < $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('host' === $name) {
            $value = (string) $value;

            if (preg_match('/^\[(.+)\]$/', $value, $matches)) {
                if (
                    ! preg_match('/^v|V[A-Fa-f0-9]\.['.static::$unreserved.static::$subDelims.':]$/', $matches[0]) ||
                    false === filter_var($matches[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
                ) {
                    $this->throwInvalidComponentException($name, $value);
                }
            } else if (
                false === filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
                ! preg_match('/^(['.static::$unreserved.static::$subDelims.']|'.static::$pctEncodedPattern.')*$/', $value)
            ) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('path' === $name) {
            $value = (string) $value;

            if (
                ($this->getAuthority() && $value && '/' !== $value[0]) ||
                (! $this->getAuthority() && '/' === $value[0] && '/' === $value[1]) ||
                ! preg_match('/^(['.static::$unreserved.static::$subDelims.':@\/]|'.static::$pctEncodedPattern.')*$/', $value)
            ) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('query' === $name) {
            $value = (string) $value;

            if (! preg_match('/^(['.static::$unreserved.static::$subDelims.':@\/?]|'.static::$pctEncodedPattern.')*$/', $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('fragment' === $name) {
            $value = (string) $value;

            if (! preg_match('/^(['.static::$unreserved.static::$subDelims.':@\/?]|'.static::$pctEncodedPattern.')*$/', $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        $this->{$name} = $value;
    }

    /**
     * Throw an exception if the component of the URI is invalid.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function throwInvalidComponentException($name, $value)
    {
        throw new InvalidArgumentException("Invalid {$name} component of the URI: {$value}!");
    }
}
