<?php


namespace App\Database\Entity;


class User
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var int
     */
    private $rank;

    /**
     * @var int
     */
    private $mute = 0;

    /**
     * User constructor.
     * @param int $userId
     * @param int $groupId
     * @param int $rank
     */
    public function __construct(int $userId, int $groupId, int $rank)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
        $this->rank = $rank;
    }

    /**
     * @return int
     */
    public function userId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return int
     */
    public function rank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     */
    public function setRank(int $rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return bool
     */
    public function isMuted(): bool
    {
        return $this->mute > 0 && $this->mute > time();
    }

    /**
     * @param int $time
     */
    public function setMute(int $time): void
    {
        $this->mute = $time;
    }

    /**
     * @return int
     */
    public function muteTime(): int
    {
        if (!$this->isMuted()) {
            $this->mute = 0;
        }

        return $this->mute;
    }

    /**
     * @param array $data
     * @return User
     */
    public static function createFromData(array $data): User
    {
        $user = new self($data['user_id'], $data['group_id'], $data['rank']);
        $user->setMute((int) $data['mute_time']);

        return $user;
    }
}