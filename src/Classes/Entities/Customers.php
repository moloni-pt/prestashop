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

namespace Moloni\Classes\Entities;

use Moloni\Classes\Curl;
use Moloni\Classes\Entities;
use Moloni\Classes\MoloniError;
use Moloni\Facades\ModuleFacade;
use Moloni\Traits\ClassTrait;

class Customers extends Entities
{
    use ClassTrait;

    public function __construct()
    {

    }

    public function number($companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/count");
        return ($result);
    }

    public function getAll($companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/getAll");
        return ($result);
    }

    public function getOne($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/getOne");
        return ($result);
    }

    public function countBySearch($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/countBySearch");
        return ($result);
    }

    public function getBySearch($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/getBySearch");
        return ($result);
    }

    public function countByVat($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/countByVat");
        return ($result);
    }

    public function getByVat($vat, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['exact'] = "1";
        $values['vat'] = $vat;
        $result = Curl::simple("customers/getByVat", $values, true);
        return (isset($result[0]['customer_id']) ? $result[0] : false);
    }

    public function getByReference($reference, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['exact'] = "1";
        $values['number'] = $reference;
        $result = Curl::simple("customers/getByNumber", $values, true);
        return (isset($result[0]['customer_id']) ? $result[0] : false);
    }

    public function getByEmail($email, $companyID = COMPANY)
    {
        $values = [];
        $values['company_id'] = $companyID;
        $values['email'] = $email;

        $result = Curl::simple("customers/getByEmail", $values, true);

        if (is_array($result) && !empty($result)) {
            foreach ($result as $customer) {
                if (!isset($customer['customer_id']) || !isset($customer['vat'])) {
                    continue;
                }

                if ($customer['vat'] === '999999990') {
                    return $customer;
                }
            }
        }

        return false;
    }

    public function countByNumber($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/countByNumber");
        return ($result);
    }

    public function countByName($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/countByName");
        return ($result);
    }

    public function getByName($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/getByName");
        return ($result);
    }

    public function getLastNumber($companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/getLastNumber", $values);

        return ($result['number']);
    }

    public function getNextNumber($companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/getNextNumber", $values);

        return ($result['number']);
    }

    public function insert($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/insert", $values, true);

        if (isset($result['customer_id'])) {
            return ($result['customer_id']);
        } else {
            $message = ModuleFacade::getModule()->l('Error inserting client', $this->className());

            MoloniError::create("customers/insert", $message, $values, $result);
        }
    }

    public function update($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/update", $values, true);

        if (isset($result['customer_id'])) {
            return ($result['customer_id']);
        } else {
            $message = ModuleFacade::getModule()->l('Error updating client', $this->className());

            MoloniError::create("customers/update", $message, $values, $result);
        }
    }

    public function delete($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("customers/delete");
        return ($result);
    }
}
