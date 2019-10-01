<?php


namespace App\Database;


use PDO;
use PDOException;

class MySQL
{
    /**
     * @var PDO
     */
    private $statement;

    /**
     * MySQL constructor.
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $name
     * @param string $charset
     * @throws PDOException
     */
    public function __construct(string $host, string $user, string $pass, string $name, string $charset = 'utf8')
    {
        $dsn = "mysql:host=$host;dbname=$name;charset=$charset";
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $this->statement = new PDO($dsn, $user, $pass, $options);
    }

    /**
     * @return PDO
     */
    public function stmt(): PDO
    {
        return $this->statement;
    }
}