<?php

namespace URITools;

use ArrayAccess;
use Countable;

class QueryString implements ArrayAccess, Countable
{
    private $elements = [];

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->elements)) {
            return null;
        }

        return $this->elements[$name];
    }

    public function __set($name, $value)
    {
        $this->elements[$name] = $value;
    }

    public function __toString()
    {
        return http_build_query($this->elements);
    }

    /* ArrayAccess */

    public function offsetExists($name)
    {
        return isset($this->elements[$name]);
    }

    public function offsetGet($name)
    {
        return $this->__get($name);
    }

    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    public function offsetUnset($name)
    {
        unset($this->elements[$name]);
    }

    /* Countable */

    public function count()
    {
        return count($this->elements);
    }
}