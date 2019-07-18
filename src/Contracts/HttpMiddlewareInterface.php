<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpMiddlewareInterface
{
    /**
     * Process an HTTP request and return an HTTP response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  The HTTP request.
     * @param  array  $opts  The array of HTTP request options.
     * @param  \Lazy\Http\Contracts\HttpHandlerInterface  $next  The next HTTP request handler.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, array $opts = [], HttpHandlerInterface $next): ResponseInterface;
}
