<?php

namespace Moloni\Webservice\Product;

use Exception;
use Moloni\Classes\Start;
use Moloni\Services\Tools\ProductSyncService;

class ProductSync
{
    private $start;

    private $results = [];

    public function __construct()
    {
        $this->start = new Start();

        if (defined('ENABLE_PRODUCT_SYNC_WEBSERVICE')) {
            $date = $this->getSinceDate();

            $productSyncService = new ProductSyncService();
            $productSyncService->setImportDate($date);
            $productSyncService->instantiateSyncFilters();
            $productSyncService->saveLog();

            try {
                $productSyncService->run();
                $productSyncService->saveLog();

                $this->results = $productSyncService->getResults();
            } catch (Exception $e) {
                $this->results = [
                    'fatal_error' => [
                        'error' => $e->getMessage()
                    ]
                ];
            }
        }
    }

    //          Gets          //

    public function getResults()
    {
        return json_encode($this->results);
    }

    //          Privates          //

    private function getSinceDate()
    {
        $dateNow = date('Y-m-d H:i:s');
        $dateToUse = '';

        if (defined('LAST_WEBSERVICE_PRODUCT_SYNC_RUN')) {
            if (!empty(LAST_WEBSERVICE_PRODUCT_SYNC_RUN)) {
                $dateToUse = LAST_WEBSERVICE_PRODUCT_SYNC_RUN;
            }

            $this->start->updateVariableByKey('last_webservice_product_sync_run', $dateNow);
        } else {
            $this->start->insertNewVariable('last_webservice_product_sync_run', 'Ultima sincronização por WebService', '', $dateNow);
        }

        if (empty($dateToUse)) {
            $dateToUse = date('Y-m-d H:i:s', strtotime("-1 hour"));
        }

        return $dateToUse;
    }
}
