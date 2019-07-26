<?php

namespace Lazy\Http;

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Psr\Http\Client\ClientInterface;

class Client implements ClientInterface
{
    /** @var array */
    protected static $defaultOptions = [

        'base_uri' => ''

    ];

    /**
     * Create a new remote socket connection.
     *
     * @param UriInterface $uri
     * @param array $options
     *
     * @return resource
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected static function getSocket(UriInterface $uri, array $options = [])
    {
        $host = $uri->getHost();

        if (0 === stripos($host, '[v')) {
            trigger_error('Address mechanism is not supported!', E_USER_ERROR);
        }
    }
}
