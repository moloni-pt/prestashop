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

class MoloniError
{
    public static $exists = false;
    public static $message = false;

    public static function create($where, $message, $values_sent = null, $values_receive = null)
    {
        self::$exists = true;
        self::$message = array();
        self::$message['where'] = $where;
        self::$message['message'] = $message;
        self::$message['values_sent'] = (empty($values_sent) ? '' : $values_sent);
        self::$message['values_receive'] = (empty($values_receive) ? '' : $values_receive);
    }
}
