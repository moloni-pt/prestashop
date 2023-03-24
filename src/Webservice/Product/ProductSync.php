<?php

namespace Moloni\Webservice\Product;

use Moloni\Services\ProductSyncService;
use PrestaShopDatabaseException;
use PrestaShopException;

class ProductSync
{
    private $results = [];

    public function __construct()
    {
        require_once(_PS_MODULE_DIR_ . 'moloni/controllers/admin/classes/moloni.curl.php');
        require_once(_PS_MODULE_DIR_ . 'moloni/controllers/admin/classes/moloni.start.php');
        require_once(_PS_MODULE_DIR_ . 'moloni/controllers/admin/classes/moloni.products.php');
        require_once(_PS_MODULE_DIR_ . 'moloni/controllers/admin/classes/moloni.settings.php');
        require_once(_PS_MODULE_DIR_ . 'moloni/controllers/admin/classes/prestashop.general.php');

        new \Start();

        if (defined('ENABLE_PRODUCT_SYNC_WEBSERVICE')) {
            $productSyncService = new ProductSyncService();
            $productSyncService->setImportDate(date('Y-m-d H:i:s', strtotime("-1 week")));
            $productSyncService->instantiateSyncFilters();

            try {
                $productSyncService->run();
                $this->results = $productSyncService->getResults();
            } catch (PrestaShopDatabaseException|PrestaShopException $e) {
                // No need to catch
            }
        }
    }

    public function getResults()
    {
        return json_encode($this->results);
    }
}