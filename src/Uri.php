<?php

namespace Lazy\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string The scheme component of the URI. */
    protected $scheme = '';

    /** @var string The user information component of the URI. */
    protected $userInfo = '';

    /** @var string The host component of the URI. */
    protected $host = '';

    /** @var int|null The port component of the URI. */
    protected $port;

    /** @var string The path component of the URI. */
    protected $path = '';

    /** @var string The query component of the URI. */
    protected $query = '';

    /** @var string The fragment component of the URI. */
    protected $fragment = '';
}
