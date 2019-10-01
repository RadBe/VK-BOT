<?php


namespace App\Callback\Response;


class ConversationMembers
{
    /**
     * @var array
     */
    private $response;

    /**
     * @var int
     */
    private $count;

    /**
     * @var array
     */
    private $members = [];

    /**
     * ConversationMembers constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;

        $this->count = ((int) $this->response['count'] ?? 1) - 1;
        $this->members = $this->parseMembers();
    }

    /**
     * @return array
     */
    private function parseMembers(): array
    {
        $members = [];
        foreach (($this->response['items'] ?? []) as $item)
        {
            $item['member_id'] = (int) $item['member_id'];
            if ($item['member_id'] > 0) {
                $members[$item['member_id']] = [
                    'id' => $item['member_id'],
                    'invited_by' => $item['invited_by'],
                    'join_date' => $item['join_date'],
                    'is_admin' => $item['is_admin'],
                    'can_kick' => $item['can_kick'],
                ];
            }
        }
        foreach (($this->response['profiles'] ?? []) as $profile)
        {
            $profile['id'] = (int) $profile['id'];
            if (isset($members[$profile['id']])) {
                $members[$profile['id']]['display_name'] = "[id{$profile['id']}|{$profile['first_name']} {$profile['last_name']}]";
                $members[$profile['id']]['first_name'] = $profile['first_name'];
                $members[$profile['id']]['last_name'] = $profile['last_name'];
                $members[$profile['id']]['online'] = $profile['online'];
                if (isset($profile['online_info']['last_seen'])) {
                    $members[$profile['id']]['last_seen'] = $profile['online_info']['last_seen'];
                }
            }
        }

        return $members;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function members(): array
    {
        return $this->members;
    }
}