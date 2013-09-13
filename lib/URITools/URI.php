<?php

namespace URITools;

class URI implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable
{
    const SCHEME   = 0x01;
    const USER     = 0x02;
    const PASS     = 0x04;
    const HOST     = 0x08;
    const PORT     = 0x10;
    const PATH     = 0x20;
    const QUERY    = 0x40;
    const FRAGMENT = 0x80;

    // The private props and magic methods are only implemented like this to give type validation,
    // these are effectively public properties
    private $scheme;
    private $user;
    private $pass;
    private $host;
    private $port;
    private $path;
    private $query;
    private $fragment;

    // obviously these two properties would be handled internally in a native impl
    private static $constPropMap = [
        self::SCHEME   => 'scheme',
        self::USER     => 'user',
        self::PASS     => 'pass',
        self::HOST     => 'host',
        self::PORT     => 'port',
        self::PATH     => 'path',
        self::QUERY    => 'query',
        self::FRAGMENT => 'fragment',
    ];
    private $iterationPointer = self::SCHEME;

    private function validateScheme($value)
    {
        // in the generic URI syntax, only the format of the scheme is rigid
        return (bool) preg_match('/^[a-z][a-z0-9+.\-]*$/i', $value);
    }

    public function __construct($uri)
    {
        $parts = [];
        $this->query = new QueryLevel;

        if (((string) $uri) !== '' && false === $parts = parse_url($uri)) {
            throw new \InvalidArgumentException('Invalid URI');
        }

        foreach ($parts as $name => $value) {
            $this->__set($name, urldecode($value));
        }
    }

    public function __get($name)
    {
        if (!in_array($name, self::$constPropMap)) {
            trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_NOTICE);
            return null;
        }

        return $this->$name;
    }

    public function __set($name, $value)
    {
        if ($value === null) {
            $this->$name = null;
        } else if ($name === 'port') {
            $this->port = (int) $value;
        } else if ($name === 'query') {
            parse_str($value, $query);
            $this->query = new QueryLevel($query);
        } else if (in_array($name, self::$constPropMap)) {
            if ($name === 'scheme' && !$this->validateScheme($value)) {
                throw new \InvalidArgumentException('Invalid URI scheme');
            }

            $this->$name = (string) $value;
        } else {
            // because PHP allows expando properties on anything afaik :-(
            $this->$name = $value;
        }
    }

    public function __toString()
    {
        $result = '';

        if (isset($this->scheme)) {
            $result = $this->scheme . ':';
        }

        if (isset($this->host)) {
            $result .= '//';

            if (isset($this->user)) {
                $result .= urlencode($this->user);

                if (isset($this->pass)) {
                    $result .= ':' . urlencode($this->pass);
                }

                $result .= '@';
            }

            $result .= urlencode($this->host);

            if (isset($this->port)) {
                $result .= ':' . $this->port;
            }
        }

        if (isset($this->path)) {
            $result .= implode('/', array_map('urlencode', preg_split('~/+~', $this->path)));
        }

        if (count($this->query)) {
            $result .= '?' . $this->query;
        }

        if (isset($this->fragment)) {
            $result .= '#' . urlencode($this->fragment);
        }

        return $result;
    }

    /* ArrayAccess */

    public function offsetExists($name)
    {
        return isset($this->$name) || isset(self::$constPropMap[$name]);
    }

    public function offsetGet($name)
    {
        if (isset(self::$constPropMap[$name])) {
            return $this->__get(self::$constPropMap[$name]);
        } else {
            return $this->__get($name);
        }
    }

    public function offsetSet($name, $value)
    {
        if (isset(self::$constPropMap[$name])) {
            $this->__set(self::$constPropMap[$name], $value);
        } else {
            $this->__set($name, $value);
        }
    }

    public function offsetUnset($name)
    {
        if (isset(self::$constPropMap[$name])) {
            $this->__set(self::$constPropMap[$name], null);
        } else if (in_array($name, self::$constPropMap)) {
            $this->__set($name, null);
        } else {
            unset($this->$name);
        }
    }

    /* Iterator */

    public function current()
    {
        return $this->{self::$constPropMap[$this->iterationPointer]};
    }

    public function key()
    {
        return self::$constPropMap[$this->iterationPointer];
    }

    public function next()
    {
        $this->iterationPointer *= 2;
    }

    public function rewind()
    {
        $this->iterationPointer = self::SCHEME;
    }

    public function valid()
    {
        return $this->iterationPointer <= self::FRAGMENT;
    }

    /* Countable */

    public function count()
    {
        $result = 0;

        foreach (self::$constPropMap as $const => $name) {
            if ($this->$name !== null) {
                $result++;
            }
        }

        return $result;
    }

    /* JsonSerializable */

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}