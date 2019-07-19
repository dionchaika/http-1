<?php

namespace Lazy\Http;

/**
 * @see https://tools.ietf.org/html/rfc7230
 */
abstract class Rfc7230
{
    /**
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.6
     */
    public static $token = '[!#$%&\'*+\-.^_`|~0-9A-Za-z]+';

    /**
     * Check is the header field name valid.
     *
     * @param  string  $name  The header field name.
     * @return bool
     */
    public static function isHeaderFieldNameValid($name)
    {
        return preg_match('/^'.static::$token.'$/', $name);
    }
}
