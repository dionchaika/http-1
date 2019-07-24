<?php

declare(strict_types=1);

namespace Lazy\Http\Contracts;

trait MessageTrait
{
    /** @var string @see https://tools.ietf.org/html/rfc7230#section-3.2.6 */
    protected static $token = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /** @var string @see https://tools.ietf.org/html/rfc7230#section-3.2 */
    protected static $header = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r?\n[ \t]+)*[ \t]*$/';
}
