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
        $collection->attach(new Route('GET /login.html', 'A->login'));
        $collection->attach(new Route('POST /login.html', 'A->doLogin'));

        $this->assertEquals(2, count($collection));
        $this->assertEquals(['GET'], $collection->current()->getMethods());
        $collection->next();
        $this->assertEquals(['POST'], $collection->getChildren()->getMethods());
    }
}