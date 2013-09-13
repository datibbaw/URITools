<?php

namespace URITools;

use InvalidArgumentException;
use JsonSerializable;

/**
 * @property $scheme string
 * @property $user string
 * @property $pass string
 * @property $host string
 * @property $port string
 * @property $path string
 * @property $query QueryLevel
 * @property $fragment string
 */
class URI implements JsonSerializable
{
    protected $scheme;
    protected $user;
    protected $pass;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    public function __construct($uri)
    {
        if (($parts = parse_url($uri)) === false) {
            throw new InvalidArgumentException('Invalid URI');
        }

        foreach ($parts as $name => $value) {
            $this->$name = $value;
        }

        $this->setQuery($this->query);
    }

    public function setScheme($value)
    {
        if ($value !== null && !preg_match('/^[a-z][a-z0-9+.\-]*$/i', $value)) {
            throw new InvalidArgumentException(sprintf("'%s': Invalid URI Scheme", $value));
        }
        $this->scheme = $value;
    }

    public function setPort($value)
    {
        if ($value !== null) {
            $value = (int)$value;
        }
        $this->port = $value;
    }

    public function setQuery($value)
    {
        if ($value !== null) {
            parse_str($value, $query);
        } else {
            $query = [];
        }
        $this->query = new QueryString($query);
    }

    public function asString()
    {
        $result = '';

        if ($this->scheme !== null) {
            $result = $this->scheme . ':';
        }

        if ($this->host !== null) {
            $result .= '//';

            if ($this->user !== null) {
                $result .= urlencode($this->user);

                if ($this->pass !== null) {
                    $result .= ':' . urlencode($this->pass);
                }

                $result .= '@';
            }

            $result .= urlencode($this->host);

            if ($this->port) {
                $result .= ':' . $this->port;
            }
        }

        if ($this->path) {
            $result .= implode('/', array_map('urlencode', preg_split('~/+~', $this->path)));
        }

        if (count($this->query)) {
            $result .= '?' . $this->query;
        }

        if ($this->fragment !== null) {
            $result .= '#' . urlencode($this->fragment);
        }

        return $result;
    }

    /* Magic methods */

    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException(sprintf("'%s': Invalid URI Part", $name));
        }

        return $this->$name;
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'query':
                $this->setQuery($value);
                return;

            case 'port':
                $this->setPort($value);
                return;

            case 'scheme':
                $this->setScheme($value);
                return;

            default:
                if (!property_exists($this, $name)) {
                    throw new InvalidArgumentException(sprintf("'%s': Invalid URI Part", $name));
                }
        }

        $this->$name = $value;
    }

    public function __toString()
    {
        return $this->asString();
    }

    /* JsonSerializable */

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}