<?php


namespace App\Exceptions;


use Exception;

class UnsupportedEventException extends Exception
{
    /**
     * UnsupportedEventException constructor.
     * @param string|null $eventName
     */
    public function __construct(?string $eventName)
    {
        parent::__construct("Неизвестное событие: $eventName");
    }
}