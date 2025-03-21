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
 * @author    Nuno Almeida
 * @copyright Nuno Almeida
 * @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 */

class Moloni extends Module
{
    public function __construct()
    {
        $this->name = 'moloni';
        $this->tab = 'administration';
        $this->need_instance = 1;
        $this->version = '3.2.3';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->author = 'Moloni';
        $this->bootstrap = true;
        $this->module_key = 'c1b44ca634a5bc18032f311803470fea';

        $this->autoload();

        parent::__construct();

        $this->displayName = $this->l('Moloni');
        $this->description = $this->l('Transform all your orders in verified documents without any effort and focus on selling!');
    }

    /**
     * @return bool success
     * */
    public function install()
    {
        $this->setMenu('MoloniTab', $this->l('Moloni'), (_PS_VERSION_ > 1.6) ? Tab::getIdFromClassName('SELL') : '0');
        $this->setMenu('MoloniStart', $this->l('Moloni'), Tab::getIdFromClassName('MoloniTab'));
        $this->setMenu('MoloniMovimentos', $this->l('Documents'), Tab::getIdFromClassName('MoloniTab'));
        $this->setMenu('MoloniConfiguracao', $this->l('Settings'), Tab::getIdFromClassName('MoloniTab'));
        $this->setMenu('MoloniTools', $this->l('Tools'), Tab::getIdFromClassName('MoloniTab'));
        $this->setMenu('MoloniLogs', $this->l('Logs'), Tab::getIdFromClassName('MoloniTab'));

        return parent::install()
            && $this->dbInstall()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayOrderDetail')
            && $this->registerHook('addWebserviceResources')
            && $this->registerHook('actionOrderStatusPostUpdate') //after order status is changed
            && $this->registerHook('actionProductSave');
    }

    /**
     * @return bool success
     * */
    public function uninstall()
    {
        $this->delMenu('MoloniTab');
        $this->delMenu('MoloniStart');
        $this->delMenu('MoloniMovimentos');
        $this->delMenu('MoloniConfiguracao');
        $this->delMenu('MoloniTools');
        $this->delMenu('MoloniLogs');

        return parent::uninstall() && $this->dbUninstall();
    }

    public function hookActionPaymentConfirmation($params)
    {
    }

    /**
     * Fires after product update/create
     * @param $params
     * @return bool
     */
    public function hookActionProductSave($params)
    {
        new \Moloni\Classes\Start();
        $functions = new \Moloni\Classes\General();

        if (defined('AUTO_ADD_PRODUCT') && AUTO_ADD_PRODUCT == 1) {
            $functions->productCreate($params);
        }

        return true;
    }

    /**
     * Fires after an orders has it status changed
     * @param $params
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        new \Moloni\Classes\Start();

        if (defined('INVOICE_AUTO')) {
            if ((int)INVOICE_AUTO === 1) {
                /** @var OrderState $newOrderStatus */
                $newOrderStatus = $params['newOrderStatus'];

                if ($newOrderStatus->paid) {
                    $functions = new \Moloni\Classes\General();

                    $functions->makeInvoice($params['id_order'], true);
                }
            } else if ((int)INVOICE_AUTO === 2) {
                //check if the new status was chosen in settings
                if (defined('ORDER_STATUS') && in_array($params['newOrderStatus']->id, unserialize(ORDER_STATUS))) {
                    $functions = new \Moloni\Classes\General();

                    $functions->makeInvoice($params['id_order'], true);
                }
            }

            if (\Moloni\Classes\MoloniError::$exists) {
                \Moloni\Classes\MoloniError::$message;
            }
        }
    }

    /**
     * Add endpoints to Prestashop Webservices
     *
     * @return array[]
     */
    public function hookAddWebserviceResources()
    {
        if (\Moloni\Helpers\Version::isPrestashopVersion_1_6()) {
            return [];
        }

        include_once _PS_MODULE_DIR_ . 'moloni/src/Webservice/WebserviceSpecificManagementMoloniResource.php';

        return [
            'moloniresource' => [
                'description' => 'Moloni sync resource',
                'specific_management' => true,
            ],
        ];
    }

    /**
     * Module tables installation
     *
     * @return bool success
     * */
    protected function dbInstall()
    {
        require('sql/install.php');
        return true;
    }

    /**
     * Module tables installation
     *
     * @return bool success
     * */
    protected function dbUninstall()
    {
        require('sql/uninstall.php');
        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path . 'views/css/moloni-icons.css');
    }

    public function hookDisplayOrderDetail($data)
    {
        if (empty($data['order'])) {
            return '';
        }

        $service = new \Moloni\Hooks\DisplayOrderDetail($data['order']);
        $service->run();

        return $service->getHtml();
    }

    public function setMenu($className, $text, $parent = '2')
    {
        $tab = new Tab();

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int)$lang['id_lang']] = $text;
        }

        $tab->class_name = $className;
        $tab->id_parent = $parent;
        $tab->module = $this->name;
        $tab->icon = 'logo';
        $tab->add();

        unset($tab);
        return true;
    }

    public function delMenu($className)
    {
        $idTab = Tab::getIdFromClassName($className);

        if ((int)$idTab > 0) {
            $tab = new Tab($idTab);
            $tab->delete();

            return true;
        }

        return false;
    }

    /**
     * Inits Autoload
     */
    private function autoload()
    {
        require_once __DIR__ . '/vendor/autoload.php';
    }
}
