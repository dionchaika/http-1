<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string */
    protected $scheme = '';

    /** @var string */
    protected $user = '';

    /** @var string|null */
    protected $password;

    /** @var string */
    protected $host = '';

    /** @var int|null */
    protected $port;

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $query = '';

    /** @var string */
    protected $fragment = '';
}
