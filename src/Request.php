<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Lazy\Http\Contracts\MessageTrait;
use Psr\Http\Message\RequestInterface;

class Request implements RequestInterface
{
    use MessageTrait;

    /** @var mixed */
    protected $requestTarget;

    /** @var string */
    protected $method = 'GET';

    /** @var UriInterface */
    protected $uri;

    /** @var array */
    protected static $standartMethods = [

        'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE'

    ];

    /**
     * Create a new request instance.
     *
     * @param string $method The request method.
     * @param UriInterface|string $uri The request URI.
     */
    public function __construct(string $method, $uri)
    {
        $this->validateMethod($method);

        $this->method = $method;

        if (is_string($uri)) {
            //
        }

        $this->uri = $uri;

        if (! $this->hasHeader('host')) {
            $this->setHostHeader($uri);
        }
    }

    public function getRequestTarget()
    {
        if ($this->requestTarget) {
            return (string) $this->requestTarget;
        }

        $uri = $this->getUri();

        $requestTarget = '/'.ltrim($uri->getPath());

        $query = $uri->getQuery();

        return $query ? $requestTarget.'?'.$query : $requestTarget;
    }

    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;

        $new->requestTarget = $requestTarget;

        return $new;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $new = clone $this;

        $new->method = $method;

        return $new;
    }

    public function getUri()
    {
        if (! $this->uri) {
            //
        }

        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;

        $new->uri = $uri;

        if ($preserveHost && $this->getHeaderLine('host')) {
            return $new;
        }

        $new->setHostHeader($uri);

        return $new;
    }

    /**
     * Set the "Host" header field from the URI.
     *
     * @param UriInterface $uri
     * @return void
     */
    protected function setHostHeader(UriInterface $uri)
    {
        $host = $uri->getHost();

        if ($host) {
            $port = $uri->getPort();

            if (null !== $port) {
                $host .= ':'.$port;
            }

            $this->headers = ['host' => ['name' => 'Host', 'value' => [$host]]] + $this->headers;
        }
    }

    /**
     * Validate a request method.
     *
     * @param string $method
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateMethod($method)
    {
        if (! in_array($method, static::$standartMethods) && ! preg_match('/^'.static::$token.'$/', $method)) {
            throw new InvalidArgumentException("Method is not valid: {$method}!");
        }
    }
}
