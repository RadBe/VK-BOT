<?php


namespace App\Commands;


use App\Database\Entity\User;

class DateTimeCommand extends Command
{
    /**
     * @param User $commandSender
     * @param string $cmd
     * @param array $args
     * @throws \App\Exceptions\FailedRequestException
     * @throws \App\Exceptions\InvalidResponseException
     */
    public function execute(User $commandSender, string $cmd, array $args): void
    {
        if (count($args) > 0) {
            $date = date($args[0]);
        } else {
            $date = date('d.m.Y H:i');
        }

        $this->sendMessage('Текущая дата: ' . $date);
    }
}