<?php

declare(strict_types=1);

namespace Lazy\Http;

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /** @var string */
    protected static $readableModes = '/r\+?|w\+|a\+|x\+|c\+/';

    /** @var string */
    protected static $writableModes = '/r\+|w\+?|a\+?|x\+?|c\+?/';

    /** @var resource */
    protected $stream;

    /** @var bool */
    protected $seekable = false;

    /** @var bool */
    protected $readable = false;

    /** @var bool */
    protected $writable = false;

    /**
     * Creates a new stream instance.
     *
     * @param resource $stream
     * @throws InvalidArgumentException
     */
    public function __construct($stream)
    {
        if (! is_resource($stream)) {
            throw new InvalidArgumentException(
                sprintf('Stream MUST be a resource, "%s" given!', gettype($stream))
            );
        }

        $this->stream = $stream;
    }

    public function getMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->stream);

        if (null === $key) {
            return $metadata;
        }

        return isset($metadata[$key]) ? $metadata[$key] : null;
    }
}
