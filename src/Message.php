<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    const TOKEN = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';
    const FIELD_VALUE = '/^[ \t]*(?:(?:[](?:[ \t]+[])?)|\r\n[ \t]+)*[ \t]*$/';
}
