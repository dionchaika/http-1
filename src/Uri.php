<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * The scheme component of the URI.
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * The user information component of the URI.
     *
     * @var string
     */
    protected $userInfo = '';

    /**
     * The host component of the URI.
     *
     * @var string
     */
    protected $host = '';

    /**
     * The port component of the URI.
     *
     * @var int|null
     */
    protected $port;

    /**
     * The path component of the URI.
     *
     * @var string
     */
    protected $path = '';

    /**
     * The query component of the URI.
     *
     * @var string
     */
    protected $query = '';

    /**
     * The fragment component of the URI.
     *
     * @var string
     */
    protected $fragment = '';

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
    protected static $unreserved  = 'A-Za-z0-9\-._~';

    /**
     * The array of standart TCP or UDP ports.
     *
     * @var array
     */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

    /**
     * Create a new URI instance.
     *
     * @param string $uri The URI string.
     *
     * @throws \InvalidArgumentException If unable to parse
     *      the URI string or a component of the URI is not valid.
     */
    public function __construct(string $uri = '')
    {
        $components = parse_url($uri);

        if (false === $components) {
            throw new InvalidArgumentException("Unable to parse the URI string: {$uri}!");
        }

        //
    }

    /**
     * {@inheritDoc}
     */
    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        $authority = $host;

        if ($authority) {
            if ($userInfo) {
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
        return $this->userInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return strtolower($this->host);
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        if (static::isStandartPortForScheme($this->port, $this->scheme)) {
            return null;
        }

        return $this->port;
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
     * Is the TCP or UDP port standart
     * for the given scheme component of the URI.
     *
     * @param int $port The TCP or UDP port.
     * @param string $scheme The scheme component of the URI.
     *
     * @return bool Returns true
     *      if the TCP or UDP port standart
     *      for the given scheme component of the URI.
     */
    public static function isStandartPortForScheme($port, $scheme = 'http')
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }

    /**
     * Is the scheme component of the URI valid.
     *
     * @param string $scheme The scheme component of the URI.
     *
     * @return bool Returns true if the scheme component of the URI valid.
     */
    protected static function isSchemeValid($scheme)
    {
        
    }

    /**
     * Is the host component of the URI valid.
     *
     * @param string $host The host component of the URI.
     *
     * @return bool Returns true if the host component of the URI valid.
     */
    protected static function isHostValid($host)
    {
        
    }

    /**
     * Is the port component of the URI valid.
     *
     * @param int $port The port component of the URI.
     *
     * @return bool Returns true if the port component of the URI valid.
     */
    protected static function isPortValid($port)
    {
        
    }

    /**
     * Is the path component of the URI valid.
     *
     * @param string $path The path component of the URI.
     * @param UriInterface|null $uri The URI instance for additional validation.
     *
     * @return bool Returns true if the path component of the URI valid.
     */
    protected static function isPathValid($path, UriInterface $uri = null)
    {
        
    }

    /**
     * Is the query or fragment component of the URI valid.
     *
     * @param string $scheme The query or fragment component of the URI.
     *
     * @return bool Returns true if the query or fragment component of the URI valid.
     */
    protected static function isQueryOrFragmentValid($queryOrFragment)
    {
        
    }
}
