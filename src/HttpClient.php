<?php

namespace Lazy\Http;

use Psr\Http\Client\ClientInterface;

class HttpClient implements ClientInterface
{
    const VERSION = '1.0.0';

    /**
     * The array of client default options.
     *
     * @var array
     */
    protected $defaultOpts = [

        'handler' => 'stream',
        'headers' => [],
        'cookies' => [

            'enable' => true

        ],
        'timeout' => 30.0,
        'redirects' => [

            'enable' => true,
            'max' => 10,
            'strict' => true,
            'referer' => true

        ]

    ];
}
