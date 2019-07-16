<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpHandlerInterface
{
    /**
     * Handle a request and return a response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface;
}
