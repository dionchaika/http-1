<?php

namespace Lazy\Http\Contracts;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * The message body.
     *
     * @var StreamInterface
     */
    protected $body;

    /**
     * The array
     * of message headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The message HTTP protocol version.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * The "RFC 7230" header field name.
     *
     * @var string
     */
    protected static $headerFieldName = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /**
     * The "RFC 7230" header field value.
     *
     * @var string
     */
    protected static $headerFieldValue = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r?\n[ \t]+)*[ \t]*$/';

    /**
     * {@inheritDoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['values'];
        }

        return $headers;
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name)
    {
        $name = strtolower($name);

        return isset($this->headers[$name]) ? $this->headers[$name]['values'] : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * {@inheritDoc}
     */
    public function withHeader($name, $value)
    {
        $new = clone $this;
        $new->setHeader($name, $value);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedHeader($name, $value)
    {
        $new = clone $this;
        $new->addHeader($name, $value);

        return $new;
    }

    /**
     * Set header to the message.
     *
     * @param string $name Header field name.
     * @param string|string[] $value Header field value(s).
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function setHeader($name, $value)
    {
        $values = (array) $value;

        $this->headers[strtolower($name)] = compact('name', 'values');
    }

    /**
     * Add header to the message.
     *
     * @param string $name Header field name.
     * @param string|string[] $value Header field value(s).
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function addHeader($name, $value)
    {
        $values = (array) $value;

        $noralizedName = strtolower($name);

        if (isset($this->headers[$noralizedName])) {
            $this->headers[$noralizedName]['values'] += $values;
        } else {
            $this->headers[$noralizedName] = compact('name', 'values');
        }
    }
}
