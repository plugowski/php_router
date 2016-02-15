<?php
namespace PhpRouter;

/**
 * Class Router
 * @package PhpRouter
 */
class Router
{
    /**
     * @var RouteCollection
     */
    private $routeCollection;
    /**
     * @var RouteRequest
     */
    private $routeRequest;

    /**
     * Router constructor.
     * @param RouteRequest $routeRequest
     * @param RouteCollection $routeCollection
     */
    public function __construct(RouteRequest $routeRequest, RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
        $this->routeRequest = $routeRequest;
    }

    /**
     */
    public function run()
    {
        foreach ($this->routeCollection as $route) {

            if (!$this->match($route)) {
                continue;
            }

            return $route->dispatch();
        }
    }

    /**
     * @param Route $route
     * @return bool
     */
    private function match(Route $route)
    {
        if (!in_array($this->routeRequest->getRequestMethod(), $route->getMethods())) {
            return false;
        }
        if ($this->routeRequest->isAjax() && 'ajax' != $route->getType()) {
            return false;
        }
        if ($this->routeRequest->getRequestUrl() !== $route->getUrl()) {
            return false;
        }

        return true;
    }
}