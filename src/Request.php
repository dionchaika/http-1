<?php

namespace Lazy\Http;

use InvalidArgumentException;
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
    protected $method = 'GET';

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * The array of standart request methods.
     *
     * @var string[]
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4
     */
    protected static $standartMethods = [

        'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE'

    ];

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

        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        $this->uri = $uri;

        $this->setHostHeaderFromUri();
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget) {
            return (string) $this->requestTarget;
        }

        $requestTarget = '/'.ltrim($this->getUri()->getPath(), '/');

        $query = $this->getUri()->getQuery();

        return $query ? $requestTarget.'?'.$query : $requestTarget;
    }

    /**
     * {@inheritDoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;

        $new->requestTarget = $requestTarget;

        return $new;
    }

    /**
     * Apply a request method.
     *
     * @param string $method The request method.
     * @return void
     */
    protected function applyMethod($method)
    {
        if (! $this->isMethodValid($method)) {
            throw new InvalidArgumentException("Method is not valid: {$method}!");
        }

        $this->method = $method;
    }

    /**
     * Check is the request method valid.
     *
     * @param string $method The request method.
     * @return bool
     */
    protected function isMethodValid($method)
    {
        return in_array($method, static::$standartMethods) || preg_match('/^'.static::$token.'$/', $method);
    }

    /**
     * Set the "Host" header from the URI.
     *
     * @return void
     */
    protected function setHostHeaderFromUri()
    {
        $host = $this->getUri()->getHost();

        if ($host) {
            $port = $this->getUri()->getPort();

            if (null !== $port) {
                $host .= ':'.$port;
            }

            $this->headers = ['host' => ['name' => 'Host', 'value' => $host]] + $this->headers;
        }
    }
}
