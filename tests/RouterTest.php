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
        parent::setUp();

        $this->collection = new RouteCollection();
        $this->collection->attach(new Route('DELETE /', function(){ return 1; }));
        $this->collection->attach(new Route('GET /about_me', function(){ return 2; }));
        $this->collection->attach(new Route('GET /contact', function(){ return 3; }));
        $this->collection->attach(new Route('GET /contact [ajax]', function(){ return 4; }));
        $this->collection->attach(new Route('POST /contact [ajax]', function(){ return 4; }));
    }

    /**
     * @test
     */
    public function shouldMatchRouteAndDispatch()
    {
        $request = $this->getRequestMock('GET', '/about_me');
        $router = new Router($request, $this->collection);

        $this->assertEquals(2, $router->run());
    }

    /**
     * @test
     */
    public function shouldMismatchAjaxRoute()
    {
        $request = $this->getRequestMock('GET', '/contacts', true);
        $router = new Router($request, $this->collection);

        $this->assertNull($router->run());
    }

    /**
     * @test
     */
    public function shouldMatchAjaxRoute()
    {
        $request = $this->getRequestMock('POST', '/contact', true);
        $router = new Router($request, $this->collection);

        $this->assertEquals(4, $router->run());
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