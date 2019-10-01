<?php


namespace App\Commands;


use App\Database\Entity\User;
use Exception;

class OnlineCommand extends Command
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
        try {
            $response = $this->conversationMembers();

            $list = '';
            $i = 0;
            foreach ($response->members() as $memberId => $member)
            {
                if ($member['online']) {
                    $list .= ($i + 1) . ") {$member['display_name']}\n";
                    ++$i;
                }
            }

            $this->sendMessage("Онлайн беседы ($i из {$response->count()}):\n$list", true);
        } catch (Exception $e) {
            $msg = $response ?? $e->getMessage();
            $this->sendMessage(var_export($msg, true));
        }
    }
}