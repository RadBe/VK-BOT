<?php


namespace App;


use App\Exceptions\UndefinedDateFactorException;

class Helpers
{
    /**
     * Парсинг команды из сообщения.
     * @param Config $config
     * @param string $message
     * @return array - [Command cmd, string label, array args]
     */
    public static function getCommand(Config $config, string $message): array
    {
        foreach ($config->commands() as $pattern => $data)
        {
            if (preg_match("/^$pattern/u", $message)) {
                $message = trim(preg_replace("/^$pattern/u", '', $message));
                if (!empty($message)) {
                    $args = explode(' ', $message, $data['args']);
                } else {
                    $args = [];
                }

                return [$data['executor'], $pattern, $args];
            }
        }

        return [null, null, null];
    }

    /**
     * Преобразование ID пользователя в указаный анкор ссылки.
     * @param int $id
     * @param string|array $anchor
     * @return string
     */
    public static function anchorUser(int $id, $anchor): string
    {
        return is_array($anchor)
            ? "[id{$id}|{$anchor['first_name']} {$anchor['last_name']}]"
            : "[id{$id}|$anchor]";
    }

    /**
     * Ссылка на упоминание пользователя.
     * @param int $id
     * @return string
     */
    public static function mentionUser(int $id): string
    {
        return "@id{$id}";
    }

    /**
     * Преобразование слова в множитель даты.
     * Например слово часов будет преобразовано в 86400 секунд.
     * @param string $word
     * @return int
     * @throws UndefinedDateFactorException
     */
    public static function dateFactor(?string $word): int
    {
        $word = trim($word);
        if (mb_strlen($word) < 1) {
            throw new UndefinedDateFactorException($word);
        }

        $letter = mb_substr(mb_strtolower($word), 0, 1);

        switch ($letter)
        {
            case 's':
            case 'с': return 1;

            case 'm':
            case 'м': return 60;

            case 'h':
            case 'ч': return 3600;

            case 'd':
            case 'д': return 86400;
        }

        throw new UndefinedDateFactorException($word . " ($letter)");
    }
}