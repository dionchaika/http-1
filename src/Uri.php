<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Lazy\Http\Contracts\UriTrait;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    use UriTrait;

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
}
