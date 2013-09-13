<?php

namespace URITools;

class QueryLevel implements \ArrayAccess, \Iterator, \Countable
{
    private $elements = [];

    private $iterationValid;

    private function encodeElement($name, $value, $nameFormat = '%s')
    {
        $result = null;

        if ($value !== null) {
            $name = sprintf($nameFormat, $name);

            if ($value instanceof QueryLevel) {
                $result = [];

                foreach ($value as $subName => $subValue) {
                    $result[] = $this->encodeElement($subName, $subValue, $name . '[%s]');
                }

                $result = implode('&', $result);
            } else {
                if (is_bool($value)) {
                    $value = (int) $value;
                }

                $result = urlencode($name) . '=' . urlencode($value);
            }
        }

        return $result;
    }

    public function __construct($elements = [])
    {
        if (!is_array($elements) && !($elements instanceof QueryLevel)) {
            if (is_object($elements)) {
                $elements = get_object_vars($elements);
            } else {
                $elements = (array) $elements;
            }
        }

        foreach ($elements as $name => $value) {
            $this->__set($name, $value);
        }
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->elements)) {
            trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_NOTICE);
            return null;
        }

        return $this->elements[$name];
    }

    public function __set($name, $value)
    {
        if (is_scalar($value) || $value === null) {
            $this->elements[$name] = $value;
        } else {
            $this->elements[$name] = new static($value);
        }
    }

    public function __toString()
    {
        $result = [];

        foreach ($this->elements as $name => $value) {
            if (!is_numeric($name) && null !== $encoded = $this->encodeElement($name, $value)) {
                $result[] = $encoded;
            }
        }

        return implode('&', $result);
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

    /* Iterator */

    public function current()
    {
        return current($this->elements);
    }

    public function key()
    {
        return key($this->elements);
    }

    public function next()
    {
        next($this->elements);
    }

    public function rewind()
    {
        reset($this->elements);
    }

    public function valid()
    {
        return key($this->elements) !== null;
    }

    /* Countable */

    public function count()
    {
        return count($this->elements);
    }
}