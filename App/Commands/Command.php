<?php


namespace App\Commands;


use App\Callback\CallbackSender;
use App\Callback\Event\Message\MessageNewEvent;
use App\Callback\Method\GetConversationMembers;
use App\Callback\Method\SendMessage;
use App\Callback\Response\ConversationMembers;
use App\Config;
use App\Database\Entity\User;
use App\Database\MySQL;

abstract class Command
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var MessageNewEvent
     */
    protected $event;

    /**
     * @var CallbackSender
     */
    protected $sender;

    /**
     * @var MySQL
     */
    protected $db;

    /**
     * Command constructor.
     * @param Config $config
     * @param MySQL $db
     * @param MessageNewEvent $event
     */
    public function __construct(Config $config, MySQL $db, MessageNewEvent $event)
    {
        $this->config = $config;
        $this->db = $db;
        $this->event = $event;
        $this->sender = new CallbackSender($config);
    }

    /**
     * @param string $message
     * @param bool $disableMentions
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    protected function sendMessage(string $message, bool $disableMentions = false): void
    {
        $method = new SendMessage(
            $this->event->peer(),
            $message
        );
        $method->disableMentions($disableMentions);
        $this->sender->send($method);
    }

    /**
     * @param int $minRank
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    protected function allowedForRank(int $minRank): void
    {
        $this->sendMessage("Для этой команды нужен минимум $minRank ранг!");
    }

    /**
     * @return ConversationMembers
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    protected function conversationMembers(): ConversationMembers
    {
        return $this->sender->send(
            new GetConversationMembers(
                $this->event->peer(),
                $this->event->groupId()
            ),
            ConversationMembers::class
        );
    }

    /**
     * @param string $name
     * @param bool $sendError
     * @return array|null
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    protected function findUserByLastName(string $name, bool $sendError = false): ?array
    {
        $name = mb_strtolower(trim($name));
        $conversation = $this->conversationMembers();
        foreach ($conversation->members() as $memberId => $member)
        {
            if (mb_strtolower($member['last_name']) == $name) {
                return $member;
            }
        }

        if ($sendError) {
            $this->sendMessage('Пользователь не найден в беседе');
        }

        return null;
    }

    /**
     * @param int $id
     * @param bool $sendError
     * @return array|null
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    protected function findUserById(int $id, bool $sendError = false): ?array
    {
        $conversation = $this->conversationMembers();

        $member = $conversation->members()[$id] ?? null;
        if (is_array($member)) {
            return $member;
        }

        if ($sendError) {
            $this->sendMessage('Пользователь не найден в беседе');
        }

        return null;
    }

    /**
     * @param User $commandSender
     * @param string $cmd
     * @param array $args
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    public abstract function execute(User $commandSender, string $cmd, array $args): void;
}