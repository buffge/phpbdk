<?php

/*
 * Author: buff <admin@buffge.com>
 * Created on : 2018-6-11, 10:49:28
 * QQ:1515888956
 */
namespace bdk\exception;

use Exception;
use Throwable;

/**
 * 未找到通用异常
 */
class NotFoundException extends Exception
{
    protected $extra;
    public function __construct(string $message = "", int $code = 0, array $extra = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->extra = $extra;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }
}
