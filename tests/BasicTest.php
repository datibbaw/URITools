<?php
/**
 * Created by PhpStorm.
 * User: tjerk
 * Date: 13/9/13
 * Time: 9:49 PM
 */

class BasicTest extends PHPUnit_Framework_TestCase
{
    public function testScheme()
    {
        $this->assertEquals(
            'https://www.example.org',
            url_replace('http://www.example.org', 'https', PHP_URL_SCHEME)
        );

        $this->assertEquals(
            'mailto:foo@example.org',
            url_replace('sip:foo@example.org', 'mailto', PHP_URL_SCHEME)
        );
    }

    public function testPath()
    {
        $this->assertEquals(
            'http://www.example.org/foobar/',
            url_replace('http://www.example.org/foobar', function($path) {
                return $path ? $path . '/' : '/';
            }, PHP_URL_PATH)
        );
    }

    public function testRemoveQuery()
    {
        $this->assertEquals(
            'http://www.example.org/foobar#test',
            url_replace('http://www.example.org/foobar?bar=baz#test', null, PHP_URL_QUERY)
        );
    }
}
