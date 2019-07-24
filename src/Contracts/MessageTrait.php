<?php

declare(strict_types=1);

namespace Lazy\Http\Contracts;

use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /** @var string @see https://tools.ietf.org/html/rfc7230#section-3.2.6 */
    protected static $token = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';

    /** @var string @see https://tools.ietf.org/html/rfc7230#section-3.2 */
    protected static $header = '/^[ \t]*(?:(?:[\x21-\x7e\x80-\xff](?:[ \t]+[\x21-\x7e\x80-\xff])?)|\r\n[ \t]+)*[ \t]*$/';

    /** @var StreamInterface */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion = '1.1';

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $message = clone $this;
        $message->protocolVersion = $version;

        return $message;
    }

    public function getHeaders()
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['values'];
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

        return isset($this->headers[$name]) ? $this->headers[$name]['values'] : [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        
    }

    public function withAddedHeader($name, $value)
    {
        
    }

    public function withoutHeader($name)
    {
        
    }

    public function getBody()
    {
        
    }

    public function withBody(StreamInterface $body)
    {
        
    }
}
