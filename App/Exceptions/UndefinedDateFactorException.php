<?php


namespace App\Exceptions;


use Exception;

class UndefinedDateFactorException extends Exception
{
    /**
     * UndefinedDateFactorException constructor.
     * @param string|null $word
     */
    public function __construct(?string $word)
    {
        parent::__construct("Неизвестный тип даты: $word");
    }
}