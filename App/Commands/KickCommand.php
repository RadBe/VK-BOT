<?php


namespace App\Commands;


use App\Callback\Event\Message\MessageNewEvent;
use App\Callback\Method\Kick;
use App\Config;
use App\Database\Entity\User;
use App\Database\MySQL;
use App\Database\Repository\UsersRepository;
use App\Exceptions\CommandException;
use App\Helpers;

class KickCommand extends Command
{
    /**
     * Минимальный ранг для кика.
     */
    private const MIN_RANK_KICK = 7;

    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * KickCommand constructor.
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
        if ($commandSender->rank() < self::MIN_RANK_KICK) {
            $this->allowedForRank(self::MIN_RANK_KICK);
            return;
        }

        try {
            $userData = $this->getTarget($commandSender, $args);
            $this->sender->send(new Kick(
                $this->event->peer(),
                $userData['id']
            ));
            $this->sendMessage(
                'Пользователь ' . Helpers::anchorUser($userData['id'], $userData)
                . ' был кикнут из беседы. Данные о нем были удалены из базы'
            );
        } catch (CommandException $exception) {
            $this->sendMessage($exception->getMessage());
        }
    }

    /**
     * @param User $sender
     * @param array $args
     * @return array
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function getTarget(User $sender, array $args): array
    {
        if (count($args) == 0) {
            if (is_null($this->event->replyId())) {
                throw new CommandException('Вы не выбрали пользователя');
            }

            $userId = $this->event->replyId();
            $userData = $this->findUserById($userId);
            if (is_null($userData)) {
                throw new CommandException('Пользователь не найден в беседе');
            }
        } else {
            $userData = is_numeric($args[0]) ? $this->findUserById((int) $args[0]) : $this->findUserByLastName($args[0]);
            if (is_null($userData)) {
                throw new CommandException('Пользователь не найден в беседе');
            }

            $userId = $userData['id'];
        }

        if ($userData['is_admin']) {
            throw new CommandException('Вы не можете кикнуть администратора беседы');
        }

        $user = $this->usersRepository->findUser($this->event->groupId(), $userId);
        if (!is_null($user)) {
            if ($user->rank() >= $sender->rank()) {
                throw new CommandException('Ваш ранг не позволяет кикнуть этого пользователя');
            }
            $this->usersRepository->deleteUser($user);
        }

        return $userData;
    }
}