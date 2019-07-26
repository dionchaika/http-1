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
     * Percent-encode a URI path component.
     * This method DOES NOT double-encode any characters.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @param string $path URI path component.
     * @return string
     */
    protected static function percentEncodePath($path)
    {
        $pattern = '/(?:[\/'.self::UNRESERVED.self::SUB_DELIMS.':@%]++|\%(?![A-Za-z0-9]{2}))/';

        return preg_replace_callback($pattern, function ($matches) {
            return rawurlencode($matches[0]);
        }, $path);
    }

    /**
     * Percent-encode a URI query or fragment component.
     * This method DOES NOT double-encode any characters.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @param string $path URI query or fragment component.
     * @return string
     */
    protected static function percentEncodeQueryOrFragment($queryOrFragment)
    {
        $pattern = '/(?:[\/'.self::UNRESERVED.self::SUB_DELIMS.':@?%]++|\%(?![A-Za-z0-9]{2}))/';

        return preg_replace_callback($pattern, function ($matches) {
            return rawurlencode($matches[0]);
        }, $queryOrFragment);
    }
}
