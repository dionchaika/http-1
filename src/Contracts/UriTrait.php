<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    public static function removeDotSegments(UriInterface $uri): UriInterface
    {
        $path = $uri->getPath();

        $output = [];
        $input = explode('/', $path);

        while (! empty($input)) {
            
        }

        $path = implode('/', $output);

        return $uri->withPath($path);
    }
}
