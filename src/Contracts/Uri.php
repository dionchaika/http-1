<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

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

    public function getScheme()
    {
        return $this->components['scheme'];
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
        return $this->components['host'];
    }

    public function getPort()
    {
        return $this->components['port'];
    }

    public function getPath()
    {
        return $this->components['path'];
    }

    public function getQuery()
    {
        return $this->components['query'];
    }

    public function getFragment()
    {
        return $this->components['fragment'];
    }

    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->components['scheme'] = $scheme;

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
        $uri->components['host'] = $host;

        return $uri;
    }

    public function withPort($port)
    {
        $uri = clone $this;
        $uri->components['port'] = $port;

        return $uri;
    }

    public function withPath($path)
    {
        $uri = clone $this;
        $uri->components['path'] = $path;

        return $uri;
    }

    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->components['query'] = $query;

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

        $uri .= $path;

        if ('' !== $query) {
            $uri .= '?'.$query;
        }

        if ('' !== $fragment) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }
}
