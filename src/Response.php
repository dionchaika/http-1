<?php

declare(strict_types=1);

namespace Lazy\Http;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{
    /** @var array */
    protected static $standartReasonPhrases = [

        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'

    ];

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $reasonPhrase;

    /**
     * Filter an HTTP status code.
     *
     * @param int $code
     * @return int
     * @throws InvalidArgumentException
     */
    protected static function filterStatusCode($code)
    {
        if (100 <= $code && 599 >= $code && 306 !== $code) {
            return $code;
        }

        throw new InvalidArgumentException("Invalid HTTP status code: {$code}!");
    }

    /**
     * Get the standart reason
     * phrase for the given HTTP status code.
     *
     * @param int $code
     * @return string
     */
    protected static function getStandartReasonPhrase($code)
    {
        if (! isset(static::$standartReasonPhrases[$code])) {
            return '';
        }

        return static::$standartReasonPhrases[$code];
    }

    /**
     * Initializes a new response instance.
     *
     * @param int $code
     * @param string $reasonPhrase
     *
     * @throws InvalidArgumentException
     */
    public function __construct($code = 200, $reasonPhrase = '')
    {
        $code = static::filterStatusCode($code);

        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $code = static::filterStatusCode($code);

        $response = clone $this;

        $response->statusCode = $code;
        $response->reasonPhrase = $reasonPhrase;

        return $response;
    }

    public function getReasonPhrase()
    {
        if ('' === $this->reasonPhrase) {
            return '';
        }

        return static::getStandartReasonPhrase($this->getStatusCode());
    }
}
