<?php

namespace Lazy\Http\Handlers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Lazy\Http\Contracts\HttpHandlerInterface;

abstract class HttpHandler implements HttpHandlerInterface
{
    /**
     * Handle a request and return a response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(RequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }
}
