<?php


namespace App\Listeners;


use App\Callback\Event\Event;
use App\Config;
use App\Database\MySQL;

abstract class Listener
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var MySQL
     */
    protected $db;

    /**
     * Listener constructor.
     * @param Config $config
     * @param MySQL $db
     */
    public function __construct(Config $config, MySQL $db)
    {
        $this->config = $config;
        $this->db = $db;
    }

    /**
     * @param Event $event
     */
    public abstract function handle($event): void;
}