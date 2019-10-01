<?php


namespace App\Callback\Method;


use App\Bot;

class Kick implements Method
{
    /**
     * @var int
     */
    private $peerId;

    /**
     * @var int
     */
    private $userId;

    /**
     * Kick constructor.
     * @param int $peerId
     * @param int $userId
     */
    public function __construct(int $peerId, int $userId)
    {
        $this->peerId = $peerId;
        $this->userId = $userId;
    }

    /**
     * Возвращает название метода.
     * @return string
     */
    public function methodName(): string
    {
        return 'messages.removeChatUser';
    }

    /**
     * Возвращает отправляемый массив данных.
     * @return array
     */
    public function params(): array
    {
        return [
            'chat_id' => $this->peerId - Bot::BASE_PEER_ID,
            'member_id' => $this->userId
        ];
    }
}