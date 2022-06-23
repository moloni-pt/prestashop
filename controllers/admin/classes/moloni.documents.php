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
class Documents
{

    public function insertInvoice($values, $type = DOCUMENT_TYPE, $companyID = COMPANY)
    {
        $values['company_id'] = $companyID;
        $result = curl::simple("$type/insert", $values, true);

        if (isset($result['document_id'])) {
            return($result['document_id']);
        } else {
            MoloniError::create("$type/insert", "Erro ao inserir documento", $values, $result);
        }
        return($result);
    }

    public function getOneInfo($document_id, $type = DOCUMENT_TYPE, $companyID = COMPANY)
    {
        $values = array();
        $values['company_id'] = $companyID;
        $values['document_id'] = $document_id;

        $result = curl::simple($type . "/getOne", $values);
        return($result);
    }

    public function update($values)
    {
        $values['company_id'] = COMPANY;
        $result = curl::simple(DOCUMENT_TYPE . "/update", $values, true);

        if (isset($result['document_id'])) {
            return($result['document_id']);
        } else {
            MoloniError::create(DOCUMENT_TYPE . "/update", "Erro ao actualizar documento", $values, $result);
        }
        return($result);
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
            $result = curl::simple("documents/getAll", $values);
            if (is_array($result)) {
                $results = array_merge($results, $result);
                $total += count($result);
            }
            $offset += 50;

            if (count($result) < 50) {
                break;
            }
        }

        return ( count($results) > 0 ) ? $results : false;
    }

    public function getPDFLink($document_id)
    {
        $values = array();
        $values['company_id'] = COMPANY;
        $values['document_id'] = $document_id;

        $result = curl::simple("documents/getPDFLink", $values);
        return (isset($result['url'])) ? $result['url'] : false;
    }

    public function currentType($input = DOCUMENT_TYPE)
    {

        switch ($input) {
            case "invoices": $return = "Faturas";
                break;
            case "invoiceReceipts": $return = "FaturasRecibo";
                break;
            case "purchaseOrder": $return = "NotasEncomenda";
                break;
            case "estimates": $return = "Orcamentos";
                break;
            case "FT": $return = "Faturas";
                break;
            case "FR": $return = "FaturasRecibo";
                break;
            case "NE": $return = "NotasEncomenda";
                break;
            case "ORC": $return = "Orcamentos";
                break;
            default: $return = "-";
                break;
        }

        return ($return);
    }
}
