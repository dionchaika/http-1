<?php

namespace Lazy\Http;

use Throwable;
use Lazy\Http\Contracts\UriTrait;
use Psr\Http\Message\UriInterface;

/**
 * {@inheritDoc}
 */
class Uri implements UriInterface
{
    use UriTrait;

    /**
     * @var string
     */
    protected $scheme = '';

    /**
     * @var string
     */
    protected $userInfo = '';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var string
     */
    protected $fragment = '';

    /**
     * The URI constructor.
     *
     * @param  string  $scheme  The URI scheme.
     * @param  string  $user  The URI user.
     * @param  string|null  $password  The URI password.
     * @param  string  $host  The URI host.
     * @param  int|null  $port  The URI port.
     * @param  string  $path  The URI path.
     * @param  string  $query  The URI query.
     * @param  string  $fragment  The URI fragment.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($scheme = '', $user = '', $password = null, $host = '', $port = null, $path = '', $query = '', $fragment = '')
    {
        $userInfo = $user;

        if ($userInfo && $password) {
            $userInfo .= ':'.$password;
        }

        $this->scheme = $this->validateScheme($scheme);
        $this->userInfo = $userInfo;
        $this->host = $this->validateHost($host);
        $this->port = $this->validatePort($port);
        $this->path = $this->validatePath($path);
        $this->query = $this->validateQuery($query);
        $this->fragment = $this->validateFragment($fragment);
    }

    /**
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthority()
    {
        $authority = $this->host;

        if ($authority) {
            if ($this->userInfo) {
                $authority = $this->userInfo.'@'.$authority;
            }

            if (null !== $this->port) {
                $authority .= ':'.$this->port;
            }
        }

        return $authority;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritDoc}
     */
    public function withScheme($scheme)
    {
        $new = clone $this;

        $new->scheme = $new->validateScheme($scheme);
        $new->port = static::isStandartPort($new->scheme, $new->port) ? null : $new->port;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $userInfo = $user;

        if ($userInfo && $password) {
            $userInfo .= ':'.$password;
        }

        $new = clone $this;

        $new->userInfo = $userInfo;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        $new = clone $this;

        $new->host = $new->validateHost($host);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        $new = clone $this;

        $new->port = $new->validatePort($port);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        $new = clone $this;

        $new->path = $new->validatePath($path);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        $new = clone $this;

        $new->query = $new->validateQuery($query);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        $new = clone $this;

        $new->fragment = $new->validateFragment($fragment);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        try {
            $uri = '';

            if ($this->scheme) {
                $uri .= $this->scheme.':';
            }

            $authority = $this->getAuthority();

            if ($authority) {
                $uri .= '//'.$authority;
            }

            $path = $this->path;

            if ($authority && 0 !== strpos($path, '/')) {
                $path = '/'.$path;
            } else if (! $authority && 0 === strpos($this->path, '//')) {
                $path = '/'.ltrim($path, '/');
            }

            $uri .= $path;

            if ($this->query) {
                $uri .= '?'.$this->query;
            }

            if ($this->fragment) {
                $uri .= '#'.$this->fragment;
            }

            return $uri;
        } catch (Throwable $e) {
            trigger_error($e->getMessage(), \E_USER_ERROR);
        }
    }
}
