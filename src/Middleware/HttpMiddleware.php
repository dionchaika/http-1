<?php

namespace Lazy\Http\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lazy\Http\Contracts\HttpMiddlewareInterface;

abstract class HttpMiddleware implements HttpMiddlewareInterface
{
    /**
     * Process a request and return a response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @param  \Lazy\Http\Contracts\HttpHandlerInterface|callable  $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(RequestInterface $request, $handler): ResponseInterface
    {
        return $this->process($request, $handler);
    }
}
