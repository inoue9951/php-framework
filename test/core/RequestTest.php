<?php
use PHPUnit\Framework\TestCase;
use Core\Request;

class RequestTest extends TestCase
{
    protected $request;

    protected function setUp()
    {
        $this->request = new Request();
    }

    public function testGetRequestUri()
    {
        $_SERVER['REQUEST_URI'] = 'test_request_uri';
        $this->assertEquals($_SERVER['REQUEST_URI'], $this->request->getRequestUri());
    }

    public function testGetHost()
    {
        $_SERVER['HTTP_HOST'] = null;
        $_SERVER['SERVER_NAME'] = 'test_server_name';
        $this->assertEquals($_SERVER['SERVER_NAME'], $this->request->getHost());

        $_SERVER['HTTP_HOST'] = 'test_http_host';
        $this->assertEquals($_SERVER['HTTP_HOST'], $this->request->getHost());
    }

    public function testGetBaseUrl()
    {
        $_SERVER['SCRIPT_NAME'] = '/test/script/name';
        $_SERVER['REQUEST_URI'] = '/test/script/uri';
        $this->assertEquals('/test/script', $this->request->getBaseUrl());

        $_SERVER['SCRIPT_NAME'] = '/test.php';
        $_SERVER['REQUEST_URI'] = '/test.php/request';
        $this->assertEquals('/test.php', $this->request->getBaseUrl());

        $_SERVER['SCRIPT_NAME'] = '/test.php';
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertEquals('', $this->request->getBaseUrl());
    }

    public function testGetPathInfo()
    {
        $_SERVER['SCRIPT_NAME'] = '/test/script/name';
        $_SERVER['REQUEST_URI'] = '/test/script/uri';
        $this->assertEquals('/uri', $this->request->getPathInfo());

        $_SERVER['SCRIPT_NAME'] = '/test.php';
        $_SERVER['REQUEST_URI'] = '/test.php/request';
        $this->assertEquals('/request', $this->request->getPathInfo());

        $_SERVER['SCRIPT_NAME'] = '/test.php';
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertEquals('/', $this->request->getPathInfo());

    }

    public function testGetRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'get';
        $this->assertEquals(mb_strtoupper($_SERVER['REQUEST_METHOD']), $this->request->getRequestMethod());

        $_REQUEST['method'] = 'post';
        $this->assertEquals(mb_strtoupper($_REQUEST['method']), $this->request->getRequestMethod());
    }
}
