<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    public static function removeDotSegments(UriInterface $uri): UriInterface
    {
        $path = $uri->getPath();

        

        return $uri->withPath($path);
    }
}
