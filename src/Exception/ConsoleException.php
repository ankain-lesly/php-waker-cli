<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */

namespace Devlee\WakerCLI\Exception;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 */

class ConsoleException extends BaseException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
