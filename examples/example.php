<?php
require __DIR__ . '../vendor/autoload.php';

use PhpRouter\Route;
use PhpRouter\RouteCollection;
use PhpRouter\Router;
use PhpRouter\RouteRequest;

class A
{
    function index()
    {
        echo "I am the index!";
    }

    static function showParams($params)
    {
        print_r($params);
    }
}

$routing = new RouteCollection();

$routing->attach(new Route('GET /', function(){
    echo 'Hello World';
}));

$routing->attach(new Route('GET /page', function(){
    echo 'Some page...';
}));

$routing->attach(new Route('GET /page/@id', ['id' => '\d+'], function($params){
    echo 'Page no. ' . $params['id'];
}));

$routing->attach(new Route('GET /mac/@mac', ['mac' => '..:..:..:..:..:..'], function($params){
    echo 'Mac Address: ' . $params['mac'];
}));

$routing->attach(new Route('GET /index', 'A->index'));

$routing->attach(new Route('GET /index/@data', 'A->showParams'));

(new Router(new RouteRequest(), $routing))->run();