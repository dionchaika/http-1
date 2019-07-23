<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * The "RFC 7230" header field-name.
     *
     * @var string
     */
    protected static $fieldName = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /**
     * The "RFC 7230" header field-value.
     *
     * @var string
     */
    protected static $fieldValue = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r?\n[ \t]+)*[ \t]*$/';

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['values'];
        }

        return $headers;
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        $name = strtolower($name);

        return isset($this->headers[$name]) ? $this->headers[$name]['values'] : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * {@inheritDoc}
     */
    public function withHeader($name, $value)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedHeader($name, $value)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withoutHeader($name)
    {
        $new = clone $this;
        unset($new->headers[strtolower($name)]);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withBody(StreamInterface $body)
    {
        
    }
}
