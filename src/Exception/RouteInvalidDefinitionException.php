<?php
namespace PhpRouter\Exception;

use PhpRouter\RouteException;

/**
 * Class RouteInvalidDefinitionException
 * @package PhpRouter\Exception
 */
class RouteInvalidDefinitionException extends RouteException
{
    /**
     * @var int
     */
    protected $code = RouteException::INVALID_DEFINITION;
}