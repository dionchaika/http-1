<?php

namespace Lazy\Http;

use Psr\Http\Client\ClientInterface;

class HttpClient implements ClientInterface
{
    /**
     * The array of client default options.
     *
     * @var array
     */
    protected $defaultOpts = [

        'headers' => [],
        'redirects' => [

            'enable' => true,
            'max' => 10,
            'strict' => true,
            'referer' => true

        ]

    ];
}
