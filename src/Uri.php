<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    const SUB_DELIMS = '!$&\'()*+,;=';
    const UNRESERVED = 'A-Za-z0-9\-._~';

    /** @var array */
    protected static $standartPorts = [

        'http' => 80,
        'https' => 443

    ];

    /** @var string */
    protected $scheme = '';

    /** @var string */
    protected $user = '';

    /** @var string|null */
    protected $password;

    /** @var string */
    protected $host = '';

    /** @var int|null */
    protected $port;

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $query = '';

    /** @var string */
    protected $fragment = '';

    /**
     * Is the TCP or UDP port
     * standart for the given URI scheme component.
     *
     * @param int|null $port
     * @param string $scheme
     *
     * @return bool
     */
    protected static function isStandartPort($port, $scheme)
    {
        return
            isset(static::$standartPorts[$scheme]) &&
            $port === static::$standartPorts[$scheme];
    }

    /**
     * Percent-encode a URI path component.
     * This method DOES NOT double-encode any characters.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @param string $path
     * @return string
     */
    protected static function percentEncodePath($path)
    {
        $pattern = '/(?:[\/'.self::UNRESERVED.self::SUB_DELIMS.':@%]++|\%(?![A-Za-z0-9]{2}))/';

        return preg_replace_callback(
            $pattern,
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $path
        );
    }

    /**
     * Percent-encode a URI query or fragment component.
     * This method DOES NOT double-encode any characters.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @param string $queryOrFragment
     * @return string
     */
    protected static function percentEncodeQueryOrFragment($queryOrFragment)
    {
        $pattern = '/(?:[\/'.self::UNRESERVED.self::SUB_DELIMS.':@?%]++|\%(?![A-Za-z0-9]{2}))/';

        return preg_replace_callback(
            $pattern,
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $queryOrFragment
        );
    }

    /**
     * Filter a URI port component.
     *
     * @param int|null $port
     * @return int|null
     * @throws InvalidArgumentException
     */
    protected static function filterPort($port)
    {
        if (null === $port || (is_int($port) && 0 < $port && 65536 > $port)) {
            return $port;
        }

        trigger_error('TCP or UDP port MUST BE between 1 and 65535.');
        throw new InvalidArgumentException("Invalid URI port component: {$port}!");
    }

    /**
     * Normalize a URI path component
     * depending on the authority component.
     *
     * @param string $path
     * @param string $authority
     *
     * @return string
     */
    protected static function normalizePath($path, $authority)
    {
        if ('' !== $path && '/' !== $path[0] && '' !== $authority) {
            $path = '/'.$path;
        }

        if ('' !== $path && '/' === $path && isset($path[1]) && '/' === $path[1] && '' === $authority) {
            $path = '/'.ltrim($path, '/');
        }
    }

    /**
     * Initializes a new URI instance.
     *
     * @param string $uri
     * @throws InvalidArgumentException
     */
    public function __construct($uri = '')
    {
        $components = parse_url($uri);

        if (false === $components) {
            throw new InvalidArgumentException("Unable to parse the URI: {$uri}!");
        }

        if (isset($components['scheme'])) {
            $this->scheme = $components['scheme'];
        }

        if (isset($components['user'])) {
            $this->user = $components['user'];
        }

        if (isset($components['pass'])) {
            $this->password = $components['pass'];
        }

        if (isset($components['host'])) {
            $this->host = $components['host'];
        }

        if (isset($components['port'])) {
            $this->port = static::filterPort($components['port']);
        }

        if (isset($components['path'])) {
            $this->path = $components['path'];
        }

        if (isset($components['query'])) {
            $this->query = $components['query'];
        }

        if (isset($components['fragment'])) {
            $this->fragment = $components['fragment'];
        }
    }

    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    public function getAuthority()
    {
        $authority = $this->getHost();

        if ('' !== $authority) {
            $userInfo = $this->getUserInfo();
            $port = $this->getPort();

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
        $userInfo = $this->user;

        if (
            '' !== $userInfo &&
            null !== $this->password &&
            '' !== $this->password
        ) {
            $userInfo .= ':'.$this->password;
        }

        return $userInfo;
    }

    public function getHost()
    {
        return strtolower($this->host);
    }

    public function getPort()
    {
        $standart = static::isStandartPort(
            $this->port,
            $this->getScheme()
        );

        return $standart ? null : $this->port;
    }

    public function getPath()
    {
        return static::percentEncodePath($this->path);
    }

    public function getQuery()
    {
        return static::percentEncodeQueryOrFragment($this->query);
    }

    public function getFragment()
    {
        return static::percentEncodeQueryOrFragment($this->fragment);
    }

    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->scheme = $scheme;

        return $uri;
    }

    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;

        $uri->user = $user;
        $uri->password = $password;

        return $uri;
    }

    public function withHost($host)
    {
        $uri = clone $this;
        $uri->host = $host;

        return $uri;
    }

    public function withPort($port)
    {
        $uri = clone $this;
        $uri->port = static::filterPort($port);

        return $uri;
    }

    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = $path;

        return $uri;
    }

    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = $query;

        return $uri;
    }

    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = $fragment;

        return $uri;
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

        if ('' !== $authority) {
            $uri .= '//'.$authority;
        }

        $uri .= static::normalizePath($path, $authority);

        if ('' !== $query) {
            $uri .= '?'.$query;
        }

        if ('' !== $fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }
}
