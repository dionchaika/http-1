<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * The protocol version of the message.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * The array of message headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The body of the message.
     *
     * @var StreamInterface
     */
    protected $body;

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
            $headers[$header['name']] = $header['value'];
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

        return isset($this->headers[$name]) ? $this->headers[$name]['value'] : [];
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
        static::validateMessageBody($body);

        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    /**
     * Validate a header field name.
     *
     * @param string A header field name.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected static function validateHeaderName($name)
    {
        
    }

    /**
     * Validate a header field value strings.
     *
     * @param string An array of header field value strings.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected static function validateHeaderStrings(array $strings)
    {
        
    }

    /**
     * Validate a body of the message.
     *
     * @param StreamInterface $body A stream representing
     *      the body of the HTTP message.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected static function validateMessageBody(StreamInterface $body)
    {

    }
}
