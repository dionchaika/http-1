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
        'pass' => null,
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
            null !== $this->components['pass'] &&
            '' !== $this->components['pass']
        ) {
            $userInfo .= ':'.$this->components['pass'];
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
}
