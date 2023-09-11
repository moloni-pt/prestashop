<?php

namespace Moloni\Facades;

use Moloni\Logs\Logger;
use Psr\Log\LoggerInterface;

class LoggerFacade
{
    /**
     * Logger instance
     *
     * @var LoggerInterface|null
     */
    private static $LOGGER;

    private static function loadLogger()
    {
        if (empty(self::$LOGGER)) {
            self::$LOGGER = new Logger();
        }
    }

    public static function debug(string $message, ?array $context = []) {

        self::loadLogger();
        self::$LOGGER->debug($message, $context);
    }

    public static function error(string $message, ?array $context = []) {

        self::loadLogger();
        self::$LOGGER->error($message, $context);
    }

    public static function critical(string $message, ?array $context = []) {

        self::loadLogger();
        self::$LOGGER->critical($message, $context);
    }

    public static function info(string $message, ?array $context = []) {

        self::loadLogger();
        self::$LOGGER->info($message, $context);
    }

    public static function warning(string $message, ?array $context = []) {

        self::loadLogger();
        self::$LOGGER->warning($message, $context);
    }
}
