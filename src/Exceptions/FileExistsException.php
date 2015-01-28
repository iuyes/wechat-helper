<?php namespace Huying\WechatHelper\Exceptions;

use Exception;

class FileExistsException extends Exception
{

    public function __construct($path, Exception $previous = null)
    {
        $message = $path.'已存在';
        parent::__construct($message, 0, $previous);
    }
}
