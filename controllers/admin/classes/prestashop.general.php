<?php

/**
 * 2020 - moloni.pt
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.a
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
 * @noinspection SqlResolve
 * @noinspection SqlNoDataSourceInspection
 */
class General
{

    private $me = [];
    private $countries = [];
    private $moloniExchangeId = 0;
    private $moloniExchangeRate = 1;

    private $eac_id = false;
    private $freeShipping = false;
    private $priceHasTaxIncluded = false;

    /** @var Settings */
    public $settings;

    /** @var Products */
    public $products;

    public function __construct()
    {
        $this->default_lang = Configuration::get('PS_LANG_DEFAULT');
    }

    public function getCompaniesAll()
    {
        $companies = [];

        $values = curl::simple('companies/getAll');

        foreach ($values as $company) {
            $companies[] = array(
                'vat' => $company['vat'],
                'name' => $company['name'],
                'company_id' => $company['company_id'],
                'address' => $company['address'],
                'city' => $company['city'],
                'zip_code' => $company['zip_code'],
                'form_url' => $this->genURL('MoloniStart', '&company_id=' . $company['company_id']),
                'image' => $company['image'] ?: ''
            );
        }

        return ($companies);
    }


    public function getConfigsAll()
    {
        $populated = null;

        if ($results = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'moloni_configs')) {
            foreach ($results as $row) {

                $values = (is_array(@unserialize($row['value'])) ? unserialize($row['value']) : $row['value']);

                $populated[$row['label']] = [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'value' => $values,
                    'options' => $this->getConfigsOptions($row['label'])
                ];
            }
        }

        $populated['formSave'] = $this->genURL('MoloniConfiguracao', '&goDo=save');
        $populated['formToolsSubmit'] = $this->genURL('MoloniConfiguracao', '&goDo=synchronize');
        $populated['logout'] = $this->genURL('MoloniStart', '&MoloniLogout=true');

