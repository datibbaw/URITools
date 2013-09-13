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
            url_replace('http://www.example.org', PHP_URL_SCHEME, function($scheme) {
                return 'https';
            })
        );

        $this->assertEquals(
            'mailto:foo@example.org',
            url_replace('sip:foo@example.org', PHP_URL_SCHEME, function($scheme) {
                return 'mailto';
            })
        );
    }

    public function testPath()
    {
        $this->assertEquals(
            'http://www.example.org/',
            url_replace('http://www.example.org/foobar', PHP_URL_PATH, function($path) {
                return '/';
            })
        );
    }
}
