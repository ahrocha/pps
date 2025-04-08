<?php

namespace App\Core;

use PDO;
use PDOException;
use Exception;

class DatabaseService
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            $host = getenv('DB_HOST') ?: 'write-db';
            $db   = getenv('DB_NAME') ?: 'pps_write';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: 'secret';

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            try {
                self::$connection = new PDO($dsn, $user, $pass);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception('Erro ao conectar com o banco de dados: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
