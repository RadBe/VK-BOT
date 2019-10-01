<?php


namespace App\Callback\Response;


class Users
{
    /**
     * @var array
     */
    private $users;

    /**
     * Users constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->users = $response;
    }

    /**
     * @return array
     */
    public function users(): array
    {
        return $this->users;
    }
}