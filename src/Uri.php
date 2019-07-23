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
        return '' === $scheme || preg_match('/^[A-Za-z][A-Za-z0-9+\-.]*$/', $scheme);
    }

    protected static function isUserInfoValid($userInfo)
    {
        return '' === $userInfo || preg_match('/^(?:['.self::UNRESERVED.self::SUB_DELIMS.':]|\%[A-Fa-f0-9]{2})*$/', $userInfo);
    }

    protected static function isHostValid($host)
    {
        
    }
}
