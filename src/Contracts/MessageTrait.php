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
}
