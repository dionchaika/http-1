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
        'timeout' => 30.0,
        'redirects' => [

            'enable' => true,
            'max' => 10,
            'strict' => true,
            'referer' => true

        ],
        'cookies' => [

            'enable' => true,
            'jar' => null,
            'file' => null

        ]

    ];
}
