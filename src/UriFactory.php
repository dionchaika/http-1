<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

final class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
