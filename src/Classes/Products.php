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

namespace Moloni\Classes;

use ModuleAdminController;
use Moloni\Classes\Products\Categories;

class Products extends ModuleAdminController
{

    public function __construct()
    {
        $this->categories = new Categories();
    }

    public function getCount($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/count', $values);
        return ($result);
    }

    public function getAll($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/getAll', $values);
        return ($result);
    }

    public function getOne($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/getOne', $values);
        return ($result);
    }

    public function countBySearch($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/countBySearch', $values);
        return ($result);
    }

    public function getBySearch($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/getBySearch', $values);
        return ($result);
    }

    public function countByName($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/countByName', $values);
        return ($result);
    }

    public function getByName($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/getByName', $values);
        return ($result);
    }

    public function countByReference($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/countByReference', $values);
        return ($result);
    }

    public function getByReference($reference)
    {
        $values = array();
        $values['company_id'] = COMPANY;
        $values['reference'] = $reference;
        $values['exact'] = '1';
        $result = Curl::simple('products/getByReference', $values);

        return ((count($result) > 0) ? $result[0] : false);
    }

    public function countByEAN($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/countByEAN', $values);
        return ($result);
    }

    public function getByEAN($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/getByEAN', $values);
        return ($result);
    }

    public function countModifiedSince($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/countModifiedSince', $values);
        return ($result);
    }

    public function getModifiedSince($values, $offset = 0, $stop = 0)
    {
        $values['company_id'] = COMPANY;
        $values['offset'] = $offset;
        $result = Curl::simple('products/getModifiedSince', $values);
        $counter = count($result);
        while ($counter == 50) {
            $values['offset'] += 50;
            $result2 = Curl::simple('products/getModifiedSince', $values);
            $result = array_merge($result, $result2);
            $counter = count($result2);
            unset($result2);
        }

        return ($result);
    }

    public function insert($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/insert', $values, true);
        if (isset($result['product_id'])) {
            return ($result['product_id']);
        } else {
            MoloniError::create('products/insert', ('Error inserting product'), $values, $result);
            return (false);
        }
    }

    public function update($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/insert', $values);
        return ($result);
    }

    public function delete($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('products/insert', $values);
        return ($result);
    }

    public function currenciesGetAll()
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple('currencyExchange/getAll', $values);
        return ($result);
    }
}