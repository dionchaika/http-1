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

    public static function isStandartPortForScheme($port, $scheme)
    {
        return isset(static::$standartPorts) && $port === static::$standartPorts[$scheme];
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
