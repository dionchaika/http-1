<?php

declare(strict_types=1);

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /** @var string @see https://tools.ietf.org/html/rfc7230#section-3.2.6 */
    protected static $token = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /** @var string @see https://tools.ietf.org/html/rfc7230#section-3.2 */
    protected static $header = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r\n[ \t]+)*[ \t]*$/';

    /** @var StreamInterface */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion = '1.1';

    /**
     * Filter an HTTP header name according to "RFC 7230".
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function filterHeaderName($name)
    {
        if (preg_match(static::$token, $name)) {
            return $name;
        }

        throw new InvalidArgumentException("HTTP header name is not valid: {$name}!");
    }

    /**
     * Filter an HTTP header value according to "RFC 7230".
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @param string|string[] $value
     * @return string[]
     * @throws InvalidArgumentException
     */
    protected static function filterHeaderValue($value)
    {
        $values = (array) $value;

        foreach ($values as $value) {
            if (! preg_match(static::$header, $value)) {
                throw new InvalidArgumentException("HTTP header value is not valid: {$value}!");
            }
        }

        return $values;
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $message = clone $this;
        $message->protocolVersion = $version;

        return $message;
    }

    public function getHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['values'];
        }

        return $headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name)
    {
        $name = strtolower($name);

        return isset($this->headers[$name]) ? $this->headers[$name]['values'] : [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $name = static::filterHeaderName($name);
        $values = static::filterHeaderValue($value);

        $message = clone $this;
        $message->headers[strtolower($name)] = compact('name', 'values');

        return $message;
    }

    public function withAddedHeader($name, $value)
    {
        $name = static::filterHeaderName($name);
        $values = static::filterHeaderValue($value);

        $normalizedName = strtolower($name);

        $message = clone $this;

        if (isset($message->headers[$normalizedName])) {
            $message->headers[$normalizedName]['values'] += $values;
        } else {
            $message->headers[$normalizedName] = compact('name', 'values');
        }

        return $message;
    }

    public function withoutHeader($name)
    {
        $message = clone $this;
        unset($message->headers[strtolower($name)]);

        return $message;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $message = clone $this;
        $message->body = $body;

        return $message;
    }
}
