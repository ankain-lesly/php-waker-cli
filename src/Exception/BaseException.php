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

abstract class BaseException extends \Exception
{
  protected ?string $title = null;
  public function __construct(string $message, int $code = 500)
  {
    $this->message = $message;
    $this->code = $code;
    $this->title = '';
  }

  public function getTitle()
  {
    return $this->title;
  }
}
