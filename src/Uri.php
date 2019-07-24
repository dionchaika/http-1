<?php

declare(strict_types=1);

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;

use function Lazy\Http\filter_uri_host;
use function Lazy\Http\filter_uri_port;
use function Lazy\Http\filter_uri_path;
use function Lazy\Http\filter_uri_query;
use function Lazy\Http\filter_uri_scheme;
use function Lazy\Http\rawurlencode_path;
use function Lazy\Http\rawurlencode_query;

class Uri implements UriInterface
{
    /** @var array */
    protected $components = [

        'scheme' => '',
        'user' => '',
        'password' => null,
        'host' => '',
        'port' => null,
        'path' => '',
        'query' => '',
        'fragment' => ''

    ];

    /** @var array */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

    public function getScheme()
    {
        return strtolower($this->components['scheme']);
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
        $userInfo = $this->components['user'];

        if (
            '' !== $userInfo &&
            null !== $this->components['password'] &&
            '' !== $this->components['password']
        ) {
            $userInfo .= ':'.$this->components['password'];
        }

        return $userInfo;
    }

    public function getHost()
    {
        return strtolower($this->components['host']);
    }

    public function getPort()
    {
        $standart = static::isStandartPort(
            $this->components['port'],
            $this->components['scheme']
        );

        return $standart ? null : $this->components['port'];
    }

    public function getPath()
    {
        return rawurlencode_path($this->components['path']);
    }

    public function getQuery()
    {
        return rawurlencode_query($this->components['query']);
    }

    public function getFragment()
    {
        return rawurlencode_query($this->components['fragment']);
    }

    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->components['scheme'] = filter_uri_scheme($scheme);

        return $uri;
    }

    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;

        $uri->components['user'] = $user;
        $uri->components['password'] = $password;

        return $uri;
    }

    public function withHost($host)
    {
        $uri = clone $this;
        $uri->components['host'] = filter_uri_host($host);

        return $uri;
    }

    public function withPort($port)
    {
        $uri = clone $this;
        $uri->components['port'] = filter_uri_port($port);

        return $uri;
    }

    public function withPath($path)
    {
        $uri = clone $this;
        $uri->components['path'] = filter_uri_path($path);

        return $uri;
    }

    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->components['query'] = filter_uri_query($query);

        return $uri;
    }

    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->components['fragment'] = $fragment;

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

        if ('' !== $authority || 0 === strpos($path, '//')) {
            $path = '/'.ltrim($path, '/');
        }

        $uri .= $path;

        if ('' !== $query) {
            $uri .= '?'.$query;
        }

        if ('' !== $fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * Is a TCP or UDP port standart for the given URI scheme component.
     *
     * @param int|null $port
     * @param string $scheme
     * @return bool
     */
    protected static function isStandartPort($port, $scheme)
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }
}
