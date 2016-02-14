<?php
namespace PhpRouter;

use Countable;
use RecursiveIterator;

/**
 * Class RouteIterator
 * @package PhpRouter
 */
abstract class RouteIterator implements RecursiveIterator, Countable
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
     * @return bool
     */
    public function hasChildren()
    {
        if ($this->valid() && ($this->current() instanceof RecursiveIterator)) {
            return true;
        }
        return false;
    }

    /**
     * @return Route
     */
    public function getChildren()
    {
        return $this->routes[$this->index];
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }
}