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

    const SUB_DELIMS = '!$&\'()*+,;=';
    const UNRESERVED = 'A-Za-z0-9\-._~';

    protected static $standartPorts = ['http' => 80, 'https' => 443];

    public function __construct(string $uri = '')
    {
        $components = parse_url($uri);

        if (false === $components) {
            throw new InvalidArgumentException("Unable to parse the URI string: {$uri}!");
        }

        $userInfo = $this->composeUserInfo(
            ! empty($components['user']) ? $components['user'] : '',
            ! empty($components['pass']) ? $components['pass'] : null
        );

        $this->applyComponent('scheme', ! empty($components['scheme']) ? $components['scheme'] : '');
        $this->applyComponent('userInfo', $userInfo);
        $this->applyComponent('host', ! empty($components['host']) ? $components['host'] : '');
        $this->applyComponent('port', ! empty($components['port']) ? $components['port'] : null);
        $this->applyComponent('path', ! empty($components['path']) ? $components['path'] : '');
        $this->applyComponent('query', ! empty($components['query']) ? $components['query'] : '');
        $this->applyComponent('fragment', ! empty($components['fragment']) ? $components['fragment'] : '');
    }

    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        $authority = $host;

        if ('' !== $authority) {
            if ('' !== $userInfo) {
                $authority = $userInfo.'@'.$authority;
            }

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
        return static::isStandartPortForScheme($this->port, $this->scheme) ? null : $this->port;
    }

    public function getPath()
    {
        return $this->encodePath($this->path);
    }

    public function getQuery()
    {
        return $this->encodeQueryOrFragment($this->query);
    }

    public function getFragment()
    {
        return $this->encodeQueryOrFragment($this->fragment);
    }

    public function withScheme($scheme)
    {
        $new = clone $this;

        $new->applyComponent('scheme', $scheme);

        return $new;
    }

    public function withUserInfo($user, $password = null)
    {
        $new = clone $this;

        $new->applyComponent('userInfo', $this->composeUserInfo($user, $password));

        return $new;
    }

    public function withHost($host)
    {
        $new = clone $this;

        $new->applyComponent('host', $host);

        return $new;
    }

    public function withPort($port)
    {
        $new = clone $this;

        $new->applyComponent('port', $port);

        return $new;
    }

    public function withPath($path)
    {
        $new = clone $this;

        $new->applyComponent('path', $path);

        return $new;
    }

    public function withQuery($query)
    {
        $new = clone $this;

        $new->applyComponent('query', $query);

        return $new;
    }

    public function withFragment($fragment)
    {
        $new = clone $this;

        $new->applyComponent('fragment', $fragment);

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

        if ('' !== $scheme) {
            $uri .= $scheme.':';
        }

        if ('' !== $authority || 'file' === $scheme) {
            $uri .= '//'.$authority;
        }

        $uri .= '/'.ltrim($path, '/');

        if ('' !== $query) {
            $uri .= '?'.$query;
        }

        if ('' !== $fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    protected function composeUserInfo($user, $password = null)
    {
        return ('' !== $user && null !== $password && '' !== $password) ? $user.':'.$password : $user;
    }

    protected function applyComponent($name, $value) {
        if ('scheme' === $name && '' !== $value) {
            if (! static::isSchemeValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('userInfo' === $name && '' !== $value) {
            if (! static::isUserInfoValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('host' === $name && '' !== $value) {
            if (! static::isHostValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('port' === $name && null !== $value) {
            if (! static::isPortValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('path' === $name && '' !== $value) {
            if (! static::isPathValid($this, $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if (('query' === $name || 'fragment' === $name) && '' !== $value) {
            if (! static::isQueryOrFragmentValid($value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        $this->{$name} = $value;
    }

    protected function throwInvalidComponentException($name, $value)
    {
        throw new InvalidArgumentException("The {$name} component of the URI is not valid: {$value}!");
    }

    protected function encodePath($path)
    {
        return preg_replace_callback(
            '/(?:[^\/'.self::UNRESERVED.self::SUB_DELIMS.':@%]++|\%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $path
        );
    }

    protected function encodeQueryOrFragment($queryOrFragment)
    {
        return preg_replace_callback(
            '/(?:[^\/'.self::UNRESERVED.self::SUB_DELIMS.':@?%]++|\%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $queryOrFragment
        );
    }

    public static function isStandartPortForScheme($port, $scheme)
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }

    protected static function isSchemeValid($scheme)
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9+\-.]*$/', $scheme);
    }

    protected static function isUserInfoValid($userInfo)
    {
        return preg_match('/^(?:['.self::UNRESERVED.self::SUB_DELIMS.':]|\%[A-Fa-f0-9]{2})*$/', $userInfo);
    }

    protected static function isHostValid($host)
    {
        if (preg_match('/^\[.+\]$/', $host)) {
            $host = trim($host, '[]');

            if (0 === stripos($host, 'v')) {
                return false;
            }

            return false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }

        if (preg_match('/^(?:\d|[\x31-\x39]\d|1\d{2}|2[\x30-\x34]\d|25[\x30-\x35])\./', $host)) {
            return false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }

        return preg_match('/^(?:['.self::UNRESERVED.self::SUB_DELIMS.']|\%[A-Fa-f0-9]{2})*$/', $host);
    }

    protected static function isPortValid($port)
    {
        return (false !== filter_var($port, FILTER_VALIDATE_INT)) && (0 < $port && 65536 > $port);
    }

    protected static function isPathValid(UriInterface $base, $path)
    {
        if ('' !== $path) {
            $scheme = $base->getScheme();
            $authority = $base->getAuthority();

            if ('' !== $scheme && ':' === $path[0]) {
                return false;
            }

            if ('' !== $authority && '/' !== $path[0]) {
                return false;
            }

            if ('' === $authority && 0 === strpos($path, '//')) {
                return false;
            }
        }

        return preg_match('/^(?:[\/'.self::UNRESERVED.self::SUB_DELIMS.':@]|\%[A-Fa-f0-9]{2})*$/', $path);
    }

    protected static function isQueryOrFragmentValid($queryOrFragment)
    {
        return preg_match('/^(?:[\/'.self::UNRESERVED.self::SUB_DELIMS.':@?]|\%[A-Fa-f0-9]{2})*$/', $queryOrFragment);
    }
}
