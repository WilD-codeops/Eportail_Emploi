<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {

            $config = require __DIR__ ."/../../config/database.php";
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $user = $config['user'];
            $password = $config['password'];

            try {
                self::$pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données.');
            }
        }

        return self::$pdo;
    }

    private function __construct() {}
    private function __clone() {}
}