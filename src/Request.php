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

    const HTTP_ACCEPT = 'Accept';
    const HTTP_ACCEPT_CHARSET = 'Accept-Charset';
    const HTTP_ACCEPT_ENCODING = 'Accept-Encoding';
    const HTTP_ACCEPT_LANGUAGE = 'Accept-Language';
    const HTTP_AUTHORIZATION = 'Authorization';
    const HTTP_CACHE_CONTROL = 'Cache-Control';
    const HTTP_EXPECT = 'Expect';
    const HTTP_FROM = 'From';
    const HTTP_HOST = 'Host';
    const HTTP_IF_MATCH = 'If-Match';
    const HTTP_IF_MODIFIED_SINCE = 'If-Modified-Since';
    const HTTP_IF_NONE_MATCH = 'If-None-Match';
    const HTTP_IF_RANGE = 'If-Range';
    const HTTP_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
    const HTTP_MAX_FORWARDS = 'Max-Forwards';
    const HTTP_PRAGMA = 'Pragma';
    const HTTP_PROXY_AUTHORIZATION = 'Proxy-Authorization';
    const HTTP_RANGE = 'Range';
    const HTTP_REFERER = 'Referer';
    const HTTP_TE = 'TE';
    const HTTP_USER_AGENT = 'User-Agent';

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
