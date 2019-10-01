<?php


namespace App\Callback\Method;


interface Method
{
    /**
     * Возвращает название метода.
     * @return string
     */
    public function methodName(): string;

    /**
     * Возвращает отправляемый массив данных.
     * @return array
     */
    public function params(): array;
}