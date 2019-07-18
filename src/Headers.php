<?php

use InvalidArgumentException;

class Headers
{
    /**
     * The array of all of the headers.
     *
     * Note: The array keys are the normalized
     * header names while the array values contains
     * the original header name and the array of header values.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Get the array
     * of all of the headers in the collection.
     *
     * @return array
     */
    public function all()
    {
        foreach ($this->headers as $header) {
            $headers[$header['name']] = $header['value'];
        }

        return $headers;
    }

    /**
     * Check is the header exists in the collection.
     *
     * @param  string  $name  The header name.
     * @return bool
     */
    public function has($name)
    {
        return isset($this->headers[$this->normalizeName($name)]);
    }

    /**
     * Get the array of header values from the collection.
     *
     * @param  string  $name  The header name.
     * @return string[]
     */
    public function get($name)
    {
        $name = $this->normalizeName($name);

        return isset($this->headers[$name]) ? $this->headers[$name]['value'] : [];
    }

    /**
     * Get the header line from the collection.
     *
     * @param  string  $name  The header name.
     * @return string
     */
    public function getLine($name)
    {
        return implode(', ', $this->get($name));
    }

    /**
     * Set a header to the collection.
     *
     * @param  string  $name  The header name.
     * @param  string|string[]  $value  The header value.
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        $name = $this->validateName($name);
        $value = $this->validateValue((array) $value);

        $this->headers[$this->normalizeName($name)] = compact('name', 'value');

        return $this;
    }

    /**
     * Add a header to the collection.
     *
     * @param  string  $name  The header name.
     * @param  string|string[]  $value  The header value.
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function add($name, $value)
    {
        $name = $this->validateName($name);
        $value = $this->validateValue((array) $value);

        $normalizedName = $this->normalizeName($name);

        if (! isset($this->headers[$normalizedName])) {
            $this->headers[$normalizedName] = compact('name', 'value');
        } else {
            $this->headers[$normalizedName]['value'] = array_merge($this->headers[$normalizedName]['value'], $value);
        }

        return $this;
    }

    /**
     * Remove the header from the collection.
     *
     * @param  string  $name  The header name.
     * @return $this
     */
    public function remove($name)
    {
        unset($this->headers[$this->normalizeName($name)]);

        return $this;
    }

    /**
     * Normalize a header name.
     *
     * @param  string  $name  The header name.
     * @return string
     */
    protected function normalizeName($name)
    {
        return implode('-', array_map('ucfirst', explode('-', strtolower($name))));
    }

    /**
     * Validate a header name.
     *
     * @param  string  $name  The header name.
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function validateName($name)
    {
        if (! preg_match('/^[!#$%&\'*+\-.^_`|~0-9a-zA-Z]+$/', $name)) {
            throw new InvalidArgumentException(
                "Invalid header name: {$name}! "
                ."Header name must be compliant with the \"RFC 7230\" standart."
            );
        }

        return $name;
    }

    /**
     * Validate a header value.
     *
     * @param  string[]  $value  The header value.
     * @return string[]
     *
     * @throws \InvalidArgumentException
     */
    protected function validateValue(array $value)
    {
        foreach ($value as $val) {
            if (preg_match('/(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))/', $val)) {
                throw new InvalidArgumentException(
                    "Invalid header value: {$val}! "
                    ."Header value must be compliant with the \"RFC 7230\" standart."
                );
            }

            for ($i = 0; $i < strlen($val); $i++) {
                $ascii = ord($val[$i]);

                if ((32 > $ascii && (9 !== $ascii && 10 !== $ascii && 13 !== $ascii)) || 127 === $ascii || 254 < $ascii) {
                    throw new InvalidArgumentException(
                        "Invalid header value: {$val}! "
                        ."Header value must be compliant with the \"RFC 7230\" standart."
                    );
                }
            }
        }

        return $value;
    }
}