        return ($populated);
    }

    private function getConfigsOptions($label)
    {
        $options = null;

        switch ($label) {

            case 'document_set':
                $settings = new Settings();
                $options = $settings->documentSets->getAll();
                break;

            case 'order_status':
                $options = array();

                if ($results = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'order_state_lang WHERE id_lang = ' . Configuration::get('PS_LANG_DEFAULT'))) {
                    foreach ($results as $row) {
                        $options[] = array('id' => $row['id_order_state'], 'name' => $row['name']);
                    }
                }
                break;

            case 'exemption_reason':
                $globalData = new GlobalData();
                $options = $globalData->taxExemptions->getAll();
                break;

            case 'measure_unit':
                $settings = new Settings();
                $options = $settings->measurementUnits->getAll();
                break;

            case 'maturity_date':
                $settings = new Settings();
                $options = $settings->maturityDates->getAll();
                break;
        }

        return ($options);
    }

    private function genURL($controller, $extra = '')
    {
        $url = 'index.php?controller=' . $controller . $extra . '&token=' . Tools::getAdminTokenLite($controller);
        return ($url);
    }

    private function vatCheck($input)
    {
        $vat = trim($input);
        $vat = str_replace('PT', '', $vat);

        $usual = array('000000000', '111111111', '123123123', '-');

        if (empty($vat) || in_array($vat, $usual, true)) {
            $vat = '999999990';
        }

        return $vat;
    }

    #Verificar se o código postal está com o formato correcto e tenta compor
    private function zipCheck($input)
    {
        $zipCode = trim(str_replace(' ', '', $input));
        $zipCode = preg_replace('/[^0-9]/', '', $zipCode);

        if (Tools::strlen($zipCode) == 7) {
            $zipCode = $zipCode[0] . $zipCode[1] . $zipCode[2] . $zipCode[3] . '-' . $zipCode[4] . $zipCode[5] . $zipCode[6];
        }

        if (Tools::strlen($zipCode) == 6) {
            $zipCode = $zipCode[0] . $zipCode[1] . $zipCode[2] . $zipCode[3] . '-' . $zipCode[4] . $zipCode[5] . '0';
        }

        if (Tools::strlen($zipCode) == 5) {
            $zipCode = $zipCode[0] . $zipCode[1] . $zipCode[2] . $zipCode[3] . '-' . $zipCode[4] . '00';
        }

        if (Tools::strlen($zipCode) == 4) {
            $zipCode = $zipCode . '-' . '000';
        }

        if (Tools::strlen($zipCode) == 3) {
            $zipCode = $zipCode . '0-' . '000';
        }

        if (Tools::strlen($zipCode) == 2) {
            $zipCode = $zipCode . '00-' . '000';
        }

        if (Tools::strlen($zipCode) == 1) {
            $zipCode = $zipCode . '000-' . '000';
        }

        if (Tools::strlen($zipCode) == 0) {
            $zipCode = '1000-100';
        }

        if ($this->verify($zipCode)) {
            return ($zipCode);
        } else {
            #Em último caso, retorna 1000-000
            return ('1000-100');
        }
    }

    #Complemento da verificação do código postal
    private function verify($zipCode)
    {
        $regexp = "/[0-9]{4}\-[0-9]{3}/";
        if (preg_match($regexp, $zipCode)) {
            return (true);
        }

        return (false);
    }

    private function getCountryCode($idCountry)
    {
        $countryRow = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "country WHERE id_country = '" . (int)$idCountry . "'");
        $iso2 = $countryRow['iso_code'];

        if (empty($this->countries)) {
            $this->countries = curl::simple('countries/getAll');
        }

        foreach ($this->countries as $country) {
            if (Tools::strtolower($country['iso_3166_1']) == Tools::strtolower($iso2)) {
                return [$country['country_id'], Tools::strtoupper($country['iso_3166_1'])];
            }
        }

        return [1, 'PT'];
    }

    private function companyMe($companyID = COMPANY)
    {
        $values = [];
        $values['company_id'] = $companyID;
        return curl::simple('companies/getOne', $values, true);
    }

    public function cleanInvoice($order_id)
    {
        Db::getInstance()->insert('moloni_invoices', [
            'order_id' => $order_id,
            'order_total' => '0',
            'invoice_id' => '0',
            'invoice_total' => '0',
            'invoice_date' => '0',
            'invoice_status' => '4',
        ]);

        return [
            'success' => true,
            'message' => 'O documento não vai ser gerado! :)',
            'button' => 'Reverter',
            'url' => $this->genURL('MoloniStart', '&action=cleanAnulate&id_order=' . $order_id)
        ];
    }

    public function cleanInvoiceAnulate($order_id)
    {
        Db::getInstance()->delete('moloni_invoices', 'order_id = ' . (int)$order_id);
        return [
            'success' => true,
            'message' => 'O documento está pronto para ser gerado! :)',
            'button' => '',
            'url' => ''
        ];
    }

    public function makeInvoice($order_id)
    {
        require_once('moloni.documents.php');
        require_once('moloni.settings.php');
        require_once('moloni.products.php');

        $this->settings = new Settings();
        $this->products = new Products();
        $this->me = $this->companyMe();

        $order = [];
        $order['base'] = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "orders WHERE id_order = '" . (int)$order_id . "'");

        if (!$order['base']) {
            MoloniError::create('Encomenda ' . $order_id, "A encomenda com o ID $order_id não foi encontrada", null, null);
            return false;
        }

        $orderPS = new Order($order['base']['id_order']);
        $order['products'] = $orderPS->getOrderDetailList();
        $order['shipping'] = $orderPS->getShipping();

        $order['shipping'][0]['carrier_tax_rate'] = $orderPS->carrier_tax_rate;

        // Handle currency exchanges
        $orderCurrency = new Currency($orderPS->id_currency);

        if ($orderCurrency->iso_code !== 'EUR') {
            $eurCurrency = new Currency(Currency::getIdByIsoCode('EUR'));
            $this->moloniExchangeId = $this->getExchangeRateId($orderCurrency->iso_code);
            $this->moloniExchangeRate = $orderCurrency->conversion_rate;
        }

        $discount = $this->getDiscountPercentage($orderPS);

        $invoice = [];

        $invoice['company_id'] = COMPANY;
        $invoice['date'] = date('d-m-Y');
        $invoice['expiration_date'] = date('d-m-Y');
        $invoice['document_set_id'] = DOCUMENT_SET;

        // Order customer
        $moloniClient = $this->client($order);

        // Set order fiscal zone to be used
        $order['fiscal_zone'] = $this->getFiscalZone($moloniClient);

        $invoice['customer_id'] = $moloniClient['customer_id'];
        $invoice['alternate_address_id'] = (isset($moloniClient['address_id']) ? $moloniClient['address_id'] : '');

        $invoice['our_reference'] = $order['base']['reference'];
        $invoice['your_reference'] = '';

        $invoice['financial_discount'] = ''; #$discount;
        $invoice['special_discount'] = '';

        $invoice['products'] = [];
        $x = 0;

        // Products
        foreach ($order['products'] as $product) {
            if ($this->priceHasTaxIncluded) {
                $product['tax_rate'] = 23;
                $product['unit_price_tax_excl'] /= 1.23;
            }

            $product['moloni_reference'] = Tools::substr($product['product_reference'], 0, 25);

            if (empty($product['moloni_reference'])) {
                $product['moloni_reference'] = 'PS' . mt_rand(10000, 100000);
            }

            $moloniProductId = $this->product($product);
            $moloni_product = $this->products->getOne(['product_id' => $moloniProductId]);

            $invoice['products'][$x]['product_id'] = isset($moloni_product['product_id']) ? $moloni_product['product_id'] : 0;
            $invoice['products'][$x]['name'] = $product['product_name'];
            $invoice['products'][$x]['summary'] = '';
            $invoice['products'][$x]['discount'] = $discount ?: 0;
            $invoice['products'][$x]['qty'] = $product['product_quantity'];

            if ($orderCurrency->iso_code !== 'EUR') {
                $price = $this->convertPriceFull($product['unit_price_tax_excl'], $orderCurrency, $eurCurrency);
            } else {
                $price = $product['unit_price_tax_excl'];
            }

            $invoice['products'][$x]['price'] = $price;
            $invoice['products'][$x]['order'] = $x;

            if ($product['unit_price_tax_incl'] != $product['unit_price_tax_excl']) {
                $taxRate = $this->getOrderProductTax($product);

                $invoice['products'][$x]['taxes'][0]['tax_id'] = $this->settings->taxes->check($taxRate, $order['fiscal_zone']['country_code']);
                $invoice['products'][$x]['taxes'][0]['value'] = $product['unit_price_tax_incl'] - $product['unit_price_tax_excl'];

                if (isset($product['ecotax']) && (float)$product['ecotax'] > 0) {
                    $invoice['products'][$x]['taxes'][1]['tax_id'] = $this->settings->taxes->checkEcotax($product['ecotax'], $order['fiscal_zone']['country_code']);
                    $invoice['products'][$x]['taxes'][1]['value'] = $product['ecotax'];
                    $invoice['products'][$x]['taxes'][1]['order'] = '0';
                    $invoice['products'][$x]['taxes'][1]['cumulative'] = '0';

                    //tax rate must be after ecotax and cumulative
                    $invoice['products'][$x]['taxes'][0]['order'] = '1';
                    $invoice['products'][$x]['taxes'][0]['cumulative'] = '1';

                    //withdraw ecotax from the base value of the product (excluding VAT)
                    $invoice['products'][$x]['price'] -= $product['ecotax'];

                    //the eco rate has to go first
                    $invoice['products'][$x]['taxes'] = array_reverse($invoice['products'][$x]['taxes']);
                }
            } else {
                $invoice['products'][$x]['exemption_reason'] = EXEMPTION_REASON;
            }

            if (!empty($moloni_product) && (int)$moloni_product['composition_type'] === 1 && !empty($moloni_product['child_products'])) {
                // Set the price difference and apply it to the child products
                if ($orderCurrency->iso_code !== 'EUR') {
                    $product['unit_price_tax_excl'] = $this->convertPriceFull($product['unit_price_tax_excl'], $orderCurrency, $eurCurrency);
                }

                $priceScale = ($product['unit_price_tax_excl'] / $moloni_product['price']);

                foreach ($moloni_product['child_products'] as $key => $child) {
                    $moloni_child = curl::simple('products/getOne', ['product_id' => $child['product_child_id']]);

                    $invoice['products'][$x]['child_products'][$key]['product_id'] = $moloni_child['product_id'];
                    $invoice['products'][$x]['child_products'][$key]['name'] = $moloni_child['name'];
                    $invoice['products'][$x]['child_products'][$key]['summary'] = $moloni_child['summary'];
                    $invoice['products'][$x]['child_products'][$key]['discount'] = $discount ?: 0;
                    $invoice['products'][$x]['child_products'][$key]['qty'] = $child['qty'] * $product['product_quantity'];
                    $invoice['products'][$x]['child_products'][$key]['price'] = $child['price'] * $priceScale;
                    $invoice['products'][$x]['child_products'][$key]['order'] = $key;

                    //If billing country is not PT, change child products taxes to match order taxes
                    if (isset($order['fiscal_zone']['country_id']) && (int)$order['fiscal_zone']['country_id'] > 1) {
                        if (isset($invoice['products'][$x]['exemption_reason'])) {
                            //Delete moloni taxes data
                            unset($invoice['products'][$x]['child_products'][$key]['taxes']);

                            //Keep this order defined exemption reason
                            $invoice['products'][$x]['child_products'][$key]['exemption_reason'] = $invoice['products'][$x]['exemption_reason'];
                        } else {
                            //Delete moloni exemption reason data
                            unset($invoice['products'][$x]['child_products'][$key]['exemption_reason']);

                            //Keep this order defined taxes
                            $invoice['products'][$x]['child_products'][$key]['taxes'] = $invoice['products'][$x]['taxes'];
                        }
                    } else {
                        //If billing country is PT, use Moloni defined taxes and/or exemption reason
                        if (!empty($moloni_child['taxes'])) {
                            $invoice['products'][$x]['child_products'][$key]['taxes'] = $moloni_child['taxes'];
                        } else {
                            $invoice['products'][$x]['child_products'][$key]['exemption_reason'] = $moloni_child['exemption_reason'];
                        }
                    }
                }
            }

            $x++;
        }

        // Shipping
        if ($order['base']['total_shipping'] > 0) {
            if ($this->priceHasTaxIncluded) {
                $order['shipping'][0]['carrier_tax_rate'] = 23;
                $order['shipping'][0]['shipping_cost_tax_incl'] /= 1.23;
            }

            $shippingPrice = ($this->freeShipping ? 0 : $order['shipping'][0]['shipping_cost_tax_incl']);
            $shippingPrice = ($order['shipping'][0]['carrier_tax_rate'] > 0 ? ($shippingPrice * 100) / (100 + $order['shipping'][0]['carrier_tax_rate']) : $shippingPrice);

            $invoice['products'][$x]['product_id'] = $this->shipping($order['shipping'][0]);
            $invoice['products'][$x]['name'] = $order['shipping'][0]['carrier_name'];
            $invoice['products'][$x]['summary'] = ($this->freeShipping ? 'Oferta de portes' : '');
            $invoice['products'][$x]['discount'] = ($this->freeShipping ? 100 : 0);
            $invoice['products'][$x]['qty'] = '1';
            $invoice['products'][$x]['order'] = $x;

            if ($orderCurrency->iso_code !== 'EUR') {
                $invoice['products'][$x]['price'] = $this->convertPriceFull($shippingPrice, $orderCurrency, $eurCurrency);
            } else {
                $invoice['products'][$x]['price'] = $shippingPrice;
            }

            if ($order['base']['carrier_tax_rate'] > 0) {
                $invoice['products'][$x]['taxes'][0]['tax_id'] = $this->settings->taxes->check($order['base']['carrier_tax_rate'], $order['fiscal_zone']['country_code']);
                $invoice['products'][$x]['taxes'][0]['value'] = $order['base']['total_shipping_tax_incl'] - $order['base']['total_shipping_tax_excl'];
            } elseif (defined('EXEMPTION_REASON_SHIPPING')) {
                $invoice['products'][$x]['exemption_reason'] = EXEMPTION_REASON_SHIPPING;
            }

            $x++;
        }

        // Wrapping
        if (isset($order['base']['total_wrapping']) && (float)$order['base']['total_wrapping'] > 0) {
            if ($this->priceHasTaxIncluded) {
                $order['base']['total_wrapping_tax_excl'] = $order['base']['total_wrapping_tax_incl'] / 1.23;
            }

            $invoice['products'][$x]['name'] = 'Embrulho';
            $invoice['products'][$x]['summary'] = '';
            $invoice['products'][$x]['discount'] = 0;
            $invoice['products'][$x]['qty'] = 1;

            if ($orderCurrency->iso_code !== 'EUR') {
                $invoice['products'][$x]['price'] = $this->convertPriceFull($order['base']['total_wrapping_tax_excl'], $orderCurrency, $eurCurrency);
            } else {
                $invoice['products'][$x]['price'] = $order['base']['total_wrapping_tax_excl'];
            }

            if ((float)$order['base']['total_wrapping_tax_incl'] !== (float)$order['base']['total_wrapping_tax_excl']) {
                $wrappingTaxValue = $order['base']['total_wrapping_tax_incl'] - $order['base']['total_wrapping_tax_excl'];
                $wrappingTax = round((100 * ($wrappingTaxValue)) / $order['base']['total_wrapping_tax_excl'], 2);

                $invoice['products'][$x]['taxes'][0]['tax_id'] = $this->settings->taxes->check($wrappingTax, $order['fiscal_zone']['country_code']);
                $invoice['products'][$x]['taxes'][0]['value'] = $wrappingTaxValue;
                $invoice['products'][$x]['taxes'][0]['order'] = '0';
                $invoice['products'][$x]['taxes'][0]['cumulative'] = '0';
            } else {
                $invoice['products'][$x]['exemption_reason'] = EXEMPTION_REASON;
            }

            $this->wrapping($invoice['products'][$x]);
        }

        $deliveryMethodId = $this->parseDeliveryMethodId($order['shipping'][0]['carrier_name']);

        if ($deliveryMethodId > 0) {
            $invoice['delivery_method_id'] = $deliveryMethodId;
            $invoice['delivery_datetime'] = date('Y-m-d h:m:s');

            $invoice['delivery_departure_address'] = $this->me['address'];
            $invoice['delivery_departure_city'] = $this->me['city'];
            $invoice['delivery_departure_zip_code'] = $this->me['zip_code'];
            $invoice['delivery_departure_country'] = $this->me['country_id'];

            $invoice['delivery_destination_address'] = $moloniClient['shipping']['address'];
            $invoice['delivery_destination_city'] = $moloniClient['shipping']['delivery_destination_city'];
            $invoice['delivery_destination_zip_code'] = $moloniClient['shipping']['delivery_destination_zip_code'];
            $invoice['delivery_destination_country'] = $moloniClient['shipping']['delivery_destination_country'];
        }

        $orderPayments = $orderPS->getOrderPayments();
        if ($orderPayments && !empty($orderPayments)) {
            $documentPayments = $this->parsePayments($orderPayments);
            if (!empty($documentPayments)) {
                $invoice['payments'] = $documentPayments;
            }
        }

        if ($this->moloniExchangeId > 0) {
            $invoice['exchange_currency_id'] = $this->moloniExchangeId;
            $invoice['exchange_rate'] = $this->moloniExchangeRate;
        }

        if ($this->eac_id) {
            $invoice['eac_id'] = $this->eac_id;
        }

        $invoice['status'] = '0';
        $result = false;

        $invoiceExists = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "moloni_invoices WHERE order_id = '" . (int)$order_id . "'");

        if (!MoloniError::$exists && !$invoiceExists) {

            $documents = new Documents();
            $documentID = $documents->insertInvoice($invoice);

            if (!MoloniError::$exists) {
                $documentInfo = $documents->getOneInfo($documentID);
                if ($documentInfo['net_value'] == $order['base']['total_paid'] ||
                    ($orderCurrency->iso_code !== 'EUR' && $documentInfo['exchange_total_value'] == $order['base']['total_paid'])) {

                    if (defined('DOCUMENT_STATUS') && (int)DOCUMENT_STATUS === 1) {

                        $update = [];

                        $update['document_id'] = $documentID;
                        $update['status'] = 1;
                        $documents->update($update);

                        if (defined('EMAIL_SEND') && EMAIL_SEND) {

                            $invoice = $documents->getOneInfo($documentID);
                            $customerInfo = Db::getInstance()->getRow('SELECT email, firstname, lastname FROM ' . _DB_PREFIX_ . "customer WHERE id_customer = '" . (int)$order['base']['id_customer'] . "'");

                            $to = $customerInfo['email'];
                            $subject = 'Envio de documento | Fatura ' . $invoice['document_set']['name'] . '-' . $invoice['number'] . ' | ' . date('Y-m-d');

                            $downloadurl = $documents->getPDFLink($invoice['document_id']) . '&e=' . urlencode($customerInfo['email']);
                            $date = explode('T', $invoice['date']);
                            $date = $date[0];

                            switch (DOCUMENT_TYPE) {
                                case 'invoices':
                                    $docName = 'Fatura';
                                    break;
                                case 'invoiceReceipts':
                                    $docName = 'Fatura/Recibo';
                                    break;
                                case 'estimates':
                                    $docName = 'Orçamento';
                                    break;
                                case 'purchaseOrder':
                                    $docName = 'Nota de Encomenda';
                                    break;
                            }

                            MailCore::Send(
                                (int)(
                                Configuration::get('PS_LANG_DEFAULT')), 'invoice', $subject, [
                                '{image}' => $this->me['image'],
                                '{nome_empresa}' => $this->me['name'],
                                '{data_hoje}' => date('Y-m-d'),
                                '{nome_cliente}' => $customerInfo['firstname'] . ' ' . $customerInfo['lastname'],
                                '{documento_tipo}' => $docName,
                                '{documento_numero}' => $invoice['document_set']['name'] . '-' . $invoice['number'],
                                '{documento_emissao}' => $date,
                                '{documento_vencimento}' => $date,
                                '{documento_total}' => $invoice['net_value'] . '€',
                                '{documento_url}' => $downloadurl,
                                '{empresa_nome}' => $this->me['name'],
                                '{empresa_morada}' => $this->me['address'],
                                '{empresa_email}' => $this->me['mails_sender_address']
                            ], $to, null, $this->me['mails_sender_name'], $this->me['mails_sender_address'], null, null, _PS_MODULE_DIR_ . 'moloni/mails/'
                            );

                            Db::getInstance()->insert('moloni_invoices', [
                                'order_id' => (int)$order_id,
                                'order_total' => pSQL($order['base']['total_paid']),
                                'invoice_id' => (int)$documentID,
                                'invoice_total' => pSQL($documentInfo['net_value']),
                                'invoice_date' => pSQL(date('Y-m-d H:i:s')),
                                'invoice_status' => (int)'2',
                            ]);
                        } else {
                            Db::getInstance()->insert('moloni_invoices', [
                                'order_id' => (int)$order_id,
                                'order_total' => pSQL($order['base']['total_paid']),
                                'invoice_id' => (int)$documentID,
                                'invoice_total' => pSQL($documentInfo['net_value']),
                                'invoice_date' => pSQL(date('Y-m-d H:i:s')),
                                'invoice_status' => (int)'1',
                            ]);
                        }

                        return [
                            'success' => true,
                            'message' => 'Documento inserido e fechado com sucesso! :)',
                            'button' => 'Ver',
                            'tab' => '_BLANK',
                            'url' => 'https://www.moloni.pt/' . $this->me['slug'] . '/' . $documents->currentType() . '/showDetail/' . $documentID . '/'
                        ];
                    }

                    Db::getInstance()->insert('moloni_invoices', [
                        'order_id' => (int)$order_id,
                        'order_total' => pSQL($order['base']['total_paid']),
                        'invoice_id' => (int)$documentID,
                        'invoice_total' => pSQL($documentInfo['net_value']),
                        'invoice_date' => pSQL(date('Y-m-d H:i:s')),
                        'invoice_status' => (int)'0',
                    ]);
                    return [
                        'success' => true,
                        'message' => 'Documento inserido como rascunho! :)',
                        'button' => 'Ver',
                        'tab' => '_BLANK',
                        'url' => 'https://www.moloni.pt/' . $this->me['slug'] . '/' . $documents->currentType() . '/showDetail/' . $documentID . '/'
                    ];
                }

                Db::getInstance()->insert('moloni_invoices', [
                    'order_id' => (int)$order_id,
                    'order_total' => pSQL($order['base']['total_paid']),
                    'invoice_id' => (int)$documentID,
                    'invoice_total' => pSQL($documentInfo['net_value']),
                    'invoice_date' => pSQL(date('Y-m-d H:i:s')),
                    'invoice_status' => (int)'3',
                ]);
                MoloniError::create('document/update', 'Documento inserido, mas totais não correspondem', $documentInfo, $order);
            }
            return $result;
        }

        return false;
    }

    private function parsePayments($payments)
    {
        $paymentMethods = [];
        if (!empty($payments) && is_array($payments)) {
            foreach ($payments as $payment) {
                $paymentMethods[] = $this->parsePaymentMethod($payment);
            }
        }

        return $paymentMethods;
    }

    /**
     * @param OrderPayment $payment
     * @return array
     */
    private function parsePaymentMethod($payment)
    {
        $paymentMethod = [
            'value' => $payment->amount,
            'date' => date('Y-m-d')
        ];

        if (!empty($payment->conversion_rate)) {
            $paymentMethod['value'] /= $payment->conversion_rate;
        }

        $companyPaymentMethods = $this->settings->paymentMethods->getAll();

        foreach ($companyPaymentMethods as $companyPaymentMethod) {
            if (strcasecmp($companyPaymentMethod['name'], $payment->payment_method) === 0) {
                $paymentMethod['payment_method_id'] = $companyPaymentMethod['payment_method_id'];
            }
        }

        if (!isset($paymentMethod['payment_method_id'])) {
            $inserted = $this->settings->paymentMethods->insert(['name' => $payment->payment_method]);

            if (!isset($inserted['payment_method_id'])) {
                return [];
            }

            $paymentMethod['payment_method_id'] = $inserted['payment_method_id'];
        }

        return $paymentMethod;
    }

    /**
     * @param $deliveryMethodName
     * @return int
     */
    private function parseDeliveryMethodId($deliveryMethodName)
    {
        $deliveryMethodId = 0;

        if (!empty($deliveryMethodName)) {
            $deliveryMethods = $this->settings->deliveryMethods->getAll();

            if (!empty($deliveryMethods) && is_array($deliveryMethods)) {
                foreach ($deliveryMethods as $deliveryMethod) {
                    if (strcasecmp($deliveryMethod['name'], $deliveryMethodName) === 0) {
                        $deliveryMethodId = $deliveryMethod['delivery_method_id'];
                    }
                }
            }

            if ($deliveryMethodId === 0) {
                $inserted = $this->settings->deliveryMethods->insert(['name' => $deliveryMethodName]);
                $deliveryMethodId = (int)$inserted['delivery_method_id'];
            }
        }

        return $deliveryMethodId;
    }

    private function getFiscalZone($client)
    {
        $countryCode = '';
        $countryId = 1;
        $setting = 'billing';

        if (defined('FISCAL_ZONE_BASED_ON')) {
            $setting = FISCAL_ZONE_BASED_ON;
        }

        switch ($setting) {
            case 'billing':
                if (isset($client['billing_country_id'], $client['billing_country_code'])) {
                    $countryCode = $client['billing_country_code'];
                    $countryId = $client['billing_country_id'];
                }

                break;
            case 'shipping':
                if (isset($client['shipping_country_id'], $client['shipping_country_code'])) {
                    $countryCode = $client['shipping_country_code'];
                    $countryId = $client['shipping_country_id'];
                }

                break;
            case 'company':
                if (isset($this->me['country']['iso_3166_1'])) {
                    $countryCode = $this->me['country']['iso_3166_1'];
                    $countryId = $this->me['country']['country_id'];
                }

                break;
        }

        if (empty($countryCode)) {
            if (isset($this->me['country']['iso_3166_1'])) {
                $countryCode = $this->me['country']['iso_3166_1'];
                $countryId = $this->me['country']['country_id'];
            } else {
                $countryCode = 'PT';
                $countryId = 1;
            }
        }

        return [
            'country_id' => (int)$countryId,
            'country_code' => strtoupper($countryCode),
        ];
    }

    private function getOrderProductTax($product)
    {
        $taxValue = (float)$product['tax_rate'];

        if ((int)$taxValue === 0) {
            $taxValue = (100 * ($product['unit_price_tax_incl'] - $product['unit_price_tax_excl'])) / $product['unit_price_tax_excl'];
            $taxValue = round($taxValue, 2);
        }

        return $taxValue;
    }

    private function client($order)
    {
        require_once('moloni.entities.php');

        $this->entities = new Entities();
        $customer = [];

        $customer['base'] = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "customer WHERE id_customer = '" . (int)$order['base']['id_customer'] . "'");
        $customer['address']['invoice'] = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "address WHERE id_address = '" . (int)$order['base']['id_address_invoice'] . "'");
        $customer['address']['delivery'] = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "address WHERE id_address = '" . (int)$order['base']['id_address_delivery'] . "'");

        list($countryCodeId, $countryCode) = $this->getCountryCode($customer['address']['invoice']['id_country']);

        $vat = '999999990';

        $firstVatNumber = $customer['address']['invoice']['vat_number'];
        if (!empty($this->clean($firstVatNumber))) {
            $vat = $firstVatNumber;
        }

        if ($vat === '999999990') {
            $secondVatNumber = $customer['address']['invoice']['dni'];
            if (!empty($this->clean($secondVatNumber))) {
                $vat = $secondVatNumber;
            }
        }

        $vat = $this->vatCheck($vat);

        $updateCustomer = true;
        if ((string)$vat === '999999990') {
            if (!empty($customer['base']['email'])) {
                $clientExists = $this->entities->customers->getByEmail($customer['base']['email']);
            } else {
                $clientExists = $this->entities->customers->getByReference('9999');
                $updateCustomer = false;
            }
        } else {
            $clientExists = $this->entities->customers->getByVat($vat);
        }

        $companyName = $this->clean(trim($customer['address']['invoice']['company']));

        $name = (empty($companyName)) ?
            $customer['address']['invoice']['firstname'] . ' ' . $customer['address']['invoice']['lastname'] :
            $customer['address']['invoice']['company'];

        $MoloniCustomer = [];

        $MoloniCustomer['name'] = $name;
        $MoloniCustomer['email'] = $customer['base']['email'];

        switch (true) {
            case !empty($customer['address']['invoice']['phone']) && !empty($customer['address']['invoice']['phone_mobile']):
                $phoneNumber = $customer['address']['invoice']['phone'];
                $contactPhoneNumber = $customer['address']['invoice']['phone_mobile'];

                break;
            case !empty($customer['address']['invoice']['phone']):
                $phoneNumber = $customer['address']['invoice']['phone'];
                $contactPhoneNumber = $customer['address']['invoice']['phone'];

                break;
            case !empty($customer['address']['invoice']['phone_mobile']):
                $phoneNumber = $customer['address']['invoice']['phone_mobile'];
                $contactPhoneNumber = $customer['address']['invoice']['phone_mobile'];

                break;
            default:
                $phoneNumber = '';
                $contactPhoneNumber = '';

                break;
        }

        $MoloniCustomer['phone'] = $phoneNumber;
        $MoloniCustomer['contact_phone'] = $contactPhoneNumber;
        $MoloniCustomer['address'] = $customer['address']['invoice']['address1'] . (empty($customer['address']['invoice']['address2']) ? '' : ' - ' . $customer['address']['invoice']['address2']);
        $MoloniCustomer['zip_code'] = ((int)$countryCodeId === 1 ? $this->zipCheck($customer['address']['invoice']['postcode']) : $customer['address']['invoice']['postcode']);
        $MoloniCustomer['city'] = $customer['address']['invoice']['city'];

        $MoloniCustomer['country_id'] = $countryCodeId;

        if (in_array($countryCodeId, [1, 33, 8])) {
            $MoloniCustomer['language_id'] = 1;
        } elseif ((int)$countryCodeId === 70) {
            $MoloniCustomer['language_id'] = 3;
        } else {
            $MoloniCustomer['language_id'] = 2;
        }

        if (!empty($customer['address']['invoice']['company'])) {
            $MoloniCustomer['contact_name'] = $customer['address']['invoice']['firstname'] . ' ' . $customer['address']['invoice']['lastname'];
            $MoloniCustomer['contact_email'] = $customer['base']['email'];
        }

        $MoloniCustomer['maturity_date_id'] = 0;
        $MoloniCustomer['payment_method_id'] = 0;
        $MoloniCustomer['delivery_method_id'] = 0;
        $MoloniCustomer['copies'] = $this->me['copies'];

        $MoloniCustomer['salesman_id'] = '0';
        $MoloniCustomer['payment_day'] = '0';
        $MoloniCustomer['discount'] = '0';
        $MoloniCustomer['credit_limit'] = '0';

        if ($clientExists) {
            $MoloniCustomer['customer_id'] = $clientExists['customer_id'];

            $return = [];
            if ($updateCustomer) {
                $return['customer_id'] = $this->entities->customers->update($MoloniCustomer);
            } else {
                $return['customer_id'] = $clientExists['customer_id'];
            }
        } else {
            $MoloniCustomer['vat'] = $vat;
            $MoloniCustomer['number'] = $this->entities->customers->getNextNumber();

            $return = [];
            $return['customer_id'] = $this->entities->customers->insert($MoloniCustomer);
        }

        $return['billing_country_id'] = $countryCodeId;
        $return['billing_country_code'] = $countryCode;

        list($countryCodeId, $countryCode) = $this->getCountryCode($customer['address']['delivery']['id_country']);

        $return['shipping']['address'] = $customer['address']['delivery']['address1'] . (empty($customer['address']['delivery']['address2']) ? '' : ' - ' . $customer['address']['delivery']['address2']);
        $return['shipping']['delivery_destination_city'] = $customer['address']['delivery']['city'];
        $return['shipping']['delivery_destination_zip_code'] = ($countryCodeId == '1' ? $this->zipCheck($customer['address']['delivery']['postcode']) : $customer['address']['delivery']['postcode']);
        $return['shipping']['delivery_destination_country'] = $countryCodeId;

        $return['shipping_country_id'] = $countryCodeId;
        $return['shipping_country_code'] = $countryCode;

        return $return;
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function clean($string)
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    /**
     * @param array $input
     *
     * @return false|mixed
     */
    private function product($input)
    {
        $reference = $input['moloni_reference'];
        $productExists = $this->products->getByReference($reference);

        if ($productExists) {
            $productID = $productExists['product_id'];
        } else {

            $productPS = new Product($input['product_id'], 1, Configuration::get('PS_LANG_DEFAULT'));
            $categoryPS = new Category((int)$productPS->id_category_default);

            $taxRate = $this->getOrderProductTax($input);

            $product = [];

            $categoryName = !empty($categoryPS->getName()) ? $categoryPS->getName() : 'Loja Online';
            $categoryId = $this->products->categories->check($categoryName);

            if (empty($categoryId)) {
                $categoryId = $this->products->categories->check('Loja Online');
            }

            $product['category_id'] = $categoryId;
            $product['type'] = '1';
            $product['name'] = !empty($productPS->name) ? $productPS->name : 'Artigo';
            $product['summary'] = $this->formDescription($this->getAttributes($input['product_attribute_id']));
            $product['reference'] = $reference;
            $product['ean'] = $productPS->ean13;
            $product['price'] = $input['unit_price_tax_excl'];
            $product['unit_id'] = defined('MEASURE_UNIT') ? MEASURE_UNIT : 0;
            $product['has_stock'] = (defined('AT_CATEGORY') && AT_CATEGORY == 'SS') ? '0' : '1';
            $product['stock'] = $this->getStock($input['id_product'], $input['product_attribute_id'], $input['product_quantity']);
            $product['pos_favorite'] = '0';
            $product['at_product_category'] = defined('AT_CATEGORY') ? AT_CATEGORY : 'M';

            if ($taxRate == 0) {
                $product['exemption_reason'] = defined('EXEMPTION_REASON') ? EXEMPTION_REASON : '';
            } else {
                $product['taxes'][0]['tax_id'] = $this->settings->taxes->check($taxRate);
                $product['taxes'][0]['value'] = $input['unit_price_tax_incl'] - $input['unit_price_tax_excl'];
                $product['taxes'][0]['order'] = '0';
                $product['taxes'][0]['cumulative'] = '0';

                if ((float)$productPS->ecotax > 0) {
                    $product['taxes'][1]['tax_id'] = $this->settings->taxes->checkEcotax($productPS->ecotax);
                    $product['taxes'][1]['value'] = $productPS->ecotax;
                    $product['taxes'][1]['order'] = '0';
                    $product['taxes'][1]['cumulative'] = '0';

                    //taxa tem de ser depois da ecotaxa e cumulativa
                    $product['taxes'][0]['order'] = '1';
                    $product['taxes'][0]['cumulative'] = '1';

                    //retirar ecotaxa ao valor base do produto (sem iva)
                    $product['price'] -= $productPS->ecotax;

                    //a eco taxa tem de ir em primeiro lugar
                    $product['taxes'] = array_reverse($product['taxes']);
                }
            }

            $productID = $this->products->insert($product);
        }

        return $productID;
    }

    private function shipping($input)
    {

        $reference = '';

        $shippingName = $input['carrier_name'] ? substr($input['carrier_name'], 0, 28) : 'PORTES';


        $helper = preg_replace('/([^A-Z.])\w+/i', '', str_replace(' ', '.', $shippingName));
        $helper = explode('.', $helper);
        foreach ($helper as $word) {
            $reference .= Tools::substr($word, 0, 3) . '.';
        }

        $reference = Tools::strtoupper(trim($reference, '.'));

        if (trim($reference) === '') {
            $reference = 'PORTES';
        }

        $productExists = $this->products->getByReference($reference);

        if ($productExists) {
            $productID = $productExists['product_id'];
        } else {

            $taxRate = $input['carrier_tax_rate'];

            $product = [];
            $product['category_id'] = $this->products->categories->check('Portes');
            $product['type'] = '2';
            $product['name'] = $input['carrier_name'];
            $product['summary'] = ($input['tracking_number'] <> '') ? 'Tracking: ' . $input['tracking_number'] : '';
            $product['reference'] = $reference;
            $product['ean'] = '';
            $product['price'] = $input['shipping_cost_tax_excl'];
            $product['unit_id'] = defined('MEASURE_UNIT') ? MEASURE_UNIT : '';
            $product['has_stock'] = '0';

            if ($taxRate == 0) {
                $product['exemption_reason'] = defined('EXEMPTION_REASON_SHIPPING') ? EXEMPTION_REASON_SHIPPING : 0;
            } else {
                $product['taxes'][0]['tax_id'] = $this->settings->taxes->check($taxRate);
                $product['taxes'][0]['value'] = $input['shipping_cost_tax_incl'] - $input['shipping_cost_tax_excl'];
                $product['taxes'][0]['order'] = '0';
                $product['taxes'][0]['cumulative'] = '0';
            }

            $productID = $this->products->insert($product);
        }

        return $productID;
    }

    private function wrapping(&$wrapperProduct)
    {
        $wrapperReference = 'EMBRULHO';

        $productExists = $this->products->getByReference($wrapperReference);

        if ($productExists) {
            $wrapperId = $productExists['product_id'];
        } else {
            $wrapperProduct['category_id'] = $this->products->categories->check('Embrulho');
            $wrapperProduct['type'] = '1';
            $wrapperProduct['reference'] = $wrapperReference;
            $wrapperProduct['unit_id'] = defined('MEASURE_UNIT') ? MEASURE_UNIT : '';
            $wrapperProduct['has_stock'] = '0';

            $wrapperId = $this->products->insert($wrapperProduct);
        }

        $wrapperProduct['product_id'] = $wrapperId;
    }

    private function getAttributes($productAttribute)
    {

        if ($results = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . "product_attribute_combination WHERE id_product_attribute = '" . (int)$productAttribute . "'")) {
            $return = array();
            foreach ($results as $attribute) {
                $return[] = array_merge(
                    Db::getInstance()->getRow('SELECT name AS value FROM ' . _DB_PREFIX_ . "attribute_lang WHERE id_attribute = '" . (int)$attribute['id_attribute'] . "' and id_lang = '" . (int)Configuration::get('PS_LANG_DEFAULT') . "'"), Db::getInstance()->getRow('SELECT ' . _DB_PREFIX_ . 'attribute_group_lang.name AS name FROM ' . _DB_PREFIX_ . 'attribute_group_lang JOIN ' . _DB_PREFIX_ . 'attribute ON ' . _DB_PREFIX_ . 'attribute_group_lang.id_attribute_group = ' . _DB_PREFIX_ . 'attribute.id_attribute_group WHERE ' . _DB_PREFIX_ . "attribute.id_attribute = '" . (int)$attribute['id_attribute'] . "' AND " . _DB_PREFIX_ . "attribute_group_lang.id_lang = '" . (int)Configuration::get('PS_LANG_DEFAULT') . "'")
                );
            }
            return $return;
        }

        return false;
    }

    private function formDescription($attributes = false)
    {
        if ($attributes) {
            $description = '';
            foreach ($attributes as $attribute) {
                $description .= $attribute['name'] . ': ' . $attribute['value'] . "\n";
            }

            return $description;
        }

        return false;
    }

    private function getStock($productID, $attributeID = false, $qty = 0)
    {
        $attributeID = ($attributeID == false) ? '0' : $attributeID;
        $stock = Db::getInstance()->getRow('SELECT quantity AS qty FROM ' . _DB_PREFIX_ . "stock_available WHERE id_product = '" . (int)$productID . "' and id_product_attribute = '" . (int)$attributeID . "'");
        $total = $stock['qty'] + $qty;

        return ($total < 0) ? '0' : $total;
    }

    public function getDocumentsAll()
    {
        require_once('moloni.documents.php');
        $documents = new Documents();
        $allDocs = $documents->getAll(false);
        $this->me = $this->companyMe();

        $return = array();
        if (is_array($allDocs)) {
            foreach ($allDocs as $document) {

                $date = explode('T', $document['date']);
                $date = $date[0];
                $moloniURL = ($documents->currentType($document['document_type']['saft_code'])) ? 'https://www.moloni.pt/' . $this->me['slug'] . '/' . $documents->currentType($document['document_type']['saft_code']) . '/showDetail/' . $document['document_id'] . '/' : false;
                $return[] = array(
                    'document_id' => $document['document_id'],
                    'document_type' => $document['document_type']['saft_code'],
                    'customer_id' => $document['customer_id'],
                    'document_set_name' => $document['document_set_name'],
                    'number' => $document['number'],
                    'date' => $date,
                    'our_reference' => $document['our_reference'],
                    'entity_name' => $document['entity_name'],
                    'entity_vat' => $document['entity_vat'],
                    'entity_address' => $document['entity_address'],
                    'net_value' => $document['net_value'],
                    'download' => ($document['number'] > 0) ? $documents->getPDFLink($document['document_id']) : '',
                    'check' => $moloniURL
                );
            }
        }

        return $return;
    }

    public function productCreate($input)
    {
        $productID = $input['id_product'];
        $error = false;

        $reference = Tools::substr($input['product']->reference, 0, 25);
        if (empty($reference)) {
            $error = true;
        }

        $this->products = new Products();
        $this->settings = new Settings();
        $productExists = $this->products->getByReference($reference);

        $productPS = new Product($productID, 1, Configuration::get('PS_LANG_DEFAULT'));
        $categoryPS = new Category((int)$productPS->id_category_default);
        $taxRate = $productPS->tax_rate;

        $query = 'SELECT * FROM ' . _DB_PREFIX_ . "product_attribute WHERE
        id_product = '" . (int)$productID . "'";
        $attributes = Db::getInstance()->ExecuteS($query);

        if (!$productExists && !$error && count($attributes) == 0) {

            $product = array();

            $product['category_id'] = $this->products->categories->check($categoryPS->getName());
            $product['type'] = '1';
            $product['name'] = $productPS->name;
            $product['summary'] = strip_tags($productPS->description_short);
            $product['reference'] = $reference;
            $product['ean'] = $productPS->ean13;
            $product['price'] = $productPS->price;
            $product['unit_id'] = MEASURE_UNIT;
            $product['has_stock'] = (AT_CATEGORY == 'SS') ? '0' : '1';
            $product['stock'] = $productPS->quantity;
            $product['pos_favorite'] = '0';
            $product['at_product_category'] = AT_CATEGORY;

            print_r($productPS);
            print_r(get_class_methods($productPS));

            if ($taxRate == 0) {
                $product['exemption_reason'] = EXEMPTION_REASON;
            } else {
                $product['taxes'][0]['tax_id'] = $this->settings->taxes->check($taxRate);
                $product['taxes'][0]['value'] = ($productPS->price * $taxRate) / 100;
                $product['taxes'][0]['order'] = '0';
                $product['taxes'][0]['cumulative'] = '0';

                if ((float)$productPS->ecotax > 0) {
                    $product['taxes'][1]['tax_id'] = $this->settings->taxes->checkEcotax($productPS->ecotax);
                    $product['taxes'][1]['value'] = $productPS->ecotax;
                    $product['taxes'][1]['order'] = '0';
                    $product['taxes'][1]['cumulative'] = '0';

                    //taxa tem de ser depois da ecotaxa e cumulativa
                    $product['taxes'][0]['order'] = '1';
                    $product['taxes'][0]['cumulative'] = '1';

                    //retirar ecotaxa ao valor base do produto (sem iva)
                    $product['price'] -= $productPS->ecotax;

                    //a eco taxa tem de ir em primeiro lugar
                    $product['taxes'] = array_reverse($product['taxes']);
                }
            }

            $productID = $this->products->insert($product);
            unset($product);
        }

        unset($attributes);

        if ($attributes = Db::getInstance()->ExecuteS($query)) {
            foreach ($attributes as $attribute) {

                $reference = Tools::substr($attribute['reference'], 0, 25);
                if (!empty($reference)) {

                    $productExists = $this->products->getByReference($reference);

                    if (!$productExists) {
                        $product = [];

                        $product['category_id'] = $this->products->categories->check($categoryPS->getName());
                        $product['type'] = '1';
                        $product['name'] = $productPS->name;
                        $product['summary'] = $this->formDescription($this->getAttributes($attribute['id_product_attribute']));
                        $product['reference'] = $reference;
                        $product['ean'] = $attribute['ean13'] ?: '';
                        $product['price'] = $productPS->price;
                        $product['unit_id'] = MEASURE_UNIT;
                        $product['has_stock'] = (AT_CATEGORY === 'SS') ? '0' : '1';
                        $product['stock'] = $productPS->quantity;
                        $product['pos_favorite'] = '0';
                        $product['at_product_category'] = AT_CATEGORY;

                        if ($taxRate == 0) {
                            $product['exemption_reason'] = EXEMPTION_REASON;
                        } else {
                            $product['taxes'][0]['tax_id'] = $this->settings->taxes->check($taxRate);
                            $product['taxes'][0]['value'] = ($productPS->price * $taxRate) / 100;
                            $product['taxes'][0]['order'] = '0';
                            $product['taxes'][0]['cumulative'] = '0';

                            if ((float)$productPS->ecotax > 0) {
                                $product['taxes'][1]['tax_id'] = $this->settings->taxes->checkEcotax($productPS->ecotax);
                                $product['taxes'][1]['value'] = $productPS->ecotax;
                                $product['taxes'][1]['order'] = '0';
                                $product['taxes'][1]['cumulative'] = '0';

                                //taxa tem de ser depois da ecotaxa e cumulativa
                                $product['taxes'][0]['order'] = '1';
                                $product['taxes'][0]['cumulative'] = '1';

                                //retirar ecotaxa ao valor base do produto (sem iva)
                                $product['price'] -= $productPS->ecotax;

                                //a eco taxa tem de ir em primeiro lugar
                                $product['taxes'] = array_reverse($product['taxes']);
                            }
                        }

                        $this->products->insert($product);
                        unset($product);
                    }
                }
            }
        }

        if (MoloniError::$exists) {
            print_r(MoloniError::$message);
        }
    }

    //***** Copied from AdminImporterController and modified ******//
    public static function saveImageFromUrl($id_entity, $id_image = null, $url = '')
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');

        $image_obj = new Image($id_image);
        $path = $image_obj->getPathForCreation();


        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/' . implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_ . 'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parced_url);

        $orig_tmpfile = $tmpfile;

        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);
                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
            try {
                $images_types = ImageType::getImagesTypes('products', true);
            } catch (PrestaShopDatabaseException $e) {
                return false;
            }

            $previous_path = null;
            $path_infos = array();
            $path_infos[] = array($tgt_width, $tgt_height, $path . '.jpg');

            if (!empty($images_types) && is_array($images_types)) {
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize(
                        $tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height
                    )) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array($tgt_width, $tgt_height, $path . '-' . stripslashes($image_type['name']) . '.jpg');
                        }
                        if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '.jpg')) {
                            unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '.jpg');
                        }
                        if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '_' . (int)Context::getContext()->shop->id . '.jpg')) {
                            unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '_' . (int)Context::getContext()->shop->id . '.jpg');
                        }
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);
            return false;
        }
        unlink($orig_tmpfile);
        return true;
    }

    protected static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }
        return $path;
    }

    private function getExchangeRateId($from, $to = 'EUR')
    {
        $currencies = $this->products->currenciesGetAll();
        foreach ($currencies as $currency) {
            if ($currency['name'] == strtoupper($to . '/' . $from)) {
                return $currency['to'];
            }
        }
        return 0;
    }

    //***** Copied from AdminImporterController and modified ******//
    private function getDiscountPercentage($order)
    {
        $cartRules = $order->getDiscounts();
        $discount = 0;
        $discountTotal = 0;

        if (!empty($cartRules) && is_array($cartRules)) {
            if (count($cartRules) == 1) {
                $singleCartRule = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . "cart_rule WHERE id_cart_rule = '" . (int)$cartRules[0]['id_cart_rule'] . "'");
                if (!empty($singleCartRule) && is_array($singleCartRule)) {
                    if ($singleCartRule['free_shipping'] == 1) {
                        $this->freeShipping = true;
                    } elseif ($singleCartRule['reduction_percent'] > 0) {
                        $discount = $singleCartRule['reduction_percent'];
                    } else {
                        $discountTotal = $this->getCartRulesTotal($cartRules);
                    }
                }
            } else {
                $discountTotal = $this->getCartRulesTotal($cartRules);
            }
        }

        if ($discountTotal != '0') {
            $discount = round(($discountTotal * 100) / $order->total_products, 5);
            $discount = ($discount > 100) ? 100 : $discount;
        }

        return $discount;
    }

    private function getCartRulesTotal(array $cartRules)
    {
        $discountTotal = 0;
        foreach ($cartRules as $rule) {
            if ($rule['free_shipping'] == 1) {
                $this->freeShipping = true;
            } else {
                $discountTotal = $discountTotal + $rule['value_tax_excl'];
            }
        }
        return $discountTotal;
    }

    private function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null)
    {
        if ($currency_from === $currency_to) {
            return $amount;
        }

        if ($currency_from === null) {
            $currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_to === null) {
            $currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
            $amount *= $currency_to->conversion_rate;
        } else {
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
            // Convert amount to default currency (using the old currency rate)
            $amount /= $conversion_rate;
            // Convert to new currency
            $amount *= $currency_to->conversion_rate;
        }

        return Tools::ps_round($amount, 5);
    }

}
