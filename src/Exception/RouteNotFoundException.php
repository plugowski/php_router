<?php
namespace PhpRouter\Exception;

use PhpRouter\RouteException;

/**
 * Class RouteNotFoundException
 * @package PhpRouter\Exception
 */
class RouteNotFoundException extends RouteException
{
    /**
     * @var int
     */
    protected $code = RouteException::ROUTE_NOT_FOUND;
}