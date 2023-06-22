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

use Moloni\Classes\Settings\DeliveryMethods;
use Moloni\Classes\Settings\DocumentSets;
use Moloni\Classes\Settings\MaturityDates;
use Moloni\Classes\Settings\MeasurementUnits;
use Moloni\Classes\Settings\PaymentMethods;
use Moloni\Classes\Settings\Taxes;

class Settings
{
    /** @var DocumentSets */
    public $documentSets;

    /** @var PaymentMethods */
    public $paymentMethods;

    /** @var MaturityDates */
    public $maturityDates;

    /** @var DeliveryMethods */
    public $deliveryMethods;

    /** @var Taxes */
    public $taxes;

    /** @var MeasurementUnits */
    public $measurementUnits;

    public function __construct()
    {
        $this->documentSets = new DocumentSets;
        $this->paymentMethods = new PaymentMethods;
        $this->maturityDates = new MaturityDates;
        $this->deliveryMethods = new DeliveryMethods;
        $this->taxes = new Taxes;
        $this->measurementUnits = new MeasurementUnits;
    }
}