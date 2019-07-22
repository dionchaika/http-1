<?php

namespace Lazy\Http;

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;

class StreamFactory implements StreamFactoryInterface
{
    /** The readable stream mode pattern. */
    protected static $readableStreamModePattern = '/r|r\+|w\+|a\+|x\+|c\+/';

    /** The writable stream mode pattern. */
    protected static $writableStreamModePattern = '/r\+|w|w\+|a|a\+|x|x\+|c|c\+/';

    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream(fopen('php://temp', 'r+'));

        $stream->write($content);

        return $stream;
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (! preg_match(static::$readableStreamModePattern, $mode) && ! preg_match(static::$writableStreamModePattern, $mode)) {
            throw new InvalidArgumentException("Stream mode is not valid: {$mode}!");
        }

        $resource = fopen($filename, $mode);

        if (false === $resource) {
            throw new RuntimeException("Unable to open the file: {$filename}!");
        }

        return new Stream($resource);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        $stream = new Stream($resource);

        if (! $stream->isReadable()) {
            trigger_error("The stream is not readable!", E_USER_WARNING);
        }

        return $stream;
    }
}
