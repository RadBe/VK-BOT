<?php


namespace App\Database\Repository;


use App\Database\Entity\User;
use App\Database\MySQL;

class UsersRepository
{
    /**
     * @var MySQL
     */
    private $db;

    /**
     * UsersRepository constructor.
     * @param MySQL $db
     */
    public function __construct(MySQL $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @return User|null
     */
    public function findUser(int $groupId, int $userId): ?User
    {
        $stmt = $this->db->stmt()->prepare('SELECT * FROM `users` WHERE `user_id` = ? AND `group_id` = ? LIMIT 1');
        $stmt->execute([$userId, $groupId]);
        $row = $stmt->fetch();

        return !empty($row) && is_array($row) ? User::createFromData($row) : null;
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @param int $rank
     * @return User
     */
    public function findOrCreateUser(int $groupId, int $userId, int $rank): User
    {
        $user = $this->findUser($groupId, $userId);
        if (is_null($user)) {
            $user = new User($userId, $groupId, $rank);
            $this->createNewUser($user);
        }

        return $user;
    }

    /**
     * @param User $user
     */
    public function createNewUser(User $user): void
    {
        $stmt = $this->db->stmt()->prepare('INSERT INTO `users` (`user_id`, `group_id`, `rank`) VALUES (?, ?, ?)');
        $stmt->execute([$user->userId(), $user->groupId(), $user->rank()]);
    }

    /**
     * @param User $user
     */
    public function updateUser(User $user): void
    {
        $stmt = $this->db->stmt()->prepare(
            'UPDATE `users` SET `rank` = ?, `mute_time` = ? WHERE `user_id` = ? AND `group_id` = ? LIMIT 1'
        );
        $stmt->execute([$user->rank(), $user->muteTime(), $user->userId(), $user->groupId()]);
    }

    /**
     * @param User $user
     */
    public function deleteUser(User $user): void
    {
        $stmt = $this->db->stmt()->prepare('DELETE FROM `users` WHERE `user_id` = ? AND `group_id` = ? LIMIT 1');
        $stmt->execute([$user->userId(), $user->groupId()]);
    }

    /**
     * @return User[]
     */
    public function mutedUsers(): array
    {
        $stmt = $this->db->stmt()->prepare('SELECT * FROM `users` WHERE `mute_time` > ? ORDER BY `mute_time` DESC');
        $stmt->execute([time()]);
        $rows = $stmt->fetchAll();

        $users = [];
        foreach ($rows as $row)
        {
            $users[] = User::createFromData($row);
        }

        return $users;
    }
}