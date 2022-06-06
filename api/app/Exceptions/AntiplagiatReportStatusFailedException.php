<?php


namespace App\Exceptions;


use \Exception;

class AntiplagiatReportStatusFailedException extends Exception
{
    public function __construct($message, $code = 100, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}