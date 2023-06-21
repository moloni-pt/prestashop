<?php

namespace Moloni\Webservice\Product;

use Exception;
use Moloni\Classes\Start;
use Moloni\Services\ProductSyncService;

class ProductSync
{
    private $results = [];

    public function __construct()
    {
        new Start();

        if (defined('ENABLE_PRODUCT_SYNC_WEBSERVICE')) {
            $productSyncService = new ProductSyncService();
            $productSyncService->setImportDate(date('Y-m-d H:i:s', strtotime("-1 week")));
            $productSyncService->instantiateSyncFilters();

            try {
                $productSyncService->run();
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

    public function getResults()
    {
        return json_encode($this->results);
    }
}
