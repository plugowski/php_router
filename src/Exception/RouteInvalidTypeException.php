<?php
namespace PhpRouter\Exception;

use PhpRouter\RouteException;

/**
 * Class RouteInvalidTypeException
 * @package PhpRouter\Exception
 */
class RouteInvalidTypeException extends RouteException
{
    /**
     * @var int
     */
    protected $code = RouteException::WRONG_TYPE;
}