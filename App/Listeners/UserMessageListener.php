<?php


namespace App\Listeners;


use App\Callback\CallbackSender;
use App\Callback\Event\Message\MessageAction;
use App\Callback\Event\Message\MessageNewEvent;
use App\Callback\Method\Kick;
use App\Callback\Method\SendMessage;
use App\Database\Entity\User;
use App\Helpers;
use App\Commands\Command;
use App\Database\Repository\UsersRepository;

class UserMessageListener extends Listener
{
    /**
     * @param MessageNewEvent $event
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    public function handle($event): void
    {
        $action = $event->action();
        if (is_null($action)) {
            $this->handleMessage($event);
        } else {
            $this->handleAction($event, $action);
        }

        print 'ok';
    }

    /**
     * @param MessageNewEvent $event
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function handleMessage(MessageNewEvent $event): void
    {
        $usersRepository = new UsersRepository($this->db);
        $author = $usersRepository->findOrCreateUser($event->groupId(), $event->author(), $this->config->defaultRank());

        if ($author->isMuted()) {
            $this->kickForMute($event);
            return;
        }

        [$clazz, $label, $args] = Helpers::getCommand($this->config, $event->message());
        if (!empty($clazz)) {
            /* @var Command $command */
            $command = new $clazz($this->config, $this->db, $event);
            $command->execute($author, $label, $args);
        }
    }

    /**
     * @param MessageNewEvent $event
     * @param MessageAction $action
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function handleAction(MessageNewEvent $event, MessageAction $action): void
    {
        $sender = new CallbackSender($this->config);
        switch ($action->type())
        {
            case MessageAction::ACTION_INVITE:
                $this->writeNewUser($action->userId(), $event->groupId());
                break;

            case MessageAction::ACTION_KICK:
                $message = new SendMessage(
                    $event->peer(),
                    'Пользователь ' . Helpers::mentionUser($action->userId())
                    . ' покинул беседу. Чтобы полностью исключить его из беседы, напишите: бот кик ' . $action->userId()
                );
                $message->disableMentions();
                $sender->send($message);
                break;
        }
    }

    /**
     * @param int $userId
     * @param int $groupId
     * @return User
     */
    private function writeNewUser(int $userId, int $groupId): User
    {
        $repository = new UsersRepository($this->db);
        $user = $repository->findOrCreateUser($groupId, $userId, $this->config->defaultRank());

        return $user;
    }

    /**
     * @var MessageNewEvent $event
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    private function kickForMute(MessageNewEvent $event): void
    {
        $sender = new CallbackSender($this->config);
        $sender->send(new Kick($event->peer(), $event->author()));
        $sender->send(new SendMessage(
                $event->peer(),
                'Пользователь . ' . Helpers::mentionUser($event->author()) . ' был кикнут (мут)')
        );
    }
}