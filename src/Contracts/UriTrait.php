<?php

namespace Lazy\Http\Contracts;

trait UriTrait
{
    /**
     * @var array
     */
    protected $standartPorts = [

        'http'  => 80,
        'https' => 443

    ];

    /**
     * @var array
     */
    protected $defaultEnvironments = [

        'HTTPS'         => 'off',
        'PHP_AUTH_PW'   => '',
        'PHP_AUTH_USER' => '',
        'QUERY_STRING'  => '',
        'REQUEST_URI'   => '/',
        'SERVER_NAME'   => 'localhost',
        'SERVER_PORT'   => '80'

    ];

    /**
     * Check is the TCP or UDP port
     * standart for the given URI scheme.
     *
     * @param  string  $scheme  The URI scheme.
     * @param  int|null  $port  The TCP or UDP port.
     * @return bool
     */
    public static function isStandartPort($scheme, $port)
    {
        return isset($this->standartPorts[$scheme]) && $port === $this->standartPorts[$scheme];
    }
}
