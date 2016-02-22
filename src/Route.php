<?php
namespace PhpRouter;

use PhpRouter\Exception\RouteCallbackNotFoundException;
use PhpRouter\Exception\RouteInvalidDefinitionException;
use PhpRouter\Exception\RouteInvalidTypeException;
use PhpRouter\Exception\RouteWrongCallbackException;
use ReflectionClass;

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
            (?<url>([@\/\w]+))
            (?:\.(?<extension>\w+))?
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
     * @var array
     */
    private $callbackArgs = [];
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $extension;
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
     * @param array $callbackArgs
     * @throws RouteInvalidDefinitionException
     * @throws RouteInvalidTypeException
     */
    public function __construct($route, $rules, $callback = null, $callbackArgs = [])
    {
        if (is_callable($rules) || is_string($rules)) {
            $this->callback = $rules;
            $this->callbackArgs = (array)$callback;
        } else {
            $this->paramRules = $rules;
            $this->callback = $callback;
            $this->callbackArgs = (array)$callbackArgs;
        }

        $this->parseRoute($route);
    }

    /**
     * Parse route and save results in object properties
     *
     * @param string $route
     * @throws RouteInvalidDefinitionException
     * @throws RouteInvalidTypeException
     */
    private function parseRoute($route)
    {
        if (!preg_match($this->patterns['route'], $route, $result)) {
            throw new RouteInvalidDefinitionException();
        }

        $this->methods = explode('|', $result['method']);
        $this->url = $result['url'];

        if (!empty($result['extension'])) {
            $this->extension = $result['extension'];
            $this->url .= '.' . $this->extension;
        }

        if (!empty($result['type'])) {
            if (!in_array($result['type'], $this->types)) {
                throw new RouteInvalidTypeException();
            }

            $this->type = $result['type'];
        }

        $this->pattern = $this->preparePattern();
    }

    /**
     * Build regex, required for Router to check if current path mach
     *
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

    /**
     * Parse named params and build associative array with them
     *
     * @param $requestUrl
     */
    public function parseParams($requestUrl)
    {
        preg_match($this->getPattern(), $requestUrl, $values);
        preg_match_all($this->patterns['param'], $this->getUrl(), $names);

        unset($values[0]);
        $values = array_values($values);

        if (count($values) == count($names['name'])) {
            foreach ($names['name'] as $key => $name) {
                $this->namedParams[$name] = $values[$key];
            }
        }
    }

    /**
     * Execute specified Route - anonymous function or pointed class->method
     *
     * @return mixed
     * @throws RouteWrongCallbackException
     */
    public function dispatch()
    {
        if (is_callable($this->callback)) {
            return call_user_func_array($this->callback, [$this->namedParams]);
        } else if (preg_match($this->patterns['callback'], $this->callback, $result)) {
            return $this->call($result['class'], $result['method'], $result['type'], [$this->namedParams]);
        }
        throw new RouteWrongCallbackException();
    }

    /**
     * Call class and method defined as callback
     *
     * @param string $class
     * @param string $method
     * @param string $type
     * @param array $params
     * @return mixed
     * @throws RouteCallbackNotFoundException
     */
    private function call($class, $method, $type, array $params = [])
    {
        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new RouteCallbackNotFoundException();
        }

        if ('->' == $type) {
            $reflection = new ReflectionClass($class);
            $class = !empty($this->callbackArgs) ? $reflection->newInstanceArgs($this->callbackArgs) : $reflection->newInstance();
        }

        return call_user_func_array([$class, $method], $params);
    }
}