<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;

trait UriTrait
{
    protected static $defaultHost = 'localhost';

    /**
     * Apply a component of the URI.
     *
     * @param  string  $component
     * @param  mixed  $value
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function applyComponent($component, $value)
    {
        if ('scheme' === $component) {
            if (false === filter_var($value.'://'.static::$defaultHost, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
                $this->throwInvalidComponentException($component, $value);
            }
        }

        if ('host' === $component) {
            if (false === filter_var('//'.$value, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
                $this->throwInvalidComponentException($component, $value);
            }
        }

        if ('path' === $component) {
            if (false === filter_var('//'.static::$defaultHost.$value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                $this->throwInvalidComponentException($component, $value);
            }
        }

        if ('query' === $component) {
            if (false === filter_var('//'.static::$defaultHost.'?'.$value, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED)) {
                $this->throwInvalidComponentException($component, $value);
            }
        }

        $this->{$component} = $value;
    }

    protected function throwInvalidComponentException($component, $value)
    {
        throw new InvalidArgumentException("Invalid {$component} component of the URI: {$value}!");
    }
}
