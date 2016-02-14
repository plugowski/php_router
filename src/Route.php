<?php
namespace PhpRouter;

/**
 * Class Router
 */
class Route
{
    /**
     * @var string
     */
    private $patterns = [
        'route' => '/^
            (?<method>[\|\w]+)\h+
            (?:(?<path>@(\w+)|[^\h]+))
            (?:\h+\[(?<type>\w+)\])?/x',
        'callback' => '/^
            (?<class>[^\h]+)\h*
            (?<type>->|::)\h*
            (?<method>[^\h]+)$/x'
    ];
    /**
     * @var callable|string
     */
    private $callback;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $type = 'sync';
    /**
     * @var array
     */
    private $types = ['sync', 'ajax'];
    /**
     * @var array
     */
    private $methods = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * Router constructor.
     * @param string $route
     * @param callable $callback
     */
    public function __construct($route, $callback)
    {
        $this->parseRoute($route);
        $this->callback = $callback;
    }

    /**
     * @param string $route
     * @throws \Exception
     * @return void
     */
    private function parseRoute($route)
    {
        if (!preg_match($this->patterns['route'], $route, $result)) {
            throw new \Exception('Wrong route format!');
        }

        $this->methods = explode('|', $result['method']);
        $this->url = $result['path'];

        if (!empty($result['type'])) {
            if (!in_array($result['type'], $this->types)) {
                throw new \Exception(sprintf('Wrong type format! Allowed [%s]'), implode(', ', $this->types));
            }

            $this->type = $result['type'];
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Execute specified Route
     *
     * @param array $params
     */
    public function dispatch(array $params = [])
    {
        if (is_callable($this->callback)) {
            call_user_func_array($this->callback, $params);
        } else if (preg_match($this->patterns['callback'], $this->callback, $result)) {
            $this->call($result['class'], $result['method'], $params);
        }
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $params
     */
    private function call($class, $method, array $params = [])
    {
        if (class_exists($class) && method_exists($class, $method)) {
            call_user_func_array([$class, $method], $params);
        }
    }
}