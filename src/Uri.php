<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    protected $scheme = '';
    protected $userInfo = '';
    protected $host = '';
    protected $port;
    protected $path = '';
    protected $query = '';
    protected $fragment = '';

    protected static $standartPorts = [

        'http' => 80, 'https' => 443

    ];

    protected static $subDelims = '!$&\'()*+,;=';

    protected static $unreserved = 'A-Za-z0-9\-._~';

    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    public function getAuthority()
    {
        $authority = $this->getHost();

        if ($authority) {
            if ($this->userInfo) {
                $authority = $this->userInfo.'@'.$authority;
            }

            $port = $this->getPort();

            if ($port) {
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
         return $this->isStandartPort($this->port, $this->scheme) ? null : $this->port;
    }

    /**
     * Is the TCP or UDP port is standart for the given scheme component.
     *
     * @param int $port
     * @param string $scheme
     * @return bool
     */
    protected function isStandartPort($port, $scheme)
    {
        return isset(static::$standartPorts[$port]) && $port === static::$standartPorts[$scheme];
    }
}
