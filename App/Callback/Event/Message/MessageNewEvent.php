<?php


namespace App\Callback\Event\Message;


use App\Callback\Event\Event;
use App\Request;

class MessageNewEvent implements Event
{
    /**
     * @var int
     */
    private $groupId;

    /**
     * @var array
     */
    private $data;

    /**
     * MessageNewEvent constructor.
     */
    public function __construct()
    {
        $this->groupId = Request::post('group_id', 0);
        $this->data = Request::post('object', []);
    }

    /**
     * @return int
     */
    public function author(): int
    {
        return (int) $this->data['from_id'] ?? 0;
    }

    /**
     * @return int
     */
    public function peer(): int
    {
        return (int) $this->data['peer_id'] ?? $this->data['user_id'] ?? 0;
    }

    /**
     * @return string|null
     */
    public function message(): ?string
    {
        return trim($this->data['text'] ?? '');
    }

    /**
     * @return int|null
     */
    public function replyId(): ?int
    {
        $reply = $this->data['reply_message'] ?? null;
        if (!is_null($reply)) {
            return (int) $reply['from_id'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function replyText(): ?string
    {
        $reply = $this->data['reply_message'] ?? null;
        if (!is_null($reply)) {
            return $reply['text'];
        }

        return null;
    }

    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return MessageAction|null
     */
    public function action(): ?MessageAction
    {
        if (isset($this->data['action']) && is_array($this->data['action'])) {
            return new MessageAction($this->data['action']);
        }

        return null;
    }
}