<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    const TOKEN = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';
    const FIELD_VALUE = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r\n[ \t]+)*[ \t]*$/';

    /** @var StreamInterface */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion = '1.1';

    /**
     * Filter an HTTP header name.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function filterHeaderName($name)
    {
        if (preg_match(self::TOKEN, $name)) {
            return $name;
        }

        throw new InvalidArgumentException("Invalid HTTP header name: {$name}!");
    }

    /**
     * Filter an HTTP header value(s).
     *
     * @param string|string[] $value
     * @return string[]
     * @throws InvalidArgumentException
     */
    protected static function filterHeaderValue($value)
    {
        $values = (array) $value;

        foreach ($values as $value) {
            if (! preg_match(self::FIELD_VALUE, $value)) {
                throw new InvalidArgumentException("Invalid HTTP header value: {$value}!");
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
        if (null === $this->body) {
            $factory = new StreamFactory();
            $this->body = $factory->createStream();
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $this->body = $body;
    }
}
