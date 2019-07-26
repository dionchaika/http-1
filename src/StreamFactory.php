<?php

declare(strict_types=1);

namespace Lazy\Http;

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream(
            fopen('php://temp', 'r+')
        );

        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (! preg_match(Stream::MODES, $mode)) {
            throw new InvalidArgumentException("Invalid stream mode: {$mode}!");
        }

        $stream = fopen($filename, $mode);

        if (false === $stream) {
            throw new RuntimeException("Unable to open the file: {$filename}!");
        }

        return new Stream($stream);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        $stream = new Stream($resource);

        if (! $stream->isReadable()) {
            trigger_error('Stream MUST be readable!', E_USER_WARNING);
        }

        return $stream;
    }
}
