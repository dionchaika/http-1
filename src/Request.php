<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;

class Request implements RequestInterface
{
    use Message;

    protected $requestTarget;
    protected $method;
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
    }
}
