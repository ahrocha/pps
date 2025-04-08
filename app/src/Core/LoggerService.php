<?php

namespace App\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerService
{
    private static ?Logger $logger = null;

    public static function getLogger(): Logger
    {
        if (!self::$logger) {
            self::$logger = new Logger('pps');

            $stream = 'php://stdout';
            self::$logger->pushHandler(new StreamHandler($stream, Logger::DEBUG));
        }

        return self::$logger;
    }
}
