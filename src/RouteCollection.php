<?php
namespace PhpRouter;

/**
 * Class RouteCollection
 */
class RouteCollection extends RouteIterator
{
    /**
     * @param Route $route
     */
    public function attach(Route $route)
    {
        $this->routes[] = $route;
    }
}