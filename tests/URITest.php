<?php
/**
 * Created by PhpStorm.
 * User: tjerk
 * Date: 13/9/13
 * Time: 9:51 PM
 */

class URITest extends PHPUnit_Framework_TestCase
{

    public function testBasic()
    {
        $uri = new \URITools\URI('http://www.example.org');
        $uri[\URITools\URI::SCHEME] = 'https';

        $this->assertEquals(
            'https://www.example.org',
            $uri
        );
    }
}
 