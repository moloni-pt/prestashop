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
 *
 * @noinspection SqlNoDataSourceInspection
 * @noinspection SqlResolve
 */

namespace Moloni\Classes;

use Db;
use Moloni\Mails\AuthenticationExpiredMail;
use Tools;
use ModuleAdminController;
use Moloni\Webservice\Webservices;
use Moloni\Classes\Curl;

class Start extends ModuleAdminController
{
    public $template = '';
    public $message = '';

    public function __construct()
    {
        if (Tools::getValue('goDo') && Tools::getValue('goDo') === 'save' && Tools::getValue('options')) {
            $this->variablesUpdate();
        }

        if (Tools::getValue('MoloniLogout') && Tools::getValue('MoloniLogout') === 'true') {
            Db::getInstance()->execute('TRUNCATE ' . _DB_PREFIX_ . 'moloni');
        }

        if (Tools::getValue('mol-username') && Tools::getValue('mol-password')) {
            #Tentativa de Login
            $validate = Curl::login(Tools::getValue('mol-username'), Tools::getValue('mol-password'));
            if (!$validate) {
                #Utilizador/password errada
                $this->template = 'login';
                $this->message = array(
                    'label' => 'login-errado',
                    'text' => 'Ups, combinação errada, tenta novamente :)'
                );
                define('MOLONI_ERROR_LOGIN', 'login-errado');
            } else {
                #Utilizador/password correcto
                #Primeiro Login

                define('ACCESS', $validate['access_token']);
                $timeNow = time();
                $timeExpire = $timeNow + 3000;

                Db::getInstance()->execute('TRUNCATE ' . _DB_PREFIX_ . 'moloni');
                Db::getInstance()->insert('moloni', array(
                    'access_token' => pSQL($validate['access_token']),
                    'refresh_token' => pSQL($validate['refresh_token']),
                    'date_login' => pSQL(date('Y-m-d H:i:s')),
                    'date_expire' => pSQL($timeExpire),
                    'company_id' => pSQL(0),
                ));
                $this->variablesCheck();
                $this->template = 'company';
            }
        } else {
            #Não são enviados dados de login

            if (Tools::getValue('company_id')) {
                Db::getInstance()->update('moloni', array(
                    'company_id' => (int)Tools::getValue('company_id')
                ));
            }

            $row = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'moloni', false);
            if (isset($row['refresh_token'])) {
                #Tem registo em base de dados

                if ((!isset($row['company_id']) || $row['company_id'] == '0')) {
                    #Caso ainda não tenha escolhido empresa
                    if ($row['date_expire'] > time()) {
                        $this->variablesCheck();
                        $this->template = 'company';
                    } else {

                        Db::getInstance()->execute('TRUNCATE ' . _DB_PREFIX_ . 'moloni');
                        $this->template = 'login';
                        $this->message = array(
                            'label' => 'sessao-expirada',
                            'text' => 'A ligação expirou, faça login novamente.'
                        );
                    }
                } else {
                    #Login feito, e empresa seleccionada
                    #Tentar refresh se for preciso
                    if ($row['date_expire'] < time()) {
                        $refresh = Curl::refresh($row['refresh_token']);

                        if (!$refresh) {
                            sleep(2000);
                            $refresh = Curl::refresh($row['refresh_token']);
                        }

                        if (!$refresh) {
                            sleep(2000);
                            $refresh = Curl::refresh($row['refresh_token']);
                        }

                        if (!$refresh) {
                            #Refresh não deu, volta a fazer login

                            Db::getInstance()->execute('TRUNCATE ' . _DB_PREFIX_ . 'moloni');
                            $this->template = 'login';
                            $this->message = array(
                                'label' => 'sessao-expirada',
                                'text' => 'A ligação expirou, faça login novamente.'
                            );

                            if (!empty($row['alert_email'])) {
                                $alert = new AuthenticationExpiredMail($row['alert_email']);
                                $alert->handle();
                            }
                        } else {
                            #Refresh deu, continua normalmente
                            $timeNow = time();
                            $timeExpire = $timeNow + 3000;
                            Db::getInstance()->update('moloni', array(
                                'access_token' => pSQL($refresh['access_token']),
                                'refresh_token' => pSQL($refresh['refresh_token']),
                                'date_expire' => pSQL($timeExpire),
                            ));

                            $this->variablesDefine();
                            $this->templateSelect();
                        }
                    } else {
                        $this->variablesDefine();
                        $this->templateSelect();
                    }
                }
            } else {
                #Não tem dados na base de dados
                $this->template = 'login';
            }
        }

