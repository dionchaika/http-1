<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;

/**
 * @see https://tools.ietf.org/html/rfc3986
 */
abstract class Rfc3986
{
    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.1
     */
    public static $pctEncoded = '\%[A-Fa-f0-9]{2}';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    public static $genDelims = ':\/?#[]@';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    public static $subDelims = '!$&\'()*+,;=';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    public static $unreserved = 'A-Za-z0-9\-._~';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    public static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';

    /**
     * Check is the scheme component of the URI valid.
     *
     * @param  string  $scheme  The scheme component of the URI.
     * @return bool
     */
    public static function isSchemeValid($scheme)
    {
        return preg_match(static::$schemePattern, $scheme);
    }

    /**
     * Check is the user information component of the URI valid.
     *
     * @param  string  $userInfo  The user information component of the URI.
     * @return bool
     */
    public static function isUserInfoValid($userInfo)
    {
        return preg_match(
            '/^(['.static::$unreserved.static::$subDelims.':]|'.static::$pctEncoded.')*$/', $userInfo
        );
    }

    /**
     * Check is the host component of the URI valid.
     *
     * @param  string  $host  The host component of the URI.
     * @return bool
     */
    public function isHostValid($host)
    {
        return preg_match('/^(['.static::$unreserved.static::$subDelims.']|'.static::$pctEncoded.')*$/', $host) ||
            static::isIpVFutureValid($host) || static::isIpV4AddressValid($host) || static::isIpV6AddressValid($host);
    }

    /**
     * Check is the "IPvFuture" of the host component of the URI valid.
     *
     * @param  string  $ip  The "IPvFuture" of the host component of the URI
     * @return bool
     */
    public static function isIpVFutureValid($ip)
    {
        return preg_match(
            '/^\[v[A-Fa-f0-9]\.['.static::$unreserved.static::$subDelims.':]\]$/i', $ip
        );
    }

    /**
     * Check is the "IPv4address" of the host component of the URI valid.
     *
     * @param  string  $ip  The "IPv4address" of the host component of the URI
     * @return bool
     */
    public static function isIpV4AddressValid($ip)
    {
        return false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Check is the "IPv6address" of the host component of the URI valid.
     *
     * @param  string  $ip  The "IPv6address" of the host component of the URI
     * @return bool
     */
    public static function isIpV6AddressValid($ip)
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
    public function isPortValid($port)
    {
        return 0 < $port && 65536 > $port;
    }

    /**
     * Check is the path component of the URI valid.
     *
     * @param  string  $path  The path component of the URI.
     * @param  \Psr\Http\Message\UriInterface|null  $uri  The URI instance.
     * @return bool
     */
    public function isPathValid($path, UriInterface $uri = null)
    {
        if ($uri && $path) {
            if (! $uri->getScheme() && ':' === $path[0]) {
                trigger_error(
                    'The path component of the URI without a scheme cannot begin with a colon', E_USER_WARNING
                );

                return false;
            }

            if (! $uri->getAuthority() && 0 === strpos($path, '//')) {
                trigger_error(
                    'The path component of the URI without an authority cannot begin with two slashes.', E_USER_WARNING
                );

                return false;
            }

            if ($uri->getAuthority() && 0 !== strpos($path, '/')) {
                trigger_error(
                    'The path component of the URI with an authority must be empty or begin with a slash.', E_USER_WARNING
                );

                return false;
            }
        }

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
    public function isQueryOrFragmentValid($queryOrFragment)
    {
        return preg_match(
            '/^(['.static::$unreserved.static::$subDelims.':@\/?]|'.static::$pctEncoded.')*$/', $queryOrFragment
        );
    }
}
