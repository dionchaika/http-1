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
     * @var array
     */
    protected static $standartPorts = [

        'http' => 80,
        'https' => 443

    ];

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.1
     */
    protected static $pctEncoded = '\%[A-Fa-f0-9]{2}';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    protected static $unreserved = 'A-Za-z0-9\-._~';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    protected static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';

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
     * Check is the scheme component of the URI valid.
     *
     * @param  string  $scheme  The scheme component of the URI.
     * @return bool
     */
    protected static function isSchemeValid($scheme)
    {
        return preg_match(static::$schemePattern, $scheme);
    }

    /**
     * Check is the host component of the URI valid.
     *
     * @param  string  $host  The host component of the URI.
     * @return bool
     */
    protected static function isHostValid($host)
    {
        if (0 === stripos($host, '[v')) {
            return static::isIpVFutureValid($host);
        }

        if (0 === strpos($host, '[')) {
            return static::isIpV6AddressValid($host);
        }

        if (preg_match('/^\d{1,3}\./', $host)) {
            return static::isIpV4AddressValid($host);
        }

        return preg_match('/^(['.static::$unreserved.static::$subDelims.']|'.static::$pctEncoded.')*$/', $host);
    }

    /**
     * Check is the "IPvFuture" of the host component of the URI valid.
     *
     * @param  string  $ip  The "IPvFuture" of the host component of the URI.
     * @return bool
     */
    protected static function isIpVFutureValid($ip)
    {
        return preg_match(
            '/^\[v[A-Fa-f0-9]+\.['.static::$unreserved.static::$subDelims.':]+\]$/i', $ip
        );
    }

    /**
     * Check is the "IPv4address" of the host component of the URI valid.
     *
     * @param  string  $ip  The "IPv4address" of the host component of the URI.
     * @return bool
     */
    protected static function isIpV4AddressValid($ip)
    {
        return false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Check is the "IPv6address" of the host component of the URI valid.
     *
     * @param  string  $ip  The "IPv6address" of the host component of the URI.
     * @return bool
     */
    protected static function isIpV6AddressValid($ip)
    {
        return '[' === $ip[0] && ']' === $ip[strlen($ip) - 1] &&
            false !== filter_var(trim($ip, '[]'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Check is the port component of the URI valid.
     *
     * @param  int  $port  The port component of the URI.
     * @return bool
     */
    protected static function isPortValid($port)
    {
        return 0 < $port && 65536 > $port;
    }

    /**
     * Check is the path component of the URI valid.
     *
     * @param  string  $path  The path component of the URI.
     * @return bool
     */
    protected static function isPathValid($path)
    {
        return preg_match(
            '/^(['.static::$unreserved.static::$subDelims.':@\/]|'.static::$pctEncoded.')*$/', $path
        );
    }

    /**
     * Check is the query or fragment component of the URI valid.
     *
     * @param  string  $queryOrFragment  The query or fragment component of the URI.
     * @return bool
     */
    protected static function isQueryOrFragmentValid($queryOrFragment)
    {
        return preg_match(
            '/^(['.static::$unreserved.static::$subDelims.':@\/?]|'.static::$pctEncoded.')*$/', $queryOrFragment
        );
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
        return preg_replace_callback(
            '/(?:[^'.static::$unreserved.static::$subDelims.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/', function ($matches) {
                return rawurlencode($matches[0]);
            }, $this->path
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return preg_replace_callback(
            '/(?:[^'.static::$unreserved.static::$subDelims.'%:@\/?]++|%(?![A-Fa-f0-9]{2}))/', function ($matches) {
                return rawurlencode($matches[0]);
            }, $this->query
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return preg_replace_callback(
            '/(?:[^'.static::$unreserved.static::$subDelims.'%:@\/?]++|%(?![A-Fa-f0-9]{2}))/', function ($matches) {
                return rawurlencode($matches[0]);
            }, $this->fragment
        );
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
            if (! static::isSchemeValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('host' === $name && $value) {
            if (! static::isHostValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('port' === $name && is_int($value)) {
            if (! static::isPortValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('path' === $name && $value) {
            if (! static::isPathValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if (('query' === $name || 'fragment' === $name) && $value) {
            if (! static::isQueryOrFragmentValid($value)) {
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
