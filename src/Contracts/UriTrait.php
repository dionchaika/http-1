<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    public static function removeDotSegments(UriInterface $uri): UriInterface
    {
        $input = $uri->getPath(); $output = '';

        while ('' !== $input) {
            
        }

        return $uri->withPath($output);
    }
}
