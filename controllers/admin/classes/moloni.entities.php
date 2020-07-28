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
require_once('entities/moloni.customers.php');
require_once('entities/moloni.alternateAddresses.php');

class Entities extends ModuleAdminController
{

    public $customers = false;
    public $alternateAddresses = false;

    public function __construct()
    {
        if (!$this->customers) {
            $this->customers = new Customers();
        }

        if (!$this->alternateAddresses) {
            $this->alternateAddresses = new AlternateAddresses();
        }
    }

}
