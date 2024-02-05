<?php
/**
 * 2022 - Moloni.com
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
 * @author    Moloni
 * @copyright Moloni
 * @license   https://creativecommons.org/licenses/by-nd/4.0/
 *
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

namespace Moloni\Mails;

use Configuration;
use Mail;
use Moloni\Enums\Domains;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DocumentWarningMail extends SendMail
{
    public function handle()
    {
        $orderId = isset($this->data["order_id"]) ? $this->data["order_id"] : 0;

        Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')), // defaut language id
            'document_warning', // email template file to be use
            $this->subject, // email subject
            [
                '{order_id}' => $orderId,
                '{moloni_url}' => Domains::MOLONI,
                '{moloni_logo_url}' =>  $this->getLogoUrl(),
                '{year}' => date("Y"),
            ],
            $this->email, // receiver email address
            NULL, //receiver name
            NULL, //from email address
            NULL,  //from name
            NULL, //file attachment
            NULL, //mode smtp
            _PS_MODULE_DIR_ . 'moloni/mails' //custom template path
        );
    }
}
