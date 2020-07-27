<?php
/**
 * 2016 - Moloni.com
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

class MoloniMovimentosController extends ModuleAdminController
{

    public function __construct()
    {

        $this->bootstrap = true;
        $this->className = 'Moloni';
        $this->context   = Context::getContext();

        require_once("classes/error.class.php");
        require_once("classes/moloni.curl.php");
        require_once("classes/moloni.start.php");

        require_once("classes/prestashop.general.php");

        $moloni    = new Start();
        $functions = new General();

        $this->moloniTpl = $moloni->template;

        $companies = null;
        $documents = null;
           
        switch ($this->moloniTpl) {
            case 'company':
                $companies = $functions->getCompaniesAll();
                break;
            case 'index':
                $this->moloniTpl = "movements";
                $documents    = $functions->getDocumentsAll();
                break;
        }


        $this->context->smarty->assign(array(
            'moloni' => array(
                'path' => array(
                    'img' => '../modules/moloni/views/img/',
                    'css' => '../modules/moloni/views/css/',
                    'js' => '../modules/moloni/views/js/'
                ),
                'documents' => $documents,
                'companies' => $companies
            )
        ));



        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function renderList()
    {
        return $this->module->display(_PS_MODULE_DIR_.'moloni', 'views/templates/admin/'.$this->moloniTpl.'.tpl');
    }
}
