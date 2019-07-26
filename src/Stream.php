<?php

declare(strict_types=1);

namespace Lazy\Http;

use Throwable;
use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    const MODES = '/^(?:r\+?|w\+?|a\+?|x\+?|c\+?|e)[tb]*$/';
    const READABLE_MODES = '/^(?:r\+?|w\+|a\+|x\+|c\+)[tb]*$/';
    const WRITABLE_MODES = '/^(?:r\+|w\+?|a\+?|x\+?|c\+?)[tb]*$/';

    /** @var resource */
    protected $stream;

    /** @var bool */
    protected $seekable = false;

    /** @var bool */
    protected $writable = false;

    /** @var bool */
    protected $readable = false;

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

        $this->seekable = $this->getMetadata('seekable');

        $mode = $this->getMetadata('mode');

        $this->writable = preg_match(self::WRITABLE_MODES, $mode);
        $this->readable = preg_match(self::READABLE_MODES, $mode);
    }

    public function __toString()
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (Throwable $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return '';
        }
    }

    public function close()
    {
        if (null !== $this->stream && fclose($this->stream)) {
            $this->detach();
        }
    }

    public function detach()
    {
        $stream = $this->stream;

        if (null !== $stream) {
            $this->stream = null;
            $this->seekable = $this->readable = $this->writable = false;
        }

        return $stream;
    }

    public function getSize()
    {
        if (null === $this->stream) {
            return null;
        }

        $stat = fstat($this->stream);

        return (false === $stat) ? null : $stat['size'];
    }

    public function tell()
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached!');
        }

        $position = ftell($this->stream);

        if (false === $position) {
            throw new RuntimeException(
                'Unable to tell the current position of the stream read/write pointer!'
            );
        }

        return $position;
    }

    public function eof()
    {
        return null === $this->stream || feof($this->stream);
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached!');
        }

        if (! $this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable!');
        }

        if (-1 === fseek($this->stream, $offset, $whence)) {
            throw new RuntimeException('Unable to seek to a position in the stream!');
        }
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function write($string)
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached!');
        }

        if (! $this->isWritable()) {
            throw new RuntimeException('Stream is not writable!');
        }

        $bytes = fwrite($this->stream, $string);

        if (false === $bytes) {
            throw new RuntimeException('Unable to write data to the stream!');
        }

        return $bytes;
    }

    public function isReadable()
    {
        return $this->readable;
    }

    public function read($length)
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached!');
        }

        if (! $this->isReadable()) {
            throw new RuntimeException('Stream is not readable!');
        }

        $data = fread($this->stream, $length);

        if (false === $data) {
            throw new RuntimeException('Unable to read data from the stream!');
        }

        return $data;
    }

    public function getContents()
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached!');
        }

        if (! $this->isReadable()) {
            throw new RuntimeException('Stream is not readable!');
        }

        $contents = stream_get_contents($this->stream);

        if (false === $contents) {
            throw new RuntimeException('Unable to get the contents of the stream!');
        }

        return $contents;
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
