<?php
namespace PhpRouter;

/**
 * Class RouteRequest
 * @package PhpRouter
 */
class RouteRequest
{
    private $protocol;
    private $requestUrl;
    private $queryParams = [];
    private $requestMethod;
    private $isAjax = false;
    private $headers;

    /**
     * RouteRequest constructor.
     */
    public function __construct()
    {
        $this->setHeaders();
        $this->setRequestUrl();
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->isAjax;
    }

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return mixed
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Get and parse request headers
     *
     * @return void
     */
    private function setHeaders()
    {
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' != substr($key, 0, 5)) {
                continue;
            }
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            $this->headers[$name] = $value;
        }

        $this->protocol = $this->getRequestProtocol();
        $this->isAjax = ('XMLHttpRequest' == $this->getHeader('X-Requested-With'));

        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        if (!is_null($this->getHeader('X-Http-Method-Override'))) {
            $this->requestMethod = $this->getHeader('X-Http-Method-Override');
        } else if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['_method'])) {
            $this->requestMethod = $_POST['_method'];
        }
    }

    /**
     * Set url for current request
     *
     * @return void
     */
    private function setRequestUrl()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $this->requestUrl = $uri['path'];

        if (!empty($uri['query'])) {
            parse_str($uri['query'], $this->queryParams);
        }
    }

    /**
     * Check if request comes from https or http
     *
     * @return string
     */
    private function getRequestProtocol()
    {
        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] || 'https' == $this->getHeader('X-Forwarded-Proto')) {
            $protocol = 'https';
        }
        return $protocol;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }
}