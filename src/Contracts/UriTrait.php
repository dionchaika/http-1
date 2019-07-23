<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    public static function removeDotSegments(UriInterface $uri): UriInterface
    {
        $input = explode('/', $uri->getPath());
        $output = [];

        while (! empty($input)) {
            
        }

        return $uri->withPath(implode('/', $output));
    }
}
