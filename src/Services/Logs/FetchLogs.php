<?php
/**
 * 2023 - moloni.pt
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Moloni
 * @copyright Moloni
 * @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 */

namespace Moloni\Services\Logs;

use Db;

class FetchLogs
{
    private $request;
    private $logList = [];
    private $totalPendingOrders = 0;

    private $queryCondition = '';
    private $queryTotalResultsCondition = '';

    public function __construct($request = [])
    {
        $this->request = $request;
    }

    public function run(): array
    {
        $this->fetchAllLogs();

        return [
            'data' => $this->logList,
            'recordsTotal' => $this->totalPendingOrders,
            'recordsFiltered' => $this->totalPendingOrders,
        ];
    }

    private function fetchAllLogs()
    {
        // Segmentation for query, based on defined settings
        $this->fetchOrdersFilters();

        // Get total pending documents
        $this->totalPendingOrders = Db::getInstance()
            ->ExecuteS('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'moloni_logs' . $this->queryTotalResultsCondition)[0]['COUNT(*)'];

        $logs = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'moloni_logs' . $this->queryCondition);

        if ($logs) {
            foreach ($logs as $log) {
                $this->logList[] = $log;
            }
        }
    }

    private function fetchOrdersFilters()
    {
        $condition = ' AS ML';
        $condition .= ' WHERE';

        // Logged company or 0
        $condition .= ' company_id IN (0, ' . (defined('COMPANY') ? (int)COMPANY : 0) . ')';

        // Manual search
        $search = $this->request['search']['value'] ?: '';

        if (!empty($search)) {
            $condition .= ' AND message LIKE "%' . $search . '%"';
        }

        $condition .= ' ORDER BY ML.created_at';

        if (isset($this->request['order'][0]['dir'])) {
            $condition .= ' ' . strtoupper($this->request['order'][0]['dir']);
        } else {
            $condition .= ' DESC';
        }

        // Save query segmentation for totals here, because it cannot have "LIMIT"
        $this->queryTotalResultsCondition = $condition;

        // Lets limit results
        $offset = $this->request['start'] ?: 0;
        $length = $this->request['length'] ?: 10;

        $condition .= ' LIMIT ' . $length . ' OFFSET ' . $offset;

        $this->queryCondition = $condition;
    }
}
