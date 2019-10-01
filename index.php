<?php

include_once __DIR__ . '/vendor/autoload.php';

try {
    $file = __DIR__ . '/config.php';
    if (!is_file($file)) {
        throw new \App\Exceptions\ConfigNotFoundException($file);
    }

    $bot = new \App\Bot(new \App\Config(require_once $file));
    $bot->run();
} catch (\App\Exceptions\ConfigNotFoundException $exception) {
    print 'Config not found';
} catch (\App\Exceptions\FailedRequestException | \App\Exceptions\InvalidResponseException | \App\Exceptions\UnsupportedEventException $exception) {
    print $exception->getMessage();
}
