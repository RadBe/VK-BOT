<?php


namespace App\Exceptions;


use Exception;

class ConfigNotFoundException extends Exception
{
    /**
     * ConfigNotFoundException constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct("Конфиг не найден: $file");
    }
}