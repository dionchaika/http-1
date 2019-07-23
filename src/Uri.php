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

    public static function isStandartPortForScheme($port, $scheme)
    {
        return isset(static::$standartPorts) && $port === static::$standartPorts[$scheme];
    }

    protected function composeUserInfo($user, $password = null)
    {
        return ('' !== $user && null !== $password && '' !== $password) ? $user.':'.$password : $user;
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
        return 0 < $port && 65536 > $port;
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
