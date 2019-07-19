<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;

class Request implements RequestInterface
{
    use Message;

    /**
     * @var mixed
     */
    protected $requestTarget;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * Create a new request instance.
     *
     * @param string $method The request method.
     * @param UriInterface|string $uri The request URI.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($method, $uri)
    {
        $this->applyMethod($method);

        $this->uri = ($uri instanceof UriInterface) ? $uri : new Uri($uri);

        $this->setHostHeaderFromUri();
    }

    /**
     * Set the "Host" header from the URI.
     *
     * @return void
     */
    protected function setHostHeaderFromUri()
    {
        $host = $this->uri->getHost();

        if ($host) {
            $port = $this->uri->getPort();

            if (null !== $port) {
                $host .= ':'.$port;
            }

            $this->headers = ['host' => ['name' => 'Host', 'value' => $host]] + $this->headers;
        }
    }
}
