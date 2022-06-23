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
class Customers extends entities
{

    public function __construct()
    {
        
    }

    public function number($companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/count");
        return($result);
    }

    public function getAll($companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/getAll");
        return($result);
    }

    public function getOne($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/getOne");
        return($result);
    }

    public function countBySearch($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/countBySearch");
        return($result);
    }

    public function getBySearch($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/getBySearch");
        return($result);
    }

    public function countByVat($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/countByVat");
        return($result);
    }

    public function getByVat($vat, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['exact'] = "1";
        $values['vat'] = $vat;
        $result = curl::simple("customers/getByVat", $values, true);
        return (isset($result[0]['customer_id']) ? $result[0] : false);
    }

    public function getByReference($reference, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['exact'] = "1";
        $values['number'] = $reference;
        $result = curl::simple("customers/getByNumber", $values, true);
        return (isset($result[0]['customer_id']) ? $result[0] : false);
    }

    public function getByEmail($email, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['exact'] = "1";
        $values['email'] = $email;
        $result = curl::simple("customers/getByEmail", $values, true);
        return (isset($result[0]['customer_id']) ? $result[0] : false);
    }

    public function countByNumber($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/countByNumber");
        return($result);
    }

    public function countByName($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/countByName");
        return($result);
    }

    public function getByName($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/getByName");
        return($result);
    }

    public function getLastNumber($companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/getLastNumber", $values);

        return($result['number']);
    }
    
    public function getNextNumber($companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/getNextNumber", $values);

        return($result['number']);
    }

    public function insert($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/insert", $values, true);

        if (isset($result['customer_id'])) {
            return($result['customer_id']);
        } else {
            MoloniError::create("customers/insert", 'Error inserting client', $values, $result);
        }
    }

    public function update($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/update", $values, true);
        if (isset($result['customer_id'])) {
            return($result['customer_id']);
        } else {
            MoloniError::create("customers/update", 'Error updating client', $values, $result);
        }
    }

    public function delete($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("customers/delete");
        return($result);
    }
}
