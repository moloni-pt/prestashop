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

class Taxes extends settings
{

    public function __construct()
    {

    }

    public function check($rate)
    {

        $taxes = $this->getAll();
        
        foreach ($taxes as $tax) {
            if (round($rate, 2) == round($tax['value'], 2)) {
                return $tax['tax_id'];
            }
        }
        
        
        foreach ($taxes as $tax) {
            if (round($rate) == round($tax['value'])) {
                return $tax['tax_id'];
            }
        }        

        $values                      = array();
        $values['name']              = "Taxa ".$rate;
        $values['value']             = $rate;
        $values['type']              = "1";
        $values['saft_type']         = "1";
        $values['vat_type']          = "OUT";
        $values['stamp_tax']         = "0";
        $values['exemption_reason']  = EXEMPTION_REASON;
        $values['fiscal_zone']       = "PT";
        $values['active_by_default'] = "0";

        return $this->insert($values);
    }

    public function getAll($companyID = COMPANY)
    {
        $values               = array();
        $values['company_id'] = $companyID;
        $result               = curl::simple("taxes/getAll", $values);
        return($result);
    }

    public function insert($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("taxes/insert", $values, true);
        if (isset($result['tax_id'])) {
            return($result['tax_id']);
        } else {
            MoloniError::create("taxes/insert", ('Error inserting tax'), $values, $result);
            return(false);
        }
    }

    public function update($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("taxes/update", $values);
        return($result);
    }

    public function delete($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("taxes/delete", $values);
        return($result);
    }
}
