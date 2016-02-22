<?php
namespace PhpRouter\Test;

use PhpRouter\Route;
use PHPUnit_Framework_TestCase;

require 'Test.php';

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

        $this->assertEquals('|^/test/page.html$|', $route->getPattern());
        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('sync', $route->getType());
        $this->assertEquals('/test/page.html', $route->getUrl());
    }

    /**
     * @test
     */
    public function shouldCreateRouteWithNamedParam()
    {
        $route = new Route('GET|POST /some_page/@id', 'A->index');
        $this->assertEquals('|^/some_page/([\w-]+)$|', $route->getPattern());
    }

    /**
     * @test
     */
    public function shouldCreateRouteWithNamedParamMatchToRegex()
    {
        $route = new Route('GET|POST /some_page/@id', ['id' => '[a-z]{2}\-[a-z]{4}'], 'A->index');
        $this->assertEquals('|^/some_page/([a-z]{2}\-[a-z]{4})$|', $route->getPattern());
    }

    /**
     * @test
     */
    public function shouldCallAndExecuteAnonymousFunction()
    {
        $route = new Route('GET /test/@data.html [ajax]', ['data' => '[a-z]{2}:[a-z]{3}'], function($params){
            return $params['data'];
        });

        $route->parseParams('/test/xx:www.html');
        $this->assertEquals('xx:www', $route->dispatch());
    }

    /**
     * @test
     */
    public function shouldCallSpecifiedCallback()
    {
        $route = new Route('GET /test/page/@number.html', '\PhpRouter\Test\Test->page');

        $route->parseParams('/test/page/42.html');
        $this->assertEquals(42, $route->dispatch());
    }

    /**
     * @test
     */
    public function shouldThrowWrongCallbackException()
    {
        $this->setExpectedException('PhpRouter\Exception\RouteWrongCallbackException');
        (new Route('GET /test.html', 'nothingSpecial'))->dispatch();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidRouteException()
    {
        $this->setExpectedException('PhpRouter\Exception\RouteInvalidDefinitionException');
        new Route('/test.html', 'A->index');
    }

    /**
     * @test
     */
    public function shouldThrowInvalidTypeException()
    {
        $this->setExpectedException('PhpRouter\Exception\RouteInvalidTypeException');
        new Route('GET /test.html [not_exists]', 'A->index');
    }

    /**
     * @test
     */
    public function shouldThrowClassOrMethodNotFoundException()
    {
        $this->setExpectedException('PhpRouter\Exception\RouteCallbackNotFoundException');
        (new Route('GET /test/page.html', 'A->test'))->dispatch();
    }
}