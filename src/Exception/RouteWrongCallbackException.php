<?php
namespace PhpRouter\Exception;

use PhpRouter\RouteException;

/**
 * Class RouteWrongCallbackException
 * @package PhpRouter\Exception
 */
class RouteWrongCallbackException extends RouteException
{
    /**
     * @var int
     */
    protected $code = RouteException::WRONG_CALLBACK_FORMAT;
}