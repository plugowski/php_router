<?php
namespace PhpRouter\Test;

use PhpRouter\Route;
use PhpRouter\RouteCollection;
use PhpRouter\Router;
use PHPUnit_Framework_TestCase;

/**
 * Class RouterTest
 * @package PhpRouter\Test
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    protected $collection;

    public function setUp()
    {
        $this->collection = new RouteCollection();
        $this->collection->attach(new Route('GET /', function(){ return 1; }));
        $this->collection->attach(new Route('GET /about_me', function(){ return 2; }));
        $this->collection->attach(new Route('POST /about_me', function(){ return 3; }));
        $this->collection->attach(new Route('GET /contact/@id', ['id' => '\w{3}\:\w{3}'], function($p){ return 'regex_' . $p['id']; }));
        $this->collection->attach(new Route('GET /contact/@id', function($p){ return $p['id']; }));
        $this->collection->attach(new Route('GET /ajax [ajax]', function(){ return 4; }));
        $this->collection->attach(new Route('GET /nonajax', function(){ return 5; }));

        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldMatchRouteAndDispatch()
    {
        $request = $this->getRequestMock('GET', '/about_me');
        $router = new Router($request, $this->collection);

        $this->assertEquals(2, $router->run());

        $request = $this->getRequestMock('POST', '/about_me');
        $router = new Router($request, $this->collection);

        $this->assertEquals(3, $router->run());
    }

    /**
     * @test
     */
    public function shouldCallAjaxRouteWithAjaxFalse()
    {
        $this->setExpectedException('PhpRouter\Exception\RouteNotFoundException');

        $request = $this->getRequestMock('GET', '/ajax');
        $router = new Router($request, $this->collection);

        $router->run();
    }

    /**
     * @test
     */
    public function shouldCallAjaxRouteWithAjaxTrue()
    {
        $request = $this->getRequestMock('GET', '/ajax', true);
        $router = new Router($request, $this->collection);

        $this->assertEquals(4, $router->run());
    }

    /**
     * @test
     */
    public function shouldCallNonAjaxRouteWithAjax()
    {
        $request = $this->getRequestMock('GET', '/nonajax', true);
        $router = new Router($request, $this->collection);

        $this->assertEquals(5, $router->run());
    }

    /**
     * @test
     */
    public function shouldCallRouteWithParam()
    {
        $request = $this->getRequestMock('GET', '/contact/test_123');
        $router = new Router($request, $this->collection);

        $this->assertEquals('test_123', $router->run());

        $request = $this->getRequestMock('GET', '/contact/xxx:xxx');
        $router = new Router($request, $this->collection);

        $this->assertEquals('regex_xxx:xxx', $router->run());
    }

    /**
     * @param string $method
     * @param string $url
     * @param bool $isAjax
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestMock($method, $url, $isAjax = false)
    {
        $request = $this->getMockBuilder('PhpRouter\RouteRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->any())
            ->method('getRequestUrl')
            ->willReturn($url);

        $request
            ->expects($this->any())
            ->method('getRequestMethod')
            ->willReturn($method);

        $request
            ->expects($this->any())
            ->method('isAjax')
            ->willReturn($isAjax);

        return $request;
    }
}