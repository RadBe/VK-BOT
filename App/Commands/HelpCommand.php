<?php


namespace App\Commands;


use App\Database\Entity\User;

class HelpCommand extends Command
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
        $commands = '';
        foreach ($this->config->commands() as $pattern => $data)
        {
            $commands .= "$pattern - {$data['description']}\n";
        }

        $this->sendMessage("Доступные команды:\n\n$commands");
    }
}