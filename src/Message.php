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

    /**
     * Filter an HTTP header name.
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function filterHeaderName($name)
    {
        if (preg_match(self::TOKEN, $name)) {
            return $name;
        }

        throw new InvalidArgumentException("Invalid header name: {$name}!");
    }

    /**
     * Filter an HTTP header value(s).
     *
     * @param string|string[] $value
     * @return string[]
     * @throws InvalidArgumentException
     */
    protected static function filterHeaderValue($value)
    {
        $values = (array) $value;

        foreach ($values as $value) {
            if (! preg_match(self::FIELD_VALUE, $value)) {
                throw new InvalidArgumentException("Invalid header value: {$value}!");
            }
        }

        return $values;
    }
}
