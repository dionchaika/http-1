<?php

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class CustomStream extends Stream implements StreamInterface
{
    /** @var int|null */
    protected $size;

    /** @var array */
    protected $metadata = [];

    /**
     * Initializes a new stream instance.
     *
     * Allowed options:
     *      1. size (int|null) - the custom stream size.
     *      2. metadata (array) - the array of custom stream metadata.
     *
     * @param resource $stream
     * @param array $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct($stream, array $options = [])
    {
        if (isset($options['size'])) {
            $this->size = $options['size'];
        }

        if (isset($options['metadata'])) {
            $this->metadata = $options['metadata'];
        }

        parent::__construct($stream);
    }

    public function getSize()
    {
        return (null !== $this->size)
            ? $this->size
            : parent::getSize();
    }

    public function getMetadata($key = null)
    {
        if (null === $key) {
            return $this->metadata += parent::getMetadata();
        }

        return isset($this->metadata[$key])
            ? $this->metadata[$key] : parent::getMetadata($key);
    }
}
