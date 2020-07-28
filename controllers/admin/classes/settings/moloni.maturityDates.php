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
 *  @author    Nuno Almeida
 *  @copyright Nuno Almeida
 *  @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 */

class MaturityDates extends settings
{

    public function __construct()
    {

    }

    public function getAll($companyID = COMPANY)
    {
        $values = array("company_id" => $companyID);
        $result = curl::simple("maturityDates/getAll", $values);
        return($result);
    }

    public function insert($values, $companyID = COMPANY)
    {
        $values = array("company_id" => $companyID);
        $result = curl::simple("maturityDates/insert", $values);
        return($result);
    }

    public function update($values, $companyID = COMPANY)
    {
        $values = array("company_id" => $companyID);
        $result = curl::simple("maturityDates/update", $values);
        return($result);
    }

    public function delete($values, $companyID = COMPANY)
    {
        $values = array("company_id" => $companyID);
        $result = curl::simple("maturityDates/delete", $values);
        return($result);
    }
}
