<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /** @var string */
    protected static $token = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

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

        $new = clone $this;

        $new->headers[strtolower($name)] = compact('name', 'value');

        return $new;
    }

    public function withAddedHeader($name, $value)
    {
        $value = (array) $value;
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
}
