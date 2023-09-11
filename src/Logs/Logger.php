<?php

namespace Moloni\Logs;

use Db;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = [])
    {
        Db::getInstance()->insert('moloni_logs', [
            'log_level' => $level,
            'company_id' => defined('COMPANY') ? (int)COMPANY : 0,
            'message' => $message,
            'context' => json_encode($context),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
