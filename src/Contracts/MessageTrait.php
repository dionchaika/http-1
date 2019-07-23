<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * The body of the message.
     *
     * @var StreamInterface
     */
    protected $body;

    /**
     * The array of message headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The protocol version of the message.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion($version)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name)
    {
        
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
