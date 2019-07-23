<?php

namespace Lazy\Http\Contracts;

trait MessageTrait
{
    /**
     * The "RFC 7230" header field name.
     *
     * @var string
     */
    protected static $headerFieldName = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /**
     * The "RFC 7230" header field value.
     *
     * @var string
     */
    protected static $headerFieldValue = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r?\n[ \t]+)*[ \t]*$/';

    /**
     * The array of message headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Is a header field valid.
     *
     * @param string $name A header field name.
     * @param array $values An array of header filed values.
     *
     * @return bool
     */
    protected static function isHeaderFieldValid($name, array $values)
    {
        if (! preg_match(static::$headerFieldName, $name)) {
            return false;
        }

        foreach ($values as $value) {
            if (! preg_match(static::$headerFieldValue, $value)) {
                return false;
            }
        }
    }
}
