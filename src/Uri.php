<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    const SUB_DELIMS = '!$&\'()*+,;=';
    const UNRESERVED = 'A-Za-z0-9\-._~';

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
     * Compare two URIs.
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
        if ($performNormalization) {
            $first = static::normalize($first);
            $second = static::normalize($second);
        }

        return (string) $first === (string) $second;
    }

    /**
     * Normalize a URI according to "RFC 3986".
     *
     * @see https://tools.ietf.org/html/rfc3986#section-6
     *
     * @param UriInterface $uri
     * @return UriInterface
     */
    public static function normalize(UriInterface $uri): UriInterface
    {
        if ('' !== $uri->getScheme() && '' === $uri->getPath()) {
            $uri = $uri->withPath('/');
        }

        $queryParams = explode('&', $uri->getQuery());

        sort($queryParams);
        $uri = $uri->withQuery(implode('&', $queryParams));

        $uri = new static(preg_replace_callback('/(?:\%[A-Fa-f0-9]{2})++/', function ($matches) {
            return strtoupper($matches[0]);
        }, (string) $uri));

        return new static(preg_replace_callback('/\%(?:2D|2E|5F|7E|3[0-9]|[46][1-9A-F]|[57][0-9A])/', function ($matches) {
            return rawurldecode($matches[0]);
        }, (string) $uri));
    }
}
