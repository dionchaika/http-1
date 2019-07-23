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

        $this->scheme = static::validateSchemeComponent(! empty($components['scheme']) ? $components['scheme'] : '');
        $this->userInfo = static::validateUserInfoComponent($userInfo);
        $this->host = static::validateHostComponent(! empty($components['host']) ? $components['host'] : '');
        $this->port = static::validatePortComponent(! empty($components['port']) ? $components['port'] : null);
        $this->path = static::validatePathComponent(! empty($components['path']) ? $components['path'] : '');
        $this->query = static::validateQueryOrFragmentComponent(! empty($components['query']) ? $components['query'] : '');
        $this->fragment = static::validateQueryOrFragmentComponent(! empty($components['fragment']) ? $components['fragment'] : '');
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
        return static::isStandartPort($this->port, $this->scheme) ? null : $this->port;
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

        $new->scheme = static::validateSchemeComponent($scheme);

        return $new;
    }

    public function withUserInfo($user, $password = null)
    {
        $new = clone $this;

        $new->userInfo = static::validateUserInfoComponent(
            $this->composeUserInfo($user, $password)
        );

        return $new;
    }

    public function withHost($host)
    {
        $new = clone $this;

        $new->host = static::validateHostComponent($host);

        return $new;
    }

    public function withPort($port)
    {
        $new = clone $this;

        $new->port = static::validatePortComponent($port);

        return $new;
    }

    public function withPath($path)
    {
        $new = clone $this;

        $new->path = static::validatePathComponent($path);

        return $new;
    }

    public function withQuery($query)
    {
        $new = clone $this;

        $new->query = static::validateQueryOrFragmentComponent($query);

        return $new;
    }

    public function withFragment($fragment)
    {
        $new = clone $this;

        $new->fragment = static::validateQueryOrFragmentComponent($fragment);

        return $new;
    }

    public function __toString()
    {
        return static::composeComponents(
            $this->getScheme(),
            $this->getAuthority(),
            $this->getPath(),
            $this->getQuery(),
            $this->getFragment()
        );
    }

    protected function composeUserInfo($user, $password = null)
    {
        return ('' !== $user && null !== $password && '' !== $password) ? $user.':'.$password : $user;
    }
}
