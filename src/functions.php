<?php

namespace Lazy\Http;

use InvalidArgumentException;

function filter_uri_scheme($scheme)
{
    $scheme = (string) $scheme;
    $pattern = '/^[a-z][a-z0-9+\-.]*$/i';

    if ('' === $scheme || preg_match($pattern, $scheme)) {
        return $scheme;
    }

    throw new InvalidArgumentException(
        "URI scheme component is not valid: {$scheme}! The rule is: \"/^[a-z][a-z0-9+\-.]*$/i\""
    );
}
