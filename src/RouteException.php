<?php
namespace PhpRouter;

use Exception;

/**
 * Class RouteException
 * @package PhpRouter
 */
class RouteException extends Exception
{
    const ROUTE_NOT_FOUND = 404;
    const INVALID_DEFINITION = 1000;
    const WRONG_TYPE = 1001;
    const WRONG_CALLBACK_FORMAT = 1002;
    const CALLBACK_NOT_FOUND = 1003;

    /**
     * @var array List of errors and base error descriptions
     */
    private static $errors = [
        self::ROUTE_NOT_FOUND => 'Route not found',
        self::INVALID_DEFINITION => 'Route definition is not correct',
        self::WRONG_TYPE => 'Defined request type is not correct',
        self::WRONG_CALLBACK_FORMAT => 'Declared callback has wrong format',
        self::CALLBACK_NOT_FOUND => 'Defined callback does not exists',
    ];

    /**
     * RouteException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($this->getMessageByCode(), $this->getCode(), $previous);
    }

    /**
     * @return string
     */
    private function getMessageByCode()
    {
        return !empty(self::$errors[$this->getCode()]) ? self::$errors[$this->getCode()] : '';
    }
}