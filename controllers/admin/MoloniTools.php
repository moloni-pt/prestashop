<?php
/**
 * 2020 - moloni.pt
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
 *  @author    Nuno Almeida
 *  @copyright Nuno Almeida
 *  @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 */

use Moloni\Classes\General;
use Moloni\Classes\Start;
use Moloni\Services\Tools\ProductSyncService;
use Moloni\Facades\ModuleFacade;

class MoloniToolsController extends ModuleAdminController
{
    public $moloniTpl = null;

    public function __construct()
    {
        parent::__construct();

        ModuleFacade::setModule($this->module);

        $this->bootstrap = true;
        $this->className = 'Moloni';
        $this->context = Context::getContext();

        $moloni = new Start();
        $functions = new General();

        if (!$this->ajax) {
            $this->moloniTpl = $moloni->template;

            $companies = null;
            $configurations = null;

            switch ($this->moloniTpl) {
                case 'company':
                    $companies = $functions->getCompaniesAll();
                    break;

                case 'index':
                    $this->moloniTpl = "tools";
                    break;
            }

            $this->context->smarty->assign([
                'moloni' => [
                    'path' => [
                        'img' => '../modules/moloni/views/img/',
                        'css' => '../modules/moloni/views/css/',
                        'js' => '../modules/moloni/views/js/'
                    ],
                    'version' => Module::getInstanceByName('moloni')->version,
                    'companies' => $companies,
                    'configurations' => $configurations,
                ],
                'html' => $moloni->template
            ]);
        }
    }

    public function displayAjax()
    {
        $params = Tools::getAllValues();
        $response = [
            'valid' => 1,
            'message' => ''
        ];

        switch ($params['operation']) {
            case 'syncProducts':
                $since = Tools::getValue('since', '');
                $page = Tools::getValue('page', 1);

                $productSyncService = new ProductSyncService();
                $productSyncService->setImportDate($since);
                $productSyncService->setPage($page);
                $productSyncService->instantiateSyncFilters();
                $productSyncService->run();
                $productSyncService->saveLog();

                $hasMore = $productSyncService->getTotalProducts() >= $productSyncService->getPerPage();
                $processedProducts = ($productSyncService->getPage() - 1) * $productSyncService->getPerPage();
                $processedProducts += $productSyncService->getTotalProducts();

                $this->context->smarty->assign([
                    'processedProducts' => $processedProducts,
                    'hasMore' => $hasMore
                ]);

                $response['hasMore'] = $hasMore;
                $response['results'] = $productSyncService->getResults();

                $response['overlayContent'] = $this->module->display(_PS_MODULE_DIR_ . 'moloni', 'views/templates/admin/tools/blocks/productSyncContent.tpl');

                break;
        }

        echo json_encode($response);
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function renderList()
    {
        return $this->module->display(_PS_MODULE_DIR_ . 'moloni', 'views/templates/admin/' . $this->moloniTpl . '.tpl');
    }

}
