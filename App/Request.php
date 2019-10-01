<?php


namespace App;


class Request
{
    /**
     * @var array
     */
    private static $get;

    /**
     * @var array
     */
    private static $post;

    /**
     * Request constructor.
     */
    public static function init()
    {
        self::loadPost();
    }

    /**
     * @return void
     */
    private static function loadPost(): void
    {
        if (isset(self::$get['test_json'])) {
            self::$post = json_decode(self::$get['test_json'], true);
        } else {
            self::$post = json_decode(file_get_contents('php://input'), true);
        }
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return array|mixed|null
     */
    public static function post(?string $key = null, $default = null)
    {
        if (empty($key)) {
            return self::$post;
        }

        return self::$post[$key] ?? $default;
    }

    /**
     * Request constructor.
     */
    private function __construct(){}
}