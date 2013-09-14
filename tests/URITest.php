<?php
use URITools\ArrayAccessURI;
use URITools\URI;

class URITest extends PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $uri = new URI('http://www.example.org');
        $this->assertEquals($uri, $uri->asString());

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

    public function testBuilder()
    {
        $uri = new URI();
        $uri->setScheme('http');
        $uri->setUser('foo');
        $uri->setPass('bar');
        $uri->setHost('www.example.org');
        $uri->setPort(8080);
        $uri->setPath('/hello');
        $uri->setQuery('bar=baz');
        $uri->setFragment('baz');

        $this->assertEquals(
            'http://foo:bar@www.example.org:8080/hello?bar=baz#baz',
            $uri
        );

        $uri = new URI();
        $uri->scheme = 'http';
        $uri->user = 'foo';
        $uri->pass = 'bar';
        $uri->host = 'www.example.org';
        $uri->port = 8080;
        $uri->path = '/hello';
        $uri->query = 'bar=baz';
        $uri->fragment = 'baz';

        $this->assertEquals(
            'http://foo:bar@www.example.org:8080/hello?bar=baz#baz',
            $uri
        );
    }

    public function testDecompose()
    {
        $uri = new URI('http://foo:bar@www.example.org:8080/hello?bar=baz#baz');

        $this->assertEquals('http', $uri->scheme);
        $this->assertEquals('http', $uri->getScheme());

        $this->assertEquals('foo', $uri->user);
        $this->assertEquals('foo', $uri->getUser());

        $this->assertEquals('bar', $uri->pass);
        $this->assertEquals('bar', $uri->getPass());

        $this->assertEquals('www.example.org', $uri->host);
        $this->assertEquals('www.example.org', $uri->getHost());

        $this->assertEquals(8080, $uri->port);
        $this->assertEquals(8080, $uri->getPort());

        $this->assertEquals('/hello', $uri->path);
        $this->assertEquals('/hello', $uri->getPath());

        $this->assertEquals('bar=baz', $uri->query);
        $this->assertEquals('bar=baz', $uri->getQuery());

        $this->assertEquals('baz', $uri->fragment);
        $this->assertEquals('baz', $uri->getFragment());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidScheme()
    {
        $uri = new URI();
        $uri->scheme = 1;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidURI()
    {
        new URI('http://host:-1');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidPart()
    {
        $uri = new URI();
        $uri->schemer = 0;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidPart()
    {
        $uri = new URI();
        $test = $uri->schemer;
    }

    public function testJson()
    {
        $uri = new URI('http://foo.bar/');
        $this->assertEquals(
            '"http:\/\/foo.bar\/"',
            json_encode($uri)
        );
    }
}