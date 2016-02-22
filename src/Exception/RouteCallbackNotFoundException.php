<?php
namespace PhpRouter\Exception;

use PhpRouter\RouteException;

/**
 * Class RouteCallbackNotFoundException
 * @package PhpRouter\Exception
 */
class RouteCallbackNotFoundException extends RouteException
{
    /**
     * @var int
     */
    protected $code = RouteException::CALLBACK_NOT_FOUND;
}