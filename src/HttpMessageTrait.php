<?php

namespace Lazy\Http;

trait HttpMessageTrait
{
    /**
     * The HTTP message token pattern.
     *
     * @var string
     *
     * @see https://tools.ietf.org/html/rfc2616#section-4.2
     */
    protected static $token = '[^\x00-\x20\x7f()<>@,;:\\"\/\[\]?={}]+';
}
