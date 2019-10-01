<?php


namespace App\Commands;


use App\Callback\Event\Message\MessageNewEvent;
use App\Config;
use App\Database\Entity\User;
use App\Database\MySQL;
use App\Database\Repository\UsersRepository;
use App\Exceptions\CommandException;
use App\Helpers;

class UnmuteCommand extends Command
{
    /**
     * Минимальный ранг для размута
     */
    private const MIN_RANK_UNMUTE = 7;

    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * UnmuteCommand constructor.
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
        if ($commandSender->rank() < self::MIN_RANK_UNMUTE) {
            $this->allowedForRank(self::MIN_RANK_UNMUTE);
            return;
        }

        try {
            $targetData = $this->getTargetData($args);

            $target = $this->usersRepository->findUser($this->event->groupId(), (int) $targetData['id']);
            if (is_null($target) || !$target->isMuted()) {
                throw new CommandException('У этого пользователя нет мута');
            }

            $target->setMute(0);
            $this->usersRepository->updateUser($target);

            $this->sendMessage(sprintf(
                'Пользователю %s был снят мут',
                Helpers::anchorUser((int) $targetData['id'], $targetData)
            ));
        } catch (CommandException $exception) {
            $this->sendMessage($exception->getMessage());
        }
    }

    /**
     * @param array $args
     * @return array
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function getTargetData(array $args): array
    {
        if (count($args) == 0) {
            if (is_null($this->event->replyId())) {
                throw new CommandException('Вы не выбрали пользователя');
            }

            $userData = $this->findUserById($this->event->replyId());
        } else {
            $userData = is_numeric($args[0]) ? $this->findUserById((int) $args[0]) : $this->findUserByLastName($args[0]);
        }

        if (is_null($userData)) {
            throw new CommandException('Пользователь не найден в беседе');
        }

        return $userData;
    }
}