<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * The protocol version of the message.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * The array of message header fields.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The body of the message.
     *
     * @var StreamInterface
     */
    protected $body;

    /**
     * The "RFC 2616" token.
     *
     * @var string
     */
    protected static $token = '[!#$%&\'*+\-.^_`|~0-9A-Za-z]+';

    /**
     * The "RFC 2616" header field value pattern.
     *
     * @var string
     */
    protected static $valuePattern = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|(?:\r\n[ \t]+))*[ \t]*$/';

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $new = clone $this;

        $new->protocolVersion = $version;

        return $new;
    }

    public function getHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['value'];
        }

        return $headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name)
    {
        $name = strtolower($name);

        return isset($this->headers[$name]) ? $this->headers[$name]['value'] : [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $value = (array) $value;

        $this->validateHeaderName($name);
        $this->validateHeaderValue($value);

        $new = clone $this;

        $new->headers[strtolower($name)] = compact('name', 'value');

        return $new;
    }

    public function withAddedHeader($name, $value)
    {
        $value = (array) $value;

        $this->validateHeaderName($name);
        $this->validateHeaderValue($value);

        $normalizedName = strtolower($name);

        $new = clone $this;

        if (isset($new->headers[$normalizedName])) {
            $new->headers[$normalizedName]['value'] += $value;
        } else {
            $new->headers[$normalizedName] = compact('name', 'value');
        }

        return $new;
    }

    public function withoutHeader($name)
    {
        $new = clone $this;

        unset($new->headers[strtolower($name)]);

        return $new;
    }

    public function getBody()
    {
        if (! $this->body) {
            //
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $new = clone $this;

        $new->body = $body;

        return $new;
    }

    /**
     * Validate a header field name.
     *
     * @param string $name The header field name.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHeaderName($name)
    {
        if (! preg_match('/^'.static::$token.'$/', $name)) {
            throw new InvalidArgumentException("Header field name is not valid: {$name}!");
        }
    }

    /**
     * Validate a header field value.
     *
     * @param array $value The header field value.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHeaderValue(array $value)
    {
        foreach ($value as $val) {
            if (! preg_match(static::$headerFiledValue, $val)) {
                throw new InvalidArgumentException("Header field value is not valid: {$val}!");
            }
        }
    }
}
