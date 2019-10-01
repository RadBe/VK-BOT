<?php


namespace App;


class Config
{
    /**
     * Токен подтверждения адреса сервера.
     * @var string
     */
    private $confirmationToken;

    /**
     * Токен доступа к ВК-апи.
     * @var string
     */
    private $accessToken;

    /**
     * ID группы бота.
     * @var int
     */
    private $groupId;

    /**
     * Команды.
     * @var array
     */
    private $commands;

    /**
     * Минимальный ранг.
     * @var int
     */
    private $minRank = 1;

    /**
     * Максимальный ранг.
     * @var int
     */
    private $maxRank = 10;

    /**
     * Ранг по-умолчанию.
     * @var int
     */
    private $defaultRank = 1;

    /**
     * Настройки БД.
     * @var array
     */
    private $db;

    /**
     * Config constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->loadConfig($data);
    }

    /**
     * Загрузка конфига.
     * @param array $data
     */
    private function loadConfig(array $data): void
    {
        $this->confirmationToken = $data['confirmation_token'];
        $this->accessToken = $data['access_token'];
        $this->groupId = $data['group_id'];
        $this->commands = $data['commands'];
        $this->minRank = $data['min_rank'];
        $this->maxRank = $data['max_rank'];
        $this->defaultRank = $data['default_rank'];
        $this->db = $data['db'];
    }

    /**
     * @return string
     */
    public function confirmationToken(): string
    {
        return $this->confirmationToken;
    }

    /**
     * @return string
     */
    public function accessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return array
     */
    public function commands(): array
    {
        return $this->commands;
    }

    /**
     * @return int
     */
    public function minRank(): int
    {
        return $this->minRank;
    }

    /**
     * @return int
     */
    public function maxRank(): int
    {
        return $this->maxRank;
    }

    /**
     * @return int
     */
    public function defaultRank(): int
    {
        return $this->defaultRank;
    }

    /**
     * @return array
     */
    public function db(): array
    {
        return $this->db;
    }
}