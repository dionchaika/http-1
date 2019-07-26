<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string */
    protected static $subDelims = '';

    /** @var string */
    protected static $unreserved = '';

    /** @var array */
    protected static $standartPorts = ['http' => 80, 'https' => 443];

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
     * Determine is the TCP or UDP port standart for the given scheme.
     *
     * @param int|null $port
     * @param string $scheme
     *
     * @return bool
     */
    protected static function isStandartPort($port, $scheme)
    {
        $scheme = strtolower($scheme);
        return isset(static::$standartPorts[$scheme]) && $port === static::$standartPorts[$scheme];
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
}
