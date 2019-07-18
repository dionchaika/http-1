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
     * The URI unreserved characters.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.3
     */
    protected static $unreserved = 'a-zA-Z0-9\-._~';

    /**
     * The pattern for the URI percent-encoded character.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2.1
     */
    protected static $pctEncodedPattern = '\%[A-Fa-f0-9]{2}';

    /**
     * Apply a component to the URI.
     *
     * @param  string  $name  The URI component name.
     * @param  mixed|null  $value  The URI component value.
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function applyComponent($name, $value)
    {
        if ('scheme' === $name) {
            $value = (string) $value;

            if ($value && ! preg_match(static::$schemePattern, $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('port' === $name) {
            if (null !== $value) {
                $value = (int) $value;
            }

            if (is_int($value) && (1 > $value || 65535 < $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('host' === $name) {
            $value = (string) $value;

            if (preg_match('/^\[(.+)\]$/', $value, $matches)) {
                if (
                    ! preg_match('/^v[a-f0-9]\.['.static::$unreserved.static::$subDelims.':]$/i', $matches[0]) ||
                    false === filter_var($matches[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
                ) {
                    $this->throwInvalidComponentException($name, $value);
                }
            } else if (
                false === filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
                ! preg_match('/^(['.static::$unreserved.static::$subDelims.']|'.static::$pctEncodedPattern.')*$/', $value)
            ) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('path' === $name) {
            $value = (string) $value;

            if (
                ($this->getAuthority() && $value && '/' !== $value[0]) ||
                (! $this->getAuthority() && '/' === $value[0] && '/' === $value[1]) ||
                ! preg_match('/^(['.static::$unreserved.static::$subDelims.':@\/]|'.static::$pctEncodedPattern.')*$/', $value)
            ) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('query' === $name) {
            $value = (string) $value;

            if (! preg_match('/^(['.static::$unreserved.static::$subDelims.':@\/?]|'.static::$pctEncodedPattern.')*$/', $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        if ('fragment' === $name) {
            $value = (string) $value;

            if (! preg_match('/^(['.static::$unreserved.static::$subDelims.':@\/?]|'.static::$pctEncodedPattern.')*$/', $value)) {
                $this->throwInvalidComponentException($name, $value);
            }
        }

        $this->{$name} = $value;
    }

    /**
     * Throw an exception if the component of the URI is invalid.
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
