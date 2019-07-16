<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpMiddlewareInterface
{
    /**
     * Process a request and return a response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @param  \Lazy\Http\Contracts\HttpHandlerInterface|callable  $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, $handler): ResponseInterface;
}
