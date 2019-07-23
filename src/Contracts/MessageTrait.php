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
    protected static $headerName = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /**
     * The "RFC 7230" header field value.
     *
     * @var string
     */
    protected static $headerValue = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r?\n[ \t]+)*[ \t]*$/';

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
     * {@inheritDoc}
     */
    public function withoutHeader($name)
    {
        $new = clone $this;
        unset($new->headers[strtolower($name)]);

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function withBody(StreamInterface $body)
    {

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

        $this->validateHeaderName($name);
        $this->validateHeaderValues($values);

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

        $this->validateHeaderName($name);
        $this->validateHeaderValues($values);

        $noralizedName = strtolower($name);

        if (isset($this->headers[$noralizedName])) {
            $this->headers[$noralizedName]['values'] += $values;
        } else {
            $this->headers[$noralizedName] = compact('name', 'values');
        }
    }

    /**
     * Validate a header field name.
     *
     * @param string $name Header field name.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function validateHeaderName($name)
    {
        if (! static::isHeaderNameValid($name)) {
            throw new InvalidArgumentException(
                "Header field name is not valid: {$name}!"
            );
        }
    }

    /**
     * Validate an array of header field values.
     *
     * @param string[] $values An array of header field values.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function validateHeaderValues(array $values)
    {
        foreach ($values as $value) {
            if (! static::isHeaderValueValid($value)) {
                throw new InvalidArgumentException(
                    "Header field value is not valid: {$value}!"
                );
            }
        }
    }

    /**
     * Is a header field name valid.
     *
     * @param string $name Header field name.
     *
     * @return bool
     */
    protected static function isHeaderNameValid($name)
    {
        return preg_match(static::$headerName, $name);
    }

    /**
     * Is a header field value valid.
     *
     * @param string $value Header field value.
     *
     * @return bool
     */
    protected static function isHeaderValueValid($value)
    {
        return preg_match(static::$headerValue, $value);
    }
}
