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

class AlternateAddresses extends entities
{

    public function __construct()
    {

    }

    public function search($values)
    {
        $results = $this->getAll($values);

        foreach ($results as $result) {
            if (mb_strtolower($result['code']) == mb_strtolower($values['email'])) {
                return($result['address_id']);
            }
        }

        return(false);
    }

    public function getAll($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("customerAlternateAddresses/getAll", $values);
        return($result);
    }

    public function insert($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("customerAlternateAddresses/insert", $values, true);

        if (isset($result['address_id'])) {
            return($result);
        } else {
            MoloniError::create("customerAlternateAddresses/insert", 'Error inserting alternate address', $values, $result);
        }
    }

    public function update($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("customerAlternateAddresses/update", $values, true);

        if (isset($result['address_id'])) {
            return($result);
        } else {
            MoloniError::create("customerAlternateAddresses/update", 'Error updating alternate address', $values, $result);
        }
    }

    public function delete($values, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result               = curl::simple("customerAlternateAddresses/delete");
        return($result);
    }
}
