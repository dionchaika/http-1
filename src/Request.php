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
     * The array of standart HTTP methods.
     *
     * @var array
     */
    protected static $standartMethods = [

        'GET',
        'HEAD',
        'POST',
        'PUT',
        'DELETE',
        'CONNECT',
        'OPTIONS',
        'TRACE'

    ];

    /**
     * Create a new request instance.
     *
     * @param string $method Request HTTP method.
     * @param UriInterface|string $uri Request URI instance.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $method, $uri)
    {
        $this->validateMethod($method);

        $this->uri = $uri;
        $this->method = $method;

        $this->setHostHeaderFromUri($uri);
    }

    /**
     * Set "Host" header to the request from the URI.
     *
     * @param UriInterface $uri The URI instance.
     *
     * @return void
     */
    protected function setHostHeaderFromUri(UriInterface $uri)
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

    /**
     * Validate a request HTTP method.
     *
     * @param string $method Request HTTP method.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateMethod($method)
    {
        if (! static::isMethodValid($method)) {
            throw new InvalidArgumentException(
                "Request HTTP method is not valid: {$method}!"
            );
        }
    }

    /**
     * Is a request HTTP method valid.
     *
     * @param string $method Request HTTP method.
     *
     * @return bool
     */
    protected static function isMethodValid($method)
    {
        return in_array($method, static::$standartMethods) || preg_match(static::$headerName, $method);
    }
}
