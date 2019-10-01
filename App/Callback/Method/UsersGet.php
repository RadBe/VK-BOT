<?php


namespace App\Callback\Method;


class UsersGet implements Method
{
    /**
     * @var array
     */
    private $userIds = [];

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $nameCase = 'Nom';

    /**
     * UsersGet constructor.
     * @param int[] $userIds
     */
    public function __construct(array $userIds)
    {
        $this->userIds = $userIds;
    }

    /**
     * Возвращает название метода.
     * @return string
     */
    public function methodName(): string
    {
        return 'users.get';
    }

    /**
     * Возвращает отправляемый массив данных.
     * @return array
     */
    public function params(): array
    {
        return [
            'user_ids' => implode(',', $this->userIds),
            'fields' => implode(',', $this->fields),
            'name_case' => $this->nameCase
        ];
    }
}