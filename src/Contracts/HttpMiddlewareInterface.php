<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpMiddlewareInterface
{
    /**
     * Process a request and return a response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  The HTTP request.
     * @param  array  $opts  The array of HTTP request options.
     * @param  \Lazy\Http\Contracts\HttpHandlerInterface|callable  $handler  The next HTTP request handler.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, array $opts = [], $handler): ResponseInterface;
}
