<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string */
    protected $scheme = '';

    /** @var string */
    protected $user = '';

    /** @var string|null */
    protected $password;

    /** @var string */
    protected $host = '';

    /** @var int|null */
    protected $port;

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $query = '';

    /** @var string */
    protected $fragment = '';

    /**
     * Compare two URI.
     *
     * @param UriInterface $first
     * @param UriInterface $second
     * @param bool $performNormalization
     *
     * @return bool
     */
    public static function isEqual(
        UriInterface $first,
        UriInterface $second,
        $performNormalization = true
    ) {
        if (! $performNormalization) {
            return (string) $first === (string) $second;
        }

        return (string) static::normalize($first) === (string) static::normalize($second);
    }

    /**
     * Normalize a URI according to "RFC 3986".
     *
     * @param UriInterface $uri
     * @return UriInterface
     */
    public static function normalize(UriInterface $uri)
    {
        
    }
}
