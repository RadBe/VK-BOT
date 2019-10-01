<?php


namespace App\Commands;


use App\Callback\Event\Message\MessageNewEvent;
use App\Config;
use App\Database\Entity\User;
use App\Database\MySQL;
use App\Database\Repository\UsersRepository;
use App\Helpers;

class RankCommand extends Command
{
    /**
     * Минимальный ранг для просмотра рангов других пользователей.
     */
    private const MIN_RANK_SHOW = 3;

    /**
     * Минимальный ранг для изменения рангов других пользователей.
     */
    private const MIN_RANK_SET = 9;

    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * RankCommand constructor.
     * @param Config $config
     * @param MySQL $db
     * @param MessageNewEvent $event
     */
    public function __construct(Config $config, MySQL $db, MessageNewEvent $event)
    {
        parent::__construct($config, $db, $event);

        $this->usersRepository = new UsersRepository($db);
    }

    /**
     * @param User $commandSender
     * @param string $cmd
     * @param array $args
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    public function execute(User $commandSender, string $cmd, array $args): void
    {
        $id = $this->event->replyId();
        if (is_null($id)) {
            if (count($args) < 2) {
                $this->showRank($commandSender, $args[0] ?? null);
            } else {
                $rank = (int) $args[1];
                if ($this->checkNewRank($commandSender, $rank)) {
                    $this->setRank($args[0], $rank);
                }
            }
        } else {
            if (count($args) == 0) {
                $this->showRank($commandSender, $id);
            } else {
                $rank = (int) $args[0];
                if ($this->checkNewRank($commandSender, $rank)) {
                    $this->setRank($id, $rank);
                }
            }
        }
    }

    /**
     * @param User $sender
     * @param string|int|null $name
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function showRank(User $sender, $name): void
    {
        if (empty($name)) {
            $this->sendMessage('Ваш ранг: ' . $sender->rank());
            return;
        }

        if ($sender->rank() < self::MIN_RANK_SHOW) {
            $this->allowedForRank(self::MIN_RANK_SHOW);
            return;
        }

        $search = is_int($name) ? $this->findUserById($name, true) : $this->findUserByLastName($name, true);
        if (!is_null($search)) {
            $user = $this->usersRepository->findOrCreateUser($this->event->groupId(), $search['id'], $this->config->defaultRank());
            $this->sendMessage(
                'Ранг пользователя ' . Helpers::anchorUser($user->userId(), $search) . ': ' . $user->rank(),
                true
            );
        }
    }

    /**
     * @param string|int $name
     * @param int $rank
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function setRank($name, int $rank): void
    {
        $search = is_int($name) ? $this->findUserById($name, true) : $this->findUserByLastName($name, true);
        if (!is_null($search)) {
            $user = $this->usersRepository->findOrCreateUser($this->event->groupId(), $search['id'], $this->config->defaultRank());
            $oldRank = $user->rank();

            if ($oldRank == $rank) {
                $this->sendMessage('У этого пользователя уже установлен такой ранг');
                return;
            } elseif ($search['is_admin']) {
                $this->sendMessage('Нельзя менять ранг администраторам беседы');
                return;
            }

            $user->setRank($rank);
            $this->usersRepository->updateUser($user);
            $this->sendMessage(
                'Ранг пользователя ' . Helpers::anchorUser($user->userId(), $search) . ' был изменен с ' . $oldRank . ' на ' . $rank,
                true
            );
        }
    }

    /**
     * @param User $sender
     * @param int $rank
     * @return bool
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function checkNewRank(User $sender, int $rank): bool
    {
        if ($sender->rank() < self::MIN_RANK_SET) {
            $this->allowedForRank(self::MIN_RANK_SET);
            return false;
        }

        if ($rank < $this->config->minRank() || $rank > $this->config->maxRank()) {
            $this->sendMessage("Ранг должен быть от {$this->config->minRank()} до {$this->config->maxRank()}");
            return false;
        }

        return true;
    }
}