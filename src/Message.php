<?php

namespace Lazy\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    /** @var string The HTTP protocol version of the message. */
    protected $protocolVersion = '1.1';

    /** @var StreamInterface The body of the message. */
    protected $body;

    /**
     * The array of message headers.
     *
     * Note: The keys are the normalized
     * header field names while the values
     * consist of the original header filed name
     * and the array of header filed value strings.
     *
     * @var array
     */
    protected $headers = [];

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
}
