<?php


namespace App\Exceptions;


use Exception;

class InvalidResponseException extends Exception
{
    /**
     * InvalidResponseException constructor.
     * @param string $method
     * @param null $response
     */
    public function __construct(string $method, $response = null)
    {
        if (is_array($response)) {
            $response = var_export($response, true);
        }
        parent::__construct("Метод: $method вернул неправильный ответ.\n\n$response");
    }
}