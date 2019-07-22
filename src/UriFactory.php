<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
