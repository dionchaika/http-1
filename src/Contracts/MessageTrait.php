<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

trait MessageTrait
{
    /** @var string */
    protected static $token = '[!#$%&\'*+\-.^_`|~0-9A-Za-z]+';

    /** @var string */
    protected static $headerFiledValue = '/^[ \t]*(?:[\x20-\x7e\x80-\xff](?:[ \t]+[\x20-\x7e\x80-\xff])?)*[ \t]*$/';

    /** @var string */
    protected $protocolVersion = '1.1';

    /** @var array */
    protected $headers = [];

    /** @var StreamInterface */
    protected $body;

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
     * @param string $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHeaderName($name)
    {
        if (! preg_match('/^'.static::$token.'$/', $name)) {
            throw new InvalidArgumentException(
                "Header field name is not valid: {$name}!"
            );
        }
    }

    /**
     * Validate a header field value.
     *
     * @param array $value
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHeaderValue(array $value)
    {
        foreach ($value as $value) {
            if (! preg_match(static::$headerFiledValue, $value)) {
                throw new InvalidArgumentException(
                    "Header field value is not valid: $value!"
                );
            }
        }
    }
}
