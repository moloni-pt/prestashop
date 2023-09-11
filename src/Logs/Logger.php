<?php

namespace Moloni\Logs;

use Db;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = [])
    {
        Db::getInstance()->insert('moloni_logs', [
            'log_level' => pSQL($level),
            'company_id' => defined('COMPANY') ? (int)COMPANY : 0,
            'message' => pSQL($message),
            'context' => json_encode($context),
            'created_at' => pSQL(date('Y-m-d H:i:s')),
        ]);
    }
}
