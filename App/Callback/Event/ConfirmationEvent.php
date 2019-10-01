<?php


namespace App\Callback\Event;


use App\Request;

class ConfirmationEvent implements Event
{
    /**
     * @var int
     */
    private $groupId;

    /**
     * ConfirmationEvent constructor.
     */
    public function __construct()
    {
        $this->groupId = Request::post('group_id', 0);
    }

    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }
}