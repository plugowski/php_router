<?php
namespace PhpRouter\Test;

use PhpRouter\Route;
use PHPUnit_Framework_TestCase;

require 'Test.php';

/**
 * Class RouterTest
 */
class RouterTest extends PHPUnit_Framework_TestCase
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
        $route->dispatch(['someData']);
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
        $route->dispatch(['someData']);
    }

    /**
     * @test
     */
    public function shouldCallSpecifiedCallback()
    {
        $class = $this->getMockBuilder('Test')->setMethods([])->getMock();

        $class->expects($this->any())
            ->method('login');

        $route = new Route('GET /test/page.html', 'Test->login');

        ob_start();
        $route->dispatch(['someData']);
        $result = ob_get_clean();

        $this->assertEquals('logged!', $result);
    }
}