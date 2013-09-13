<?php
use URITools\ArrayAccessURI;
use URITools\URI;

class URITest extends PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $uri = new URI('http://www.example.org');
        $uri->scheme = 'https';

        $this->assertEquals(
            'https://www.example.org',
            $uri
        );


        $uri->path = '/';
        $this->assertEquals(
            'https://www.example.org/',
            $uri
        );
    }

    public function testQuery()
    {
        $uri = new URI('http://www.google.com/');
        $uri->path = '/somepath//whatever you want';
        $uri->query['foo'] = 'bar';
        $uri->query['baz'] = [1,2,3];

        $this->assertEquals(
            'http://www.google.com/somepath/whatever+you+want?foo=bar&baz%5B0%5D=1&baz%5B1%5D=2&baz%5B2%5D=3',
            $uri
        );
    }

    public function testArrayAccess()
    {
        $uri = new ArrayAccessURI('http://www.google.com/');
        $uri['path'] = '/somepath//whatever you want';
        $uri['query']['foo'] = 'bar';
        $uri['query']['baz'] = [1,2,3];

        $this->assertEquals(
            'http://www.google.com/somepath/whatever+you+want?foo=bar&baz%5B0%5D=1&baz%5B1%5D=2&baz%5B2%5D=3',
            $uri
        );
    }
}