<?php
namespace PhpRouter;

/**
 * Class RouteCollection
 */
class RouteCollection extends RouteIterator
{
    /**
     * Attach new Route to the collection
     *
     * @param Route $route
     */
    public function attach(Route $route)
    {
        $this->routes[] = $route;
    }
}