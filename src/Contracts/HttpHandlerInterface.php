<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpHandlerInterface
{
    /**
     * Handle an HTTP request and return an HTTP response.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  The HTTP request.
     * @param  array  $opts  The array of HTTP request options.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request, array $opts = []): ResponseInterface;
}