<?php declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
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
     * The "file" URI scheme component value.
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
        $standart = static::isStandartPort(
            $this->components['port'],
            $this->components['scheme']
        );

        return $standart ? null : $this->components['port'];
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return static::encodePath($this->components['path']);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return static::encodeQueryOrFragment($this->components['query']);
    }

    /**
     * {@inheritDoc}
     */
    public function getFragment()
    {
        return static::encodeQueryOrFragment($this->components['fragment']);
    }

    /**
     * {@inheritDoc}
     */
    public function withScheme($scheme)
    {
        $new = clone $this;
        $new->components['scheme'] = static::filterScheme($scheme);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $new = clone $this;

        $new->components['userInfo'] = static::filterUserInfo(
            static::composeUserInfo($user, $password)
        );

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        $new = clone $this;
        $new->components['host'] = static::filterHost($host);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        $new = clone $this;
        $new->components['port'] = static::filterPort($port);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withPath($path)
    {
        $new = clone $this;
        $new->components['path'] = static::filterPath($path, $this);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withQuery($query)
    {
        $new = clone $this;
        $new->components['query'] = static::filterQueryOrFragment($query);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withFragment($fragment)
    {
        $new = clone $this;
        $new->components['fragment'] = static::filterQueryOrFragment($fragment);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * Is a TCP or UDP port standart
     * for the given URI scheme component.
     *
     * @param int|null $port TCP or UDP port.
     * @param string $scheme URI scheme component.
     *
     * @return bool
     */
    protected static function isStandartPort($port, $scheme)
    {
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
    }

    /**
     * Encode a URI path component.
     *
     * @param string $path URI path component.
     *
     * @return string
     */
    protected static function encodePath($path)
    {

    }

    /**
     * Encode a URI query or fragment component.
     *
     * @param string $queryOrFragment URI query or fragment component.
     *
     * @return string
     */
    protected static function encodeQueryOrFragment($queryOrFragment)
    {

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
    protected static function composeUserInfo($user, $password = null)
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
    protected static function composeComponents($scheme, $authority, $path, $query, $fragment)
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
     * Filter a URI scheme component.
     *
     * @param string $scheme URI scheme component.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function filterScheme($scheme)
    {
        if ('' !== $scheme) {
            if (! preg_match('/^[A-Za-z][A-Za-z0-9+\-.]*$/', $scheme)) {
                throw new InvalidArgumentException(
                    "The URI scheme component is not valid: {$scheme}!"
                );
            }
        }

        return $scheme;
    }

    /**
     * Filter a URI user information component.
     *
     * @param string $userInfo URI user information component.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function filterUserInfo($userInfo)
    {
        if ('' !== $userInfo) {
            if (! preg_match('/^(?:['.static::$unreserved.static::$subDelims.':]|\%[A-Fa-f0-9]{2})*$/', $userInfo)) {
                throw new InvalidArgumentException(
                    "The URI user information component is not valid: {$userInfo}!"
                );
            }
        }

        return $userInfo;
    }

    /**
     * Filter a URI host component.
     *
     * @param string $host URI host component.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function filterHost($host)
    {

    }

    /**
     * Filter a URI port component.
     *
     * @param int|null $port URI port component.
     *
     * @return int|null
     *
     * @throws InvalidArgumentException
     */
    protected static function filterPort($port)
    {
        if (null !== $port) {
            $port = filter_var($port, FILTER_VALIDATE_INT, [

                'options' => [

                    'min_range' => 1,
                    'max_range' => 65535

                ],
                'flags' => FILTER_FLAG_ALLOW_HEX | FILTER_FLAG_ALLOW_OCTAL

            ]);

            if (false === $port) {
                throw new InvalidArgumentException(
                    "The URI port component is not valid: {$port}! "
                    ."TCP or UDP port must be in the range from 1 to 65535."
                );
            }
        }

        return $port;
    }

    /**
     * Filter a URI path component.
     *
     * @param string $path URI path component.
     * @param UriInterface $uri Current URI instance.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function filterPath($path, UriInterface $uri)
    {

    }

    /**
     * Filter a URI query or fragment component.
     *
     * @param string $queryOrFragment URI query or fragment component.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function filterQueryOrFragment($queryOrFragment)
    {
        if ('' !== $queryOrFragment) {
            if (! preg_match('/^(?:[\/'.static::$unreserved.static::$subDelims.':@?]|\%[A-Fa-f0-9]{2})*$/', $queryOrFragment)) {
                throw new InvalidArgumentException(
                    "The URI query or fragment component is not valid: {$queryOrFragment}!"
                );
            }
        }

        return $queryOrFragment;
    }
}
