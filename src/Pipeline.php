<?php

namespace Lazy\Http;

use RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Lazy\Http\Contracts\HttpHandlerInterface;

class Pipeline
{
    /**
     * The passable HTTP request.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * The array of passable HTTP request options.
     *
     * @var array
     */
    protected $opts = [];

    /**
     * The array of pipeline HTTP request middleware.
     *
     * @var \Psr\Http\Server\MiddlewareInterface[]
     */
    protected $middleware = [];

    /**
     * The method being called on each pipeline HTTP request middleware.
     *
     * @var string
     */
    protected $method = 'process';

    /**
     * Set the passable HTTP request.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  The passable HTTP request.
     * @param  array  $opts  The array of passable HTTP request options.
     * @return $this
     */
    public function send(RequestInterface $request, array $opts = [])
    {
        $this->request = $request;
        $this->opts = $opts;

        return $this;
    }

    /**
     * Set the array of pipeline HTTP request middleware.
     *
     * @param  array|mixed  $middleware  The array of pipeline HTTP request middleware.
     * @return $this
     */
    public function through($middleware)
    {
        $this->middleware = array_merge(
            $this->middleware, is_array($middleware) ? $middleware : func_get_args()
        );

        return $this;
    }

    /**
     * Set the method being called on each pipeline HTTP request middleware.
     *
     * @param  string  $method  The method being called on each pipeline HTTP request middleware.
     * @return $this
     */
    public function via($method)
    {
        $this->method = $method;
    }

    /**
     * Run the pipeline with an HTTP request handler.
     *
     * @param  \Lazy\Http\Contracts\HttpHandlerInterface  $handler  The HTTP request handler.
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \RuntimeException
     */
    public function then(HttpHandlerInterface $handler): ResponseInterface
    {
        if (empty($this->middleware)) {
            return $handler->handle($this->request, $this->opts);
        }

        $middleware = array_shift($this->middleware);

        if (! $middleware instanceof MiddlewareInterface) {
            throw new RuntimeException(
                sprintf('Invalid type of the HTTP request middleware: %s!'
                        .'The HTTP request middleware must be an instance of "Psr\Http\Server\MiddlewareInterface".', gettype($middleware)
                )
            );
        }

        if (! method_exists($middleware, $this->method)) {
            throw new RuntimeException("Method {$this->method} does not exists!");
        }

        return $middleware->{$this->method}($this->request, $this->opts, $this);
    }
}