        $row = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'moloni');

        if (!defined('ACCESS') && is_array($row)) {
            define('ACCESS', $row['access_token']);
        }

        if (!defined('COMPANY') && is_array($row)) {
            define('COMPANY', $row['company_id']);
        }
    }

    public function templateSelect()
    {
        $this->template = 'index';
        if (Tools::getValue('action')) {
            switch ($_REQUEST['action']) {
                case 'movimentos':
                    $this->template = 'movimentos';
                    break;
                case 'config':
                    $this->template = 'config';
                    break;
                case 'invoice':
                    $this->template = 'invoice';
                    break;
            }
        }
    }

    //          Variables          //

    public function variablesDefine()
    {
        if ($results = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'moloni_configs')) {
            foreach ($results as $vars_r) {
                if (!defined(Tools::strtoupper($vars_r['label']))) {
                    define(Tools::strtoupper($vars_r['label']), $vars_r['value']);
                }
            }
        }
    }

    public function variablesUpdate()
    {
        $this->variablesCheck();

        $options = Tools::getValue('options');

        if (empty($options['order_status'])) {
            $options['order_status'] = [];
        } else {
            $options['order_status'] = array_keys($options['order_status']);
        }

        if ((int)$options['enable_product_sync_webservice'] === 1) {
            (new Webservices())->enable();
        } else {
            (new Webservices())->disable();
        }

        foreach ($options as $key => $value) {
            $val = (is_array($value) ? serialize($value) : $value);

            $this->updateVariableByKey($key, $val);
        }
    }

    public function variablesCheck()
    {
        $defines = [];
        $defines[] = [
            'label' => 'document_set',
            'name' => 'Série de documentos',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'after_date',
            'name' => 'Encomendas desde',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'exemption_reason',
            'name' => 'Razão de isenção',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'exemption_reason_shipping',
            'name' => 'Razão de isenção portes',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'payment_method',
            'name' => 'Método de pagamento',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'measure_unit',
            'name' => 'Unidade de medida',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'maturity_date',
            'name' => 'Prazo de vencimento',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'update_customer',
            'name' => 'Actualizar cliente',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'document_status',
            'name' => 'Estado do documento',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'show_shipping_information',
            'name' => 'Informações de envio',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'enable_product_sync_webservice',
            'name' => 'Ativar webservice para sincronização de artigos',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'invoice_auto',
            'name' => 'Gerar automaticamente',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'email_send',
            'name' => 'Enviar email',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'client_prefix',
            'name' => 'Prefixo do cliente',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'product_prefix',
            'name' => 'Prefixo do artigo',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'document_type',
            'name' => 'Tipo de documento',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'order_status',
            'name' => 'Estado da Encomenda',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'at_category',
            'name' => 'Categoria AT',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'stock_from_moloni',
            'name' => 'Importar stock do moloni',
            'description' => '',
            'value' => '0'
        ];
        $defines[] = [
            'label' => 'stock_from_moloni_last',
            'name' => 'Importar stock do moloni',
            'description' => '',
            'value' => '0'
        ];
        $defines[] = [
            'label' => 'auto_add_product',
            'name' => 'Adicionar artigos novos',
            'description' => '',
            'value' => '0'
        ];
        $defines[] = [
            'label' => 'fiscal_zone_based_on',
            'name' => 'Zona fiscal do documento',
            'description' => '',
            'value' => ''
        ];
        $defines[] = [
            'label' => 'alert_email',
            'name' => 'Alerta de erros',
            'description' => '',
            'value' => ''
        ];

        foreach ($defines as $variable) {
            $row = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "moloni_configs WHERE label = '" . pSQL($variable['label']) . "'");

            if (!$row) {
                $this->insertNewVariable($variable['label'], $variable['name'], $variable['description'], $variable['value']);
            }
        }
    }

    //          Auxiliary          //

    public function insertNewVariable($label, $name, $description, $value)
    {
        Db::getInstance()->insert('moloni_configs', array(
            'label' => pSQL($label),
            'name' => pSQL($name),
            'description' => pSQL($description),
            'value' => pSQL($value),
        ));
    }

    public function updateVariableByKey($key, $value)
    {
        Db::getInstance()->update('moloni_configs',
            ['value' => pSQL($value)],
            "label = '" . pSQL($key) . "'"
        );
    }
}
