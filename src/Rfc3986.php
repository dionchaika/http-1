<?php

namespace Lazy\Http;

/**
 * @see https://tools.ietf.org/html/rfc3986
 */
abstract class Rfc3986
{
    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.1
     */
    public static $pctEncoded = '\%[A-Fa-f0-9]{2}';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    public static $genDelims = ':\/?#[]@';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    public static $subDelims = '!$&\'()*+,;=';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    public static $unreserved = 'A-Za-z0-9\-._~';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    public static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';
}
