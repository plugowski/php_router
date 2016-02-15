<?php
namespace PhpRouter\Test;

use PhpRouter\Route;
use PhpRouter\RouteCollection;
use PHPUnit_Framework_TestCase;

/**
 * Class RouteCollectionTest
 */
class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldAddRoutesToCollection()
    {
        $collection = new RouteCollection();
        $collection->attach(new Route('GET /login', 'A->login'));
        $collection->attach(new Route('POST /login.html [ajax]', 'A->doLogin'));
        $collection->attach(new Route('POST /login/@id.json [ajax]', 'A->doLogin'));

        $this->assertEquals(0, $collection->key());
        $this->assertEquals(3, count($collection));
        $this->assertEquals(['GET'], $collection->current()->getMethods());

        $collection->next();
        $this->assertEquals(1, $collection->key());
        $this->assertEquals(['POST'], $collection->current()->getMethods());
    }
}