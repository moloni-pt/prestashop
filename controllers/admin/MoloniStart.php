<?php
/**
 * 2020 - Moloni.com
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

class MoloniStartController extends ModuleAdminController
{
    public $moloniTpl = null;

    public function __construct()
    {

        $this->bootstrap = true;
        $this->className = 'Moloni';
        $this->context = Context::getContext();

        require_once(__DIR__ . "/classes/error.class.php");
        require_once(__DIR__ . "/classes/moloni.curl.php");
        require_once(__DIR__ . "/classes/moloni.start.php");
        require_once(__DIR__ . "/classes/prestashop.general.php");

        $moloni = new Start();
        $functions = new General();

        $this->moloniTpl = $moloni->template;

        $companies = null;
        $orders = null;
        $message = array();

        #Gerar o documento

        if (Tools::getValue('action') && Tools::getValue('action') === "create" && Tools::getValue('id_order')) {
            $result = $functions->makeInvoice(Tools::getValue('id_order'));
            if (MoloniError::$exists) {
                $message['error'] = MoloniError::$message;
            } else {
                $message['success'] = $result;
            }
        }

        if (Tools::getValue('action') && Tools::getValue('action') === "clean" && Tools::getValue('id_order')) {
            $result = $functions->cleanInvoice(Tools::getValue('id_order'));
            $message['success'] = $result;
        }

        if (Tools::getValue('action') && Tools::getValue('action') === "cleanAnulate" && Tools::getValue('id_order')) {
            $result = $functions->cleanInvoiceAnulate(Tools::getValue('id_order'));
            $message['success'] = $result;
        }

        switch ($this->moloniTpl) {
            case 'company':
                $companies = $functions->getCompaniesAll();
                break;
            case 'index':
                $orders = $functions->getOrdersAll();
                break;
        }

        $this->context->smarty->assign(array(
            'moloni' => array(
                'path' => array(
                    'img' => '../modules/moloni/views/img/',
                    'css' => '../modules/moloni/views/css/',
                    'js' => '../modules/moloni/views/js/'
                ),
                'companies' => $companies,
                'orders' => $orders,
                'message' => $message
            ),
            'html' => $moloni->template
        ));

        if (defined("MOLONI_ERROR_LOGIN")) {
            $this->context->smarty->assign(array(
                'moloni_error' => array(
                    'login' => "login-errado"
                )
            ));
        }

        parent::__construct();
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
