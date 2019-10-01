<?php


namespace App\Commands;


use App\Callback\Event\Message\MessageNewEvent;
use App\Config;
use App\Database\Entity\User;
use App\Database\MySQL;
use App\Database\Repository\UsersRepository;
use App\Exceptions\CommandException;
use App\Exceptions\UndefinedDateFactorException;
use App\Helpers;

class MuteCommand extends Command
{
    /**
     * Минимальный ранг для выдачи мута.
     */
    private const MIN_RANK_MUTE = 7;

    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * MuteCommand constructor.
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
        if ($commandSender->rank() < self::MIN_RANK_MUTE) {
            $this->allowedForRank(self::MIN_RANK_MUTE);
            return;
        }

        if (count($args) < 2) {
            $this->sendMessage("Пример команды: $cmd пользователь 1 час");
            return;
        }

        try {
            $targetData = $this->getTargetData($args);
            if ($targetData['is_admin']) {
                throw new CommandException('Вы не можете выдать мут администратору беседы');
            }

            $target = $this->usersRepository->findOrCreateUser(
                $this->event->groupId(),
                (int) $targetData['id'],
                $this->config->defaultRank()
            );
            if ($commandSender->rank() <= $target->rank()) {
                throw new CommandException('Ваш ранг не позваляет выдать мут этому пользователю');
            }

            $time = count($args) == 2 ? $this->getTime($args[0], $args[1]) : $this->getTime($args[1], $args[2]);

            $target->setMute($time);
            $this->usersRepository->updateUser($target);

            $this->sendMessage(sprintf(
                'Пользователю %s был выдан мут до %s. Если он напишет сообщение, то он будет кикнут',
                Helpers::anchorUser((int) $targetData['id'], $targetData),
                date('d.m.Y H:i', $time)
            ));
        } catch (CommandException | UndefinedDateFactorException $exception) {
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
        if (count($args) == 2) {
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

    /**
     * @param int $amount
     * @param string $format
     * @return int
     * @throws UndefinedDateFactorException
     */
    private function getTime($amount, string $format): int
    {
        if (!is_numeric($amount) || $amount < 1) {
            throw new CommandException('Число должно быть больше 0');
        }

        return time() + ($amount * Helpers::dateFactor($format));
    }
}