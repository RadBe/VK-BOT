<?php

return [
    'confirmation_token' => '',
    'access_token' => '',
    'group_id' => 0,

    'commands' => [
        'бот оффлайн' => [
            'executor' => '\App\Commands\OfflineCommand',
            'args' => 0,
            'description' => 'Список оффлайн пользователей'
        ],
        'бот онлайн' => [
            'executor' => '\App\Commands\OnlineCommand',
            'args' => 0,
            'description' => 'Список онлайн пользователей'
        ],
        'бот ранг' => [
            'executor' => '\App\Commands\RankCommand',
            'args' => 2,
            'description' => 'Показать ранг'
        ],
        'бот кик' => [
            'executor' => '\App\Commands\KickCommand',
            'args' => 1,
            'description' => 'Кикнуть пользователя'
        ],
        'бот муты' => [
            'executor' => '\App\Commands\MutesCommand',
            'args' => 0,
            'description' => 'Список пользователей с мутом'
        ],
        'бот мут-' => [
            'executor' => '\App\Commands\UnmuteCommand',
            'args' => 1,
            'description' => 'Снять мут пользователя'
        ],
        'бот мут' => [
            'executor' => '\App\Commands\MuteCommand',
            'args' => 3,
            'description' => 'Выдать мут пользователю'
        ],
        'бот дата' => [
            'executor' => '\App\Commands\DateTimeCommand',
            'args' => 1,
            'description' => 'Показать текущую дату и время'
        ],
        'бот тест' => [
            'executor' => '\App\Commands\TestCommand',
            'args' => 0,
            'description' => 'Тестовая команда'
        ],
        'бот' => [
            'executor' => '\App\Commands\HelpCommand',
            'args' => 0,
            'description' => 'Список команд'
        ],
    ],

    'min_rank' => 1,
    'max_rank' => 10,
    'default_rank' => 1,

    'db' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'pass' => '',
        'dbname' => 'bot',
        'charset' => 'utf8'
    ],
];
