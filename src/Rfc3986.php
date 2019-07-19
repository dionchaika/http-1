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
    protected static $pctEncoded = '\%[A-Fa-f0-9]{2}';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    protected static $genDelims = ':\/?#[]@';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    protected static $unreserved = 'A-Za-z0-9\-._~';

    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    protected static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';
}
