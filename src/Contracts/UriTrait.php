<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;

trait UriTrait
{
    /**
     * The pattern for the scheme component of the URI.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    protected static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';

    /**
     * The URI sub-delimiters.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.2
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * The URI percent-encoded.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.1
     */
    protected static $pctEncoded = '(\%[A-Fa-f0-9]{2})';

    /**
     * The URI unreserved characters.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    protected static $unreserved = 'a-zA-Z0-9\-._~';

    /**
     * Apply a component to the URI.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function applyComponent($name, $value)
    {
        if ('scheme' === $name && $value) {
            if (! preg_match(static::$schemePattern, $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('port' === $name && null !== $value) {
            if (1 > $value || 65535 < $value) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        $this->{$name} = $value;
    }

    /**
     * Throw an invalid component of the URI exception.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function throwInvalidComponentException($name, $value)
    {
        throw new InvalidArgumentException("Invalid {$name} component of the URI: {$value}!");
    }
}
