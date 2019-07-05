<?php
/**
 * Created by IntelliJ IDEA.
 * User: buff
 * Date: 2019/3/12
 * Time: 14:13
 */

namespace bdk\exception;

use Exception;
use Throwable;

class SendSmsException extends Exception
{
    public function __construct(string $message = "", int $code = 0, array $extra = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->extra = $extra;
    }

}