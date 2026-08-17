<?php
/**
 * 2020 - Moloni.pt
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
 * needs please refer to http://www.prestashop.pt for more information.
 *
 * @author    Nuno Almeida
 * @copyright Nuno Almeida
 * @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 */

namespace Moloni\Classes;

class Curl
{
    /**
     * Hold the request log
     *
     * @var array
     */
    private static $logs = [];

    /**
     * CURL à API do moloni enviando por exemplo invoices/insert
     *
     * @param $action
     * @param $values
     * @param $return
     * @param $print
     *
     * @return false|mixed|void
     */
    public static function simple($action, $values = false, $return = false, $print = false)
    {
        $con = curl_init();
        $url = 'https://api.moloni.pt/v1/' . $action . '/?access_token=' . ACCESS;

        if ($values) {
            $values['company_id'] = COMPANY;
            $send = http_build_query($values);
        } else {
            $send = false;
        }

        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_POSTFIELDS, $send);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($con, CURLOPT_TIMEOUT, 30);

        $res_curl = curl_exec($con);
        $res_errno = curl_errno($con);
        $res_error = $res_errno ? curl_error($con) : '';
        curl_close($con);

        if ($res_errno) {
            // Erro de transporte (timeout, DNS, ligação recusada, ...).
            // Tratado como erro para que o fluxo devolva false de forma consistente.
            $res_txt = ['error' => true, 'curl_errno' => $res_errno, 'curl_error' => $res_error];
        } else {
            $res_txt = json_decode($res_curl, true);
        }

        if ($print) {
            echo $url;
            echo '<pre>';
            print_r($res_txt);
            echo '</pre>';
            exit;
        }

        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        return $return ? $res_txt : false;
    }

    /**
     * Login com as credênciais do utilizador
     *
     * @param $user
     * @param $pass
     *
     * @return false|mixed
     */
    public static function login($user, $pass)
    {
        $con = curl_init();
        $url = "https://api.moloni.pt/v1/grant/?grant_type=password&client_id=devapi&client_secret=53937d4a8c5889e58fe7f105369d9519a713bf43&username=$user&password=$pass";

        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, false);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($con, CURLOPT_TIMEOUT, 30);

        $res_curl = curl_exec($con);
        $res_info = curl_getinfo($con);
        $res_error = curl_errno($con) ? curl_error($con) : '';

        $res_txt = json_decode($res_curl, true);

        curl_close($con);

        $log = [
            'url' => $url,
            'sent' => [],
            'received' => $res_txt,
            'curl_info' => isset($res_info) ? $res_info : [],
            'curl_error' => $res_error,
        ];

        self::$logs[] = $log;

        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        return (false);
    }

    // Fazer um refresh à refresh e access token, quando a access token estiver para expirar
    public static function refresh($refresh)
    {
        $con = curl_init();
        $url = 'https://api.moloni.pt/v1/grant/?grant_type=refresh_token&client_id=devapi&client_secret=53937d4a8c5889e58fe7f105369d9519a713bf43&refresh_token=' . $refresh;
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, false);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($con, CURLOPT_TIMEOUT, 30);

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);

        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        return (false);
    }

    //              GETS              //

    /**
     * Returns the last curl request made from the logs
     *
     * @return array
     */
    public static function getLog()
    {
        if (empty(self::$logs)) {
            return [];
        }

        return end(self::$logs);
    }

    /**
     * Returns the last curl request made from the logs
     *
     * @return array
     */
    public static function getLogs()
    {
        return isset(self::$logs) ? self::$logs : [];
    }
}
