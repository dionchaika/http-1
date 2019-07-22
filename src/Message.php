<?php

namespace Lazy\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    /** @var string The protocol version of the message. */
    protected $protocolVersion = '1.1';

    /** @var array The array of message headers. */
    protected $headers = [];

    /** @var StreamInterface The body of the message. */
    protected $body;

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $new = clone $this;

        $new->protocolVersion = $version;

        return $new;
    }

    public function getHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['value'];
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

        return isset($this->headers[$name]) ? $this->headers[$name]['value'] : [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $value = (array) $value;

        $new = clone $this;

        $new->headers[strtolower($name)] = compact('name', 'value');

        return $new;
    }

    public function withAddedHeader($name, $value)
    {
        $value = (array) $value;

        $normalizedName = strtolower($name);

        $new = clone $this;

        if (isset($new->headers[$normalizedName])) {
            $new->headers[$normalizedName]['value'] += $value;
        } else {
            $new->headers[$normalizedName] = compact('name', 'value');
        }

        return $new;
    }

    public function withoutHeader($name)
    {
        $new = clone $this;

        unset($new->headers[strtolower($name)]);

        return $new;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $new = clone $this;

        $new->body = $body;

        return $new;
    }
}
