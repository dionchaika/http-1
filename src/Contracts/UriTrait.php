<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;

trait UriTrait
{
    /**
     * The pattern for scheme URI component.
     *
     * @var string
     */
    protected static $schemePattern = '/^[a-z][a-z0-9+\-.]*$/i';

    /**
     * The URI sub-delimiters.
     *
     * @var string
     */
    protected static $subDelims = '!$&\'()*+,;=';

    /**
     * The URI percent-encoded.
     *
     * @var string
     */
    protected static $pctEncoded = '(\%[A-Fa-f0-9]{2})';

    /**
     * The URI unreserved characters.
     *
     * @var string
     */
    protected static $unreserved = 'a-zA-Z0-9\-._~';

    /**
     * Apply the URI component.
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
    }
}
