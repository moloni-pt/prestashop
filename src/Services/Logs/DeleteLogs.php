<?php

namespace Moloni\Services\Logs;

use Db;
use Moloni\Facades\LoggerFacade;
use Moloni\Facades\ModuleFacade;
use Moloni\Traits\ClassTrait;

class DeleteLogs
{
    use ClassTrait;

    private $since;

    public function __construct($since = '')
    {
        if (empty($since)) {
            $since = date('Y-m-d H:i:s', strtotime("-1 week"));
        }

        $this->since = pSQL($since);
    }

    public function run()
    {
        Db::getInstance()->delete('moloni_logs', 'created_at < "' . $this->since . '"');
    }

    public function saveLog()
    {
        $logMessage = ModuleFacade::getModule()->l('Logs deleted.', $this->className());

        LoggerFacade::info($logMessage, [
            'tag' => 'service:logs:delete',
            'since' => $this->since
        ]);
    }
}
