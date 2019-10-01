<?php


namespace App\Callback\Method;


class GetConversationMembers implements Method
{
    /**
     * @var int
     */
    private $peerId;

    /**
     * @var int
     */
    private $groupId;

    /**
     * GetConversationMembers constructor.
     * @param int $peerId
     * @param int $groupId
     */
    public function __construct(int $peerId, int $groupId)
    {
        $this->peerId = $peerId;
        $this->groupId = $groupId;
    }

    /**
     * Возвращает название метода.
     * @return string
     */
    public function methodName(): string
    {
        return 'messages.getConversationMembers';
    }

    /**
     * Возвращает отправляемый массив данных.
     * @return array
     */
    public function params(): array
    {
        return [
            'peer_id' => $this->peerId,
            'group_id' => $this->groupId
        ];
    }
}