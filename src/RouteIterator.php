<?php
namespace PhpRouter;

use Countable;
use Iterator;

/**
 * Class RouteIterator
 * @package PhpRouter
 */
abstract class RouteIterator implements Iterator, Countable
{
    /**
     * @var Route[]
     */
    public $routes = [];

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @return Route
     */
    public function current()
    {
        return $this->routes[$this->index];
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->routes[$this->index]);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }
}