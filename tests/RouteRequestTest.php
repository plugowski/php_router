<?php
namespace PhpRouter\Test;

use PhpRouter\RouteRequest;
use PHPUnit_Framework_TestCase;

/**
 * Class RouteRequestTest
 * @package PhpRouter\Test
 */
class RouteRequestTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_SERVER = array (
                'REDIRECT_UNIQUE_ID' => 'djskjhdfkgjhdkfj',
                'REDIRECT_STATUS' => '200',
                'UNIQUE_ID' => 'sdjhlfkhdjfgldf',
                'HTTP_HOST' => 'routers.lc',
                'HTTP_CONNECTION' => 'keep-alive',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
                'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.103 Safari/537.36',
                'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
                'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,no;q=0.6,pl;q=0.4',
                'HTTP_COOKIE' => 'XDEBUG_SESSION=XDEBUG_PHPSTORM',
                'PATH' => '/opt/local/bin',
                'SERVER_SIGNATURE' => '',
                'SERVER_SOFTWARE' => 'Apache/2.2.29 (Unix) mod_ssl/2.2.29 OpenSSL/1.0.2a DAV/2 PHP/5.4.35',
                'SERVER_NAME' => 'routers.lc',
                'SERVER_ADDR' => '127.0.0.1',
                'SERVER_PORT' => '80',
                'REMOTE_ADDR' => '127.0.0.1',
                'DOCUMENT_ROOT' => '/username/php_router',
                'SERVER_ADMIN' => 'you@example.com',
                'SCRIPT_FILENAME' => '/username/php_router/index.php',
                'REMOTE_PORT' => '50630',
                'REDIRECT_QUERY_STRING' => 'url=',
                'REDIRECT_URL' => '/route/test/666',
                'GATEWAY_INTERFACE' => 'CGI/1.1',
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'REQUEST_METHOD' => 'GET',
                'QUERY_STRING' => 'url=',
                'REQUEST_URI' => '/route/test/666',
                'SCRIPT_NAME' => '/index.php',
                'PHP_SELF' => '/index.php',
                'REQUEST_TIME_FLOAT' => 1455567463.822,
                'REQUEST_TIME' => 1455567463,
            );
        
        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldBuildRequestObject()
    {
        $request = new RouteRequest();

        $this->assertEquals('GET', $request->getRequestMethod());
        $this->assertEquals('/route/test/666', $request->getRequestUrl());
        $this->assertEquals('http', $request->getProtocol());
        $this->assertEquals([], $request->getQueryParams());
        $this->assertFalse($request->isAjax());
    }

    /**
     * @test
     */
    public function shouldBuildRequestWithQueryStringAndAjax()
    {
        $_SERVER['QUERY_STRING'] = 'url=&myVar=1';
        $_SERVER['REQUEST_URI'] = '/class/adjkgdhkjfbk-djhgk?myVar=1';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $_SERVER['HTTPS'] = 'on';

        $request = new RouteRequest();

        $this->assertTrue($request->isAjax());
        $this->assertTrue(isset($request->getQueryParams()['myVar']));
        $this->assertEquals('https', $request->getProtocol());

    }

    /**
     * @test
     */
    public function shouldGetMethodFromPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'PUT';

        $request = new RouteRequest();

        $this->assertEquals('PUT', $request->getRequestMethod());
    }

    /**
     * @test
     */
    public function shouldUseMethodOverwrite()
    {
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'DELETE';

        $request = new RouteRequest();

        $this->assertEquals('DELETE', $request->getRequestMethod());
    }
}