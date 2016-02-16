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
     * Run Router and dispatch requested Route
     *
     * @return mixed
     */
    public function run()
    {
        foreach ($this->routeCollection as $route) {

            if (!$this->match($route)) {
                continue;
            }

            // if we found matched route, parse params and run
            $route->parseParams($this->routeRequest->getRequestUrl());
            return $route->dispatch();
        }
        // @todo: 404
    }

    /**
     * Match if current Route match to the request url, method and type
     *
     * @param Route $route
     * @return bool
     */
    private function match(Route $route)
    {
        if (!in_array($this->routeRequest->getRequestMethod(), $route->getMethods())) {
            return false;
        }
        if (!$this->routeRequest->isAjax() && 'ajax' == $route->getType()) {
            return false;
        }
        if (!preg_match($route->getPattern(), $this->routeRequest->getRequestUrl())) {
            return false;
        }
        return true;
    }
}