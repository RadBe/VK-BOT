<?php


namespace App\Commands;


use App\Database\Entity\User;
use App\Request;

class TestCommand extends Command
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
        $this->sendMessage(var_export(Request::post(), true));
    }
}