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

class GlobalData extends ModuleAdminController
{

    public function __construct()
    {
        $this->countries      = new Countries;
        $this->fiscalZones    = new FiscalZones;
        $this->languages      = new Languages;
        $this->currencies     = new Currencies;
        $this->documentModels = new DocumentModels;
        $this->taxExemptions  = new TaxExemptions;
    }
}
/* * ***************************** Países ****************************** */
include_once('global/moloni.countries.php');
/* * ******************************************************************* */



/* * *************************** Zonas Fiscais ************************* */
include_once('global/moloni.fiscalZones.php');
/* * ******************************************************************* */



/* * *************************** Linguas ****************************** */
include_once('global/moloni.languages.php');
/* * ******************************************************************* */



/* * ************************ Moedas Disponíveis *********************** */
include_once('global/moloni.currencies.php');
/* * ******************************************************************* */



/* * ************************** Modelos do Documento ****************** */
include_once('global/moloni.documentsModels.php');
/* * ******************************************************************* */



/* * ************************* Razões de Isenção *********************** */
include_once('global/moloni.taxExemption.php');
/* * ******************************************************************* */
