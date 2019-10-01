<?php


namespace App\Commands;


use App\Callback\Event\Message\MessageNewEvent;
use App\Callback\Method\UsersGet;
use App\Callback\Response\Users;
use App\Config;
use App\Database\Entity\User;
use App\Database\MySQL;
use App\Database\Repository\UsersRepository;
use App\Helpers;

class MutesCommand extends Command
{
    /**
     * Минимальный ранг для просмотра пользователей с мутом.
     */
    private const MIN_RANK_SHOW = 7;

    /**
     * @var UsersRepository
     */
    private $usersRepository;

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
        if ($commandSender->rank() < self::MIN_RANK_SHOW) {
            $this->allowedForRank(self::MIN_RANK_SHOW);
            return;
        }

        $mutedUsers = $this->usersRepository->mutedUsers();
        if (empty($mutedUsers)) {
            $this->sendMessage('Пользователей с мутом нет');
            return;
        }

        $mutes = [];
        foreach ($mutedUsers as $user)
        {
            $mutes[$user->userId()] = $user->muteTime();
        }

        $ids = array_keys($mutes);

        /* @var Users $users */
        $users = $this->sender->send(new UsersGet($ids), Users::class);

        $out = '';
        $i = 1;
        foreach ($users->users() as $user)
        {
            $id = (int) $user['id'];
            if (isset($mutes[$id])) {
                $out .= $i . '. ' . Helpers::anchorUser($id, $user) . ' до ' . date('d.m.Y H:i', $mutes[$id]) . "\n";
            }
        }

        if (empty(trim($out))) {
            $this->sendMessage('Пользователей с мутом нет');
            return;
        }

        $this->sendMessage("Пользователи с мутом:\n$out", true);
    }
}