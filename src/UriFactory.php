<?php

declare(strict_types=1);

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

final class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
