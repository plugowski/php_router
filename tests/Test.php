<?php
namespace PhpRouter\Test;

/**
 * Class Test
 * @package PhpRouter\Test
 */
class Test
{
    public function page($params)
    {
        return $params['number'];
    }
}