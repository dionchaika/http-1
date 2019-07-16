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

    /**
     * Create a new URI instance from PHP globals.
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public static function fromGlobals()
    {
        return static::fromEnvironments($_SERVER);
    }

    /**
     * Create a new URI instance from environments.
     *
     * @param  array  $environments  The array of environments.
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public static function fromEnvironments(array $environments)
    {
        $environments = array_merge($this->defaultEnvironments, $environments);

        $scheme = 'off' === $environments['HTTPS'] ? 'http' : 'https';
        $user = $environments['PHP_AUTH_USER'];
        $password = $environments['PHP_AUTH_PW'] ?: null;
        $host = $environments['SERVER_NAME'];
        $port = (int) $environments['SERVER_PORT'];
        $path = reset(explode('?', $environments['REQUEST_URI'], 2));
        $query = $environments['QUERY_STRING'];

        return new static($scheme, $user, $password, $host, $port, $path, $query);
    }

    /**
     * Create a new URI instance from string.
     *
     * @param  string  $uri  The URI string.
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public static function fromString($uri)
    {
        $parts = parse_url($uri);

        if (false === $parts) {
            throw new InvalidArgumentException("Unable to parse the URI: {$uri}!");
        }

        $scheme = ! empty($parts['scheme']) ? $parts['scheme'] : '';
        $user = ! empty($parts['user']) ? $parts['user'] : '';
        $password = ! empty($parts['pass']) ? $parts['pass'] : null;
        $host = ! empty($parts['host']) ? $parts['host'] : '';
        $port = ! empty($parts['port']) ? $parts['port'] : null;
        $path = ! empty($parts['path']) ? $parts['path'] : '';
        $query = ! empty($parts['query']) ? $parts['query'] : '';
        $fragment = ! empty($parts['fragment']) ? $parts['fragment'] : '';

        return new static($scheme, $user, $password, $host, $port, $path, $query, $fragment);
    }

    /**
     * Check is the URI absolute.
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return (bool) $this->scheme;
    }

    /**
     * Check is the URI a network-path reference.
     *
     * @return bool
     */
    public function isNetworkPathReference()
    {
        return ! $this->scheme && $this->getAuthority();
    }

    /**
     * Check is the URI an absolute-path reference.
     *
     * @return bool
     */
    public function isAbsolutePathReference()
    {
        return ! $this->scheme && ! $this->getAuthority() && 0 === strpos($this->path, '/');
    }

    /**
     * Check is the URI a relative-path reference.
     *
     * @return bool
     */
    public function isRelativePathReference()
    {
        return ! $this->scheme && ! $this->getAuthority() && 0 !== strpos($this->path, '/');
    }

    /**
     * Validate a URI scheme.
     *
     * @param  string  $scheme  The URI scheme.
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function validateScheme($scheme)
    {
        if ($scheme) {
            if (! preg_match('/^[a-zA-Z][a-zA-Z0-9+\-.]*$/', $scheme)) {
                throw new InvalidArgumentException(
                    "Invalid scheme: {$scheme}! "
                    ."Scheme must be compliant with the \"RFC 3986\" standart."
                );
            }

            return strtolower($scheme);
        }

        return $scheme;
    }

    /**
     * Validate a URI host.
     *
     * @param  string  $host  The URI host.
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHost($host)
    {
        if ($host) {
            //
            // Matching an IPvFuture or an IPv6address.
            //
            if (preg_match('/^\[.+\]$/', $host)) {
                $host = trim($host, '[]');

                //
                // Matching an IPvFuture.
                //
                if (preg_match('/^(v|V)/', $host)) {
                    if (! preg_match('/^(v|V)[a-fA-F0-9]\.([a-zA-Z0-9\-._~]|[!$&\'()*+,;=]|\:)$/', $host)) {
                        throw new InvalidArgumentException(
                            "Invalid host: {$host}! "
                            ."IP address must be compliant with the \"IPvFuture\" of the \"RFC 3986\" standart."
                        );
                    }
                //
                // Matching an IPv6address.
                //
                } else if (false === filter_var($host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
                    throw new InvalidArgumentException(
                        "Invalid host: {$host}! "
                        ."IP address must be compliant with the \"IPv6address\" of the \"RFC 3986\" standart."
                    );
                }

                $host = '['.$host.']';
            //
            // Matching an IPv4address.
            //
            } else if (preg_match('/^([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\./', $host)) {
                if (false === filter_var($host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
                    throw new InvalidArgumentException(
                        "Invalid host: {$host}! "
                        ."IP address must be compliant with the \"IPv4address\" of the \"RFC 3986\" standart."
                    );
                }
            //
            // Matching a domain name.
            //
            } else {
                if (! preg_match('/^([a-zA-Z0-9\-._~]|%[a-fA-F0-9]{2}|[!$&\'()*+,;=])*$/', $host)) {
                    throw new InvalidArgumentException(
                        "Invalid host: {$host}! "
                        ."Host must be compliant with the \"RFC 3986\" standart."
                    );
                }
            }

            return strtolower($host);
        }

        return $host;
    }

    /**
     * Validate a URI port.
     *
     * @param  int|null  $port  The URI port.
     * @return int|null
     *
     * @throws \InvalidArgumentException
     */
    protected function validatePort($port)
    {
        if (null !== $port) {
            if (1 > $port || 65535 < $port) {
                throw new InvalidArgumentException(
                    "Invalid port: {$port}! "
                    ."TCP or UDP port must be between 1 and 65535."
                );
            }

            return static::isStandartPort($this->scheme, $port) ? null : $port;
        }

        return $port;
    }

    /**
     * Validate a URI path.
     *
     * @param  string  $path  The URI path.
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function validatePath($path)
    {
        if (! $this->scheme && 0 === strpos($path, ':')) {
            throw new InvalidArgumentException(
                "Invalid path: {$path}! "
                ."Path of a URI without a scheme cannot begin with a colon."
            );
        }

        $authority = $this->getAuthority();

        if (! $authority && 0 === strpos($path, '//')) {
            throw new InvalidArgumentException(
                "Invalid path: {$path}! "
                ."Path of a URI without an authority cannot begin with two slashes."
            );
        }

        if ($authority && $path && 0 !== strpos($path, '/')) {
            throw new InvalidArgumentException(
                "Invalid path: {$path}! "
                ."Path of a URI with an authority must be empty or begin with a slash."
            );
        }

        if ($path && '/' !== $path) {
            if (! preg_match('/^([a-zA-Z0-9\-._~]|%[a-fA-F0-9]{2}|[!$&\'()*+,;=]|\:|\@|\/|\%)*$/', $path)) {
                throw new InvalidArgumentException(
                    "Invalid path: {$path}! "
                    ."Path must be compliant with the \"RFC 3986\" standart."
                );
            }

            return preg_replace_callback('/(?:[^a-zA-Z0-9\-._~!$&\'()*+,;=:@\/%]++|%(?![a-fA-F0-9]{2}))/', function ($matches) {
                return rawurlencode($matches[0]);
            }, $path);
        }

        return $path;
    }

    /**
     * Validate a URI query.
     *
     * @param  string  $query  The URI query.
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function validateQuery($query)
    {
        if ($query) {
            if (! preg_match('/^([a-zA-Z0-9\-._~]|%[a-fA-F0-9]{2}|[!$&\'()*+,;=]|\:|\@|\/|\?|\%)*$/', $query)) {
                throw new InvalidArgumentException(
                    "Invalid query: {$query}! "
                    ."Query must be compliant with the \"RFC 3986\" standart."
                );
            }

            return preg_replace_callback('/(?:[^a-zA-Z0-9\-._~!$&\'()*+,;=:@\/?%]++|%(?![a-fA-F0-9]{2}))/', function ($matches) {
                return rawurlencode($matches[0]);
            }, $query);
        }

        return $query;
    }

    /**
     * Validate a URI fragment.
     *
     * @param  string  $fragment  The URI fragment.
     * @return string
     */
    protected function validateFragment($fragment)
    {
        if ($fragment) {
            return preg_replace_callback('/(?:[^a-zA-Z0-9\-._~!$&\'()*+,;=:@\/?%]++|%(?![a-fA-F0-9]{2}))/', function ($matches) {
                return rawurlencode($matches[0]);
            }, $fragment);
        }

        return $fragment;
    }
}
