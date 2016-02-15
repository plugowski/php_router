<?php
namespace PhpRouter\Test;

use PhpRouter\Route;
use PHPUnit_Framework_TestCase;

/**
 * Class RouteTest
 */
class RouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateCorrectRouteObject()
    {
        $route = new Route('GET /test/page.html', 'A->index');

        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('/test/page.html', $route->getUrl());
        $this->assertEquals('sync', $route->getType());
    }

    /**
     * @test
     */
    public function shouldCreateRouteWithDefiniedNamedParam()
    {
        $route = new Route('GET|POST /some_page/@id', ['id' => '\d{2}\-\w{4}'], function($param){
            return $param['id'];
        });

        $route->parseParams('/some_page/12-zaqw');
        $this->assertEquals('12-zaqw', $route->dispatch());
    }

    /**
     * @test
     */
    public function shouldThrowWrongCallbackException()
    {
        $this->setExpectedException('Exception');
        $route = new Route('GET /test.html', 'nothingSpecial');
        $route->dispatch();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidRouteException()
    {
        $this->setExpectedException('Exception');
        new Route('/test.html', 'A->index');
    }

    /**
     * @test
     */
    public function shouldThrowInvalidTypeException()
    {
        $this->setExpectedException('Exception');
        new Route('GET /test.html [not_exists]', 'A->index');
    }

    /**
     * @test
     */
    public function shouldCallAndExecuteAnonymousFunction()
    {
        $route = new Route('GET /test.html [ajax]', function(){
            echo 'XXX';
        });

        ob_start();
        $route->dispatch();
        $result = ob_get_clean();

        $this->assertEquals('XXX', $result);
    }

    /**
     * @test
     */
    public function shouldThrowClasOrMethodNotFoundException()
    {
        $this->setExpectedException('Exception');
        $route = new Route('GET /test/page.html', 'A->test');
        $route->dispatch();
    }

    /**
     * @test
     */
    public function shouldCallSpecifiedCallback()
    {
//        $class = $this->getMockBuilder('Test')->setMethods(['login'])->getMock();
//
//        $class->expects($this->any())
//            ->method('login');
//
//        $route = new Route('GET /test/page.html', 'Test->login');
//
//        ob_start();
//        $route->dispatch(['someData']);
//        $result = ob_get_clean();
//
//        $this->assertEquals('logged!', $result);
    }
}