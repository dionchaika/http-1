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
     * consists of the original header filed name
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
}
