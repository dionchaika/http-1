<?php declare(strict_types = 1);

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * The array of URI components.
     *
     * @var array
     */
    protected $components = [

        'scheme' => '',
        'userInfo' => '',
        'host' => '',
        'port' => null,
        'path' => '',
        'query' => '',
        'fragment' => ''

    ];

    /**
     * The URI "file" scheme value.
     *
     * @var string
     */
    protected static $fileScheme = 'file';

    /**
     * The "RFC 3986" sub-delimiters.
     *
     * @var string
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * The "RFC 3986" unreserved characters.
     *
     * @var string
     */
    protected static $unreserved = 'A-Za-z0-9\-._~';

    /**
     * The array of standart TCP and UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

    /**
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return strtolower($this->components['scheme']);
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function getUserInfo()
    {
        return $this->components['userInfo'];
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return strtolower($this->components['host']);
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        $standart = $this->isStandartPort(
            $this->components['port'], $this->components['scheme']
        );

        return $standart ? null : $this->components['port'];
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withScheme($scheme)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withUserInfo($user, $password = null)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->composeComponents(
            $this->getScheme(),
            $this->getAuthority(),
            $this->getPath(),
            $this->getQuery(),
            $this->getFragment()
        );
    }

    /**
     * Is a TCP or UDP port standart
     * for the given URI scheme component.
     *
     * @param int|null $port TCP or UDP port.
     * @param string $scheme URI scheme component.
     *
     * @return bool
     */
    protected function isStandartPort($port, $scheme)
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }

    /**
     * Compose the URI user
     * information component into a single string.
     *
     * @param string $user URI user.
     * @param string|null $password URI password.
     *
     * @return string
     */
    protected function composeUserInfo($user, $password = null)
    {
        $userInfo = $user;

        if ('' !== $userInfo && ! empty($password)) {
            $userInfo .= ':'.$password;
        }

        return $userInfo;
    }

    /**
     * Compose all of the URI components into a single string.
     *
     * @param string $scheme URI scheme component.
     * @param string $authority URI authority.
     * @param string $path URI path component.
     * @param string $query URI query component.
     * @param string $fragment URI fragment component.
     *
     * @return string
     */
    protected function composeComponents($scheme, $authority, $path, $query, $fragment)
    {
        $uri = '';

        if ('' !== $scheme) {
            $uri .= $scheme.':';
        }

        if ('' !== $authority) {
            $uri .= '//'.$authority;
        } else if ($scheme === static::$fileScheme) {
            $uri .= '//';
        }

        if ($authority || 0 === strpos($path, '//')) {
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
}
