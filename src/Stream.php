<?php

namespace Lazy\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * Create a new stream instance.
     *
     * @param resource $resource The stream resource.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($resource)
    {
        
    }
}
