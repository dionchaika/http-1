<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Lazy\Http\Contracts\MessageTrait;
use Psr\Http\Message\RequestInterface;

class Request implements RequestInterface
{
    use MessageTrait;

    /**
     * The request URI.
     *
     * @var UriInterface
     */
    protected $uri;

    /**
     * The request HTTP method.
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * The request target.
     *
     * @var mixed
     */
    protected $requestTarget;

    /**
     * Set "Host" header to the request from the URI.
     *
     * @param UriInterface $uri The URI instance.
     *
     * @return void
     */
    protected function setHostHeader(UriInterface $uri)
    {
        $host = $uri->getHost();

        if ('' !== $host) {
            $port = $uri->getPort();

            if (null !== $port) {
                $host .= ':'.$port;
            }

            $this->headers = ['host' => ['name' => 'Host', 'values' => [$host]]] + $this->headers;
        }
    }
}
