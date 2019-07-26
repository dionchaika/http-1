<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;

class Request extends Message implements RequestInterface
{
    const HOST = 'Host';

    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_TRACE = 'TRACE';
    const METHOD_DELETE = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';

    /** @var array */
    protected static $standartMethods = [

        'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE'

    ];

    /** @var UriInterface */
    protected $uri;

    /** @var string */
    protected $method;

    /** @var mixed */
    protected $requestTarget;

    /**
     * Filter an HTTP method.
     *
     * @param string $method
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function filterMethod($method)
    {
        if (
            in_array($method, static::$standartMethods) ||
            preg_match(self::TOKEN, $method)
        ) {
            return $method;
        }

        throw new InvalidArgumentException("Invalid HTTP method: {$method}!");
    }

    /**
     * Get the "Host" header value from the given URI.
     *
     * @param UriInterface $uri
     * @return string
     */
    protected static function getHost(UriInterface $uri)
    {
        $host = $uri->getHost();

        if ('' !== $host) {
            $port = $uri->getPort();

            if (null !== $port) {
                $host .= ':'.$port;
            }
        }

        return $host;
    }

    /**
     * Initializes a new request instance.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @throws InvalidArgumentException
     */
    public function __construct($method, $uri)
    {
        $this->method = static::filterMethod($method);

        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        $this->uri = $uri;
        $this->setHost(static::getHost($uri));
    }

    public function getRequestTarget()
    {
        if (null !== $this->requestTarget) {
            return (string) $this->requestTarget;
        }

        $uri = $this->getUri();
        $query = $uri->getQuery();

        $requestTarget = '/'.ltrim($uri->getPath(), '/');

        if ('' !== $query) {
            $requestTarget .= '?'.$query;
        }

        return $requestTarget;
    }

    public function withRequestTarget($requestTarget)
    {
        $request = clone $this;
        $request->requestTarget = $requestTarget;

        return $request;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $request = clone $this;
        $request->method = static::filterMethod($method);

        return $request;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $request = clone $this;
        $request->uri = $uri;

        if (! $preserveHost || '' === $this->getHeaderLine(self::HOST)) {
            $request->setHost(static::getHost($uri));
        }

        return $request;
    }

    public function withHeader($name, $value)
    {
        if (0 === strcasecmp($name, self::HOST)) {
            $request = clone $this;
            $request->setHost($value);

            return $request;
        }

        return parent::withHeader($name, $value);
    }

    public function withAddedHeader($name, $value)
    {
        if (0 === strcasecmp($name, self::HOST)) {
            $request = clone $this;
            $request->setHost($value);

            return $request;
        }

        return parent::withAddedHeader($name, $value);
    }

    /**
     * Set the "Host" header to the request.
     *
     * @param string $host
     * @return void
     */
    protected function setHost($host)
    {
        if ('' !== $host) {
            $this->headers = [

                'host' => [

                    'name' => self::HOST,
                    'values' => static::filterHeaderValue($host)

                ]

            ] + $this->headers;
        }
    }
}
