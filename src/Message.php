<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    const TOKEN = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';
    const FIELD_VALUE = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r\n[ \t]+)*[ \t]*$/';

    /** @var StreamInterface */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion = '1.1';
}
