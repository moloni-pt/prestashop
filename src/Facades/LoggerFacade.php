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

    public static function debug($message, $context = []) {

        self::loadLogger();
        self::$LOGGER->debug($message, $context);
    }

    public static function error($message, $context = []) {

        self::loadLogger();
        self::$LOGGER->error($message, $context);
    }

    public static function critical($message, $context = []) {

        self::loadLogger();
        self::$LOGGER->critical($message, $context);
    }

    public static function info($message, $context = []) {

        self::loadLogger();
        self::$LOGGER->info($message, $context);
    }

    public static function warning($message, $context = []) {

        self::loadLogger();
        self::$LOGGER->warning($message, $context);
    }
}
