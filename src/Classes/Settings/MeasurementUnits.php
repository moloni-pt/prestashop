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

namespace Moloni\Classes\Settings;

use Moloni\Classes\Curl;
use Moloni\Classes\MoloniError;
use Moloni\Classes\Settings;

class MeasurementUnits extends Settings
{

    public function __construct()
    {

    }

    public function check($name)
    {

        $units = $this->getAll();
        foreach ($units as $unit) {
            if (mb_strtolower($name) == mb_strtolower($unit['name'])) {
                return $unit['unit_id'];
            }
        }

        $values = array();
        $values['name'] = $name;
        $values['short_name'] = "Uni.";

        return $this->insert($values);
    }

    public function getAll()
    {
        $values = array();
        $values['company_id'] = COMPANY;
        $result = Curl::simple("measurementUnits/getAll", $values);
        return ($result);
    }

    public function insert($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple("measurementUnits/insert", $values, true);
        if (isset($result['unit_id'])) {
            return ($result['unit_id']);
        } else {
            MoloniError::create("measurementUnits/insert", ('Error inserting Measurement Unit'), $values, $result);
            return (false);
        }
    }

    public function update($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple("measurementUnits/update", $values);
        return ($result);
    }

    public function delete($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple("measurementUnits/delete", $values);
        return ($result);
    }
}
