<?php
namespace PhpRouter;

use Exception;

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
            (?<path>([@\/\w]+))
            (?:\h+\[(?<type>\w+)\])?$/x',
        'callback' => '/^
            (?<class>[^\h]+)\h*
            (?<type>->|::)\h*
            (?<method>[^\h]+)$/x',
        'param' => '/@(?<name>[\w]+)/'
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
    private $pattern;
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
     * @var array
     */
    private $namedParams = [];
    /**
     * @var array
     */
    private $paramRules = [];

    /**
     * Router constructor.
     * @param string $route
     * @param array|callable $rules
     * @param callable|null $callback
     * @throws Exception
     */
    public function __construct($route, $rules, $callback = null)
    {
        if (is_callable($rules) || is_string($rules)) {
            $this->callback = $rules;
        } else {
            $this->paramRules = $rules;
            $this->callback = $callback;
        }

        $this->parseRoute($route);
    }

    /**
     * Parse route and save results in object properties
     *
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
                throw new \Exception(sprintf('Wrong type format! Allowed [%s]', implode(', ', $this->types)));
            }

            $this->type = $result['type'];
        }

        $this->pattern = $this->preparePattern();
    }

    /**
     * @return string
     */
    private function preparePattern()
    {
        $pattern = preg_replace_callback($this->patterns['param'], function($match){
            return isset($this->paramRules[$match['name']]) ? '(' . $this->paramRules[$match['name']] . ')' : '([\w-]+)';
        }, $this->getUrl());

        return sprintf('|^%s$|', $pattern);
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
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }


    public function parseParams($requestUrl)
    {
        preg_match($this->getPattern(), $requestUrl, $values);
        preg_match_all($this->patterns['param'], $this->getUrl(), $names);

        unset($values[0]);
        $values = array_values($values);

        if (count($values) != count($names['name']) + 1) {
            foreach ($names['name'] as $key => $name) {
                $this->namedParams[$name] = $values[$key];
            }
        }
    }

    /**
     * Execute specified Route - anonymous function or pointed class->method
     *
     * @return mixed
     */
    public function dispatch()
    {
        if (is_callable($this->callback)) {
            return call_user_func_array($this->callback, [$this->namedParams]);
        } else if (preg_match($this->patterns['callback'], $this->callback, $result)) {
            return $this->call($result['class'], $result['method'], [$this->namedParams]);
        }
        return false;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function call($class, $method, array $params = [])
    {
        if (class_exists($class) && method_exists($class, $method)) {
            return call_user_func_array([$class, $method], $params);
        } else {
            throw new Exception('No class or method found!');
        }
    }
}