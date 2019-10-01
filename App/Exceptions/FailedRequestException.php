<?php


namespace App\Exceptions;


use Exception;

class FailedRequestException extends Exception
{
    /**
     * FailedRequestException constructor.
     * @param string $method
     */
    public function __construct(string $method)
    {
        parent::__construct("Ошибка при выполнении метода: $method");
    }
}