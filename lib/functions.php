<?php

function url_replace($url, $replace, $component = null)
{
    static $map = array(
        PHP_URL_SCHEME      => 'scheme',
        PHP_URL_USER        => 'user',
        PHP_URL_PASS        => 'pass',
        PHP_URL_HOST        => 'host',
        PHP_URL_PORT        => 'port',
        PHP_URL_PATH        => 'path',
        PHP_URL_QUERY       => 'query',
        PHP_URL_FRAGMENT    => 'fragment',
    );

    if ($component !== null && !isset($map[$component])) {
        return $url;
    }

    if (($parts = parse_url($url)) === false) {
        return $url;
    }

    if ($component !== null) {
        $key = $map[$component];
        // perform replacement
        if (is_string($replace) || is_null($replace)) {
            $parts[$key] = $replace;
        } else {
            $old = isset($parts[$key]) ? $parts[$key] : null;
            $parts[$key] = call_user_func($replace, $old, $component);
        }
    } elseif (is_callable($replace)) {
        $parts = call_user_func($replace, $parts);
    }

    return url_recombine($parts);
}

/**
 * @param $parts
 * @return string
 */
function url_recombine(array $parts)
{
    $result = '';

    if (isset($parts['scheme'])) {
        $result = $parts['scheme'] . ':';
    }

    if (isset($parts['host'])) {
        $result .= '//';

        if (isset($parts['user'])) {
            $result .= $parts['user'];

            if (isset($parts['pass'])) {
                $result .= ':' . $parts['pass'];
            }

            $result .= '@';
        }

        $result .= $parts['host'];

        if (isset($parts['port'])) {
            $result .= ':' . $parts['port'];
        }
    }

    if (isset($parts['path'])) {
        $result .= $parts['path'];
    }

    if (isset($parts['query'])) {
        $result .= '?' . $parts['query'];
    }

    if (isset($parts['fragment'])) {
        $result .= '#' . $parts['fragment'];
    }

    return $result;
}