<?php

namespace URITools;

use ArrayAccess;

class ArrayAccessURI extends URI implements ArrayAccess
{
    /* ArrayAccess */

    public function offsetExists($name)
    {
        return isset($this->parts[$name]) || in_array($name, self::$constPropMap);
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
        $this->__set($name, null);
    }
}