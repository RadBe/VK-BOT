<?php


namespace App;


use App\Callback\Event\ConfirmationEvent;
use App\Callback\Event\Message\MessageNewEvent;
use App\Database\MySQL;
use App\Exceptions\UnsupportedEventException;
use App\Listeners\ConfirmationListener;
use App\Listeners\UserMessageListener;

class EventListener
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var MySQL
     */
    private $db;

    /**
     * CommandExecutor constructor.
     * @param Config $config
     * @param MySQL $db
     */
    public function __construct(Config $config, MySQL $db)
    {
        $this->config = $config;
        $this->db = $db;
    }

    /**
     * @throws Exceptions\FailedRequestException
     * @throws Exceptions\InvalidResponseException
     * @throws UnsupportedEventException
     */
    public function handle(): void
    {
        $type = Request::post('type');
        switch ($type)
        {
            case Bot::API_EVENT_CONFIRMATION:
                $event = new ConfirmationEvent();
                $listener = new ConfirmationListener($this->config, $this->db);
                break;

            case Bot::API_EVENT_MESSAGE_NEW:
                $event = new MessageNewEvent();
                $listener = new UserMessageListener($this->config, $this->db);
                break;

            default: throw new UnsupportedEventException($type);
        }

        $listener->handle($event);
    }
}