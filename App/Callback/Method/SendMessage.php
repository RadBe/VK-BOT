<?php


namespace App\Callback\Method;


class SendMessage implements Method
{
    /**
     * @var int
     */
    private $peerId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $disableMentions = 0;

    /**
     * SendMessage constructor.
     * @param int $peerId
     * @param string $message
     */
    public function __construct(int $peerId, string $message)
    {
        $this->peerId = $peerId;
        $this->message = $message;
    }

    /**
     * Отключает упоминание участника беседы.
     * @param bool $val
     */
    public function disableMentions(bool $val = true): void
    {
        $this->disableMentions = (int) $val;
    }

    /**
     * Возвращает название метода.
     * @return string
     */
    public function methodName(): string
    {
        return 'messages.send';
    }

    /**
     * Преобразовывает данные в готовый для отправки вк-апи формат.
     * @return array
     */
    public function params(): array
    {
        return [
            'random_id' => time() . rand(1, 999999),
            'peer_id' => $this->peerId,
            'message' => $this->message,
            'disable_mentions' => $this->disableMentions
        ];
    }
}