<?php


namespace App\Callback\Event\Message;


class MessageAction
{
    /**
     * Название действия приглашения в беседу.
     */
    public const ACTION_INVITE = 'chat_invite_user';

    /**
     * Название действия выхода из беседы.
     */
    public const ACTION_KICK = 'chat_kick_user';

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $userId;

    /**
     * MessageAction constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->userId = $data['member_id'];
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function userId(): int
    {
        return $this->userId;
    }
}