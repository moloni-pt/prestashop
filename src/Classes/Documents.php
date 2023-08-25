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

use Moloni\Facades\ModuleFacade;
use Moloni\Traits\ClassTrait;

class Documents
{
    use ClassTrait;

    public function insertInvoice($values, $type = DOCUMENT_TYPE, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = Curl::simple("$type/insert", $values, true);

        if (isset($result['document_id'])) {
            return ($result['document_id']);
        } else {
            $message = ModuleFacade::getModule()->l('Error inserting document', $this->className());

            MoloniError::create("$type/insert", $message, $values, $result);
        }
        return ($result);
    }

    public function getOneInfo($document_id, $type = DOCUMENT_TYPE, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['document_id'] = $document_id;

        $result = Curl::simple($type . "/getOne", $values);
        return ($result);
    }

    public function update($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple(DOCUMENT_TYPE . "/update", $values, true);

        if (isset($result['document_id'])) {
            return ($result['document_id']);
        } else {
            $message = ModuleFacade::getModule()->l('Error updating document', $this->className());

            MoloniError::create(DOCUMENT_TYPE . "/update", $message, $values, $result);
        }
        return ($result);
    }

    public function getAll($values, $max = 20)
    {
        $values['company_id'] = COMPANY;
        $values['document_set_id'] = DOCUMENT_SET;


        $total = 0;
        $offset = (isset($values['offset']) ? $values['offset'] : "0");
        $results = array();

        while ($total < $max) {
            $values['offset'] = $offset;
            $result = Curl::simple("documents/getAll", $values);
            if (is_array($result)) {
                $results = array_merge($results, $result);
                $total += count($result);
            }
            $offset += 50;

            if (count($result) < 50) {
                break;
            }
        }

        return (count($results) > 0) ? $results : false;
    }

    public function getPDFLink($document_id)
    {
        $values = array();
        $values['company_id'] = COMPANY;
        $values['document_id'] = $document_id;

        $result = Curl::simple("documents/getPDFLink", $values);
        return (isset($result['url'])) ? $result['url'] : false;
    }

    public function currentType($input = DOCUMENT_TYPE)
    {

        switch ($input) {
            case "invoices":
                $return = "Faturas";
                break;
            case "invoiceReceipts":
                $return = "FaturasRecibo";
                break;
            case "purchaseOrder":
                $return = "NotasEncomenda";
                break;
            case "estimates":
                $return = "Orcamentos";
                break;
            case "FT":
                $return = "Faturas";
                break;
            case "FR":
                $return = "FaturasRecibo";
                break;
            case "NE":
                $return = "NotasEncomenda";
                break;
            case "ORC":
                $return = "Orcamentos";
                break;
            default:
                $return = "-";
                break;
        }

        return ($return);
    }
}
