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
    const SCHEME   = 'scheme';
    const USER     = 'user';
    const PASS     = 'pass';
    const HOST     = 'host';
    const PORT     = 'port';
    const PATH     = 'path';
    const QUERY    = 'query';
    const FRAGMENT = 'fragment';

    // We expose our private parts via ArrayAccess ;-)
    /**
     * @var array
     */
    protected $parts = array();

    // obviously these two properties would be handled internally in a native impl
    protected static $constPropMap = [
        self::SCHEME,
        self::USER,
        self::PASS,
        self::HOST,
        self::PORT,
        self::PATH,
        self::QUERY,
        self::FRAGMENT,
    ];

    private function validateScheme($value)
    {
        if (!preg_match('/^[a-z][a-z0-9+.\-]*$/i', $value)) {
            throw new InvalidArgumentException(sprintf("'%s': Invalid URI Scheme", $value));
        }
        return $value;
    }

    public function __construct($uri)
    {
        if (($this->parts = parse_url($uri)) === false) {
            throw new InvalidArgumentException('Invalid URI');
        }

        $this->setQuery($this->get(self::QUERY));
    }

    protected function setQuery($value)
    {
        if ($value !== null) {
            parse_str($value, $query);
        } else {
            $query = [];
        }
        $this->parts[self::QUERY] = new QueryLevel($query);
    }

    protected function get($name)
    {
        return isset($this->parts[$name]) ? $this->parts[$name] : null;
    }

    /* Magic methods */

    public function __get($name)
    {
        if (!in_array($name, self::$constPropMap)) {
            throw new InvalidArgumentException(sprintf("'%s': Invalid URI Part", $name));
        }

        return $this->get($name);
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case self::QUERY:
                $this->setQuery($value);
                return;

            case self::PORT:
                $value = (int)$value;
                break;

            case self::SCHEME:
                $value = $this->validateScheme($value);
                break;

            default:
                if (!in_array($name, self::$constPropMap)) {
                    throw new InvalidArgumentException(sprintf("'%s': Invalid URI Part", $name));
                }
        }

        $this->parts[$name] = $value;
    }

    public function __toString()
    {
        return self::asString($this->parts);
    }

    /* JsonSerializable */

    public function jsonSerialize()
    {
        return $this->__toString();
    }

    /* Static methods */
    public static function asString(array $parts)
    {
        $result = '';

        if (isset($parts[self::SCHEME])) {
            $result = $parts[self::SCHEME] . ':';
        }

        if (isset($parts[self::HOST])) {
            $result .= '//';

            if (isset($parts[self::USER])) {
                $result .= urlencode($parts[self::USER]);

                if (isset($parts[self::PASS])) {
                    $result .= ':' . urlencode($parts[self::PASS]);
                }

                $result .= '@';
            }

            $result .= urlencode($parts[self::HOST]);

            if (isset($parts[self::PORT])) {
                $result .= ':' . $parts[self::PORT];
            }
        }

        if (isset($parts[self::PATH])) {
            $result .= implode('/', array_map('urlencode', preg_split('~/+~', $parts[self::PATH])));
        }

        if (isset($parts[self::QUERY]) && count($parts[self::QUERY])) {
            $result .= '?' . $parts[self::QUERY];
        }

        if (isset($parts[self::FRAGMENT])) {
            $result .= '#' . urlencode($parts[self::FRAGMENT]);
        }

        return $result;
    }
}