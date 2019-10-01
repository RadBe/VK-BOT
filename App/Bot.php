<?php


namespace App;


use App\Database\MySQL;

class Bot
{
    /**
     * Версия вк апи.
     */
    public const API_VERSION = '5.101';

    /**
     * Базовое значение peer_id.
     */
    public const BASE_PEER_ID = 2000000000;

    /**
     * Адрес обращения к апи.
     */
    public const API_ENDPOINT = 'https://api.vk.com/method/';

    /**
     * Название типа события для подтверждения сервера.
     */
    public const API_EVENT_CONFIRMATION = 'confirmation';

    /**
     * Название типа события для входящего сообщения.
     */
    public const API_EVENT_MESSAGE_NEW = 'message_new';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MySQL
     */
    private $db;

    /**
     * Bot constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        Request::init();
        $this->config = $config;
        $dbConfig = $config->db();
        $this->db = new MySQL($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname']);
    }

    /**
     * @return Config
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * @return MySQL
     */
    public function db(): MySQL
    {
        return $this->db;
    }

    /**
     * @throws Exceptions\FailedRequestException
     * @throws Exceptions\InvalidResponseException
     * @throws Exceptions\UnsupportedEventException
     */
    public function run(): void
    {
        $handler = new EventListener($this->config, $this->db);
        $handler->handle();
    }
}