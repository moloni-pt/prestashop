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

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);

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
     * Testar a ligação para verificar se existe algum erro
     *
     * @return bool
     */
    public static function test()
    {
        $con = curl_init();
        $url = 'https://api.moloni.pt/v1/products/getOne/?access_token=FAKETOKEN';   /* Substituir pelo token atual */

        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_POSTFIELDS, false);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);
        if (isset($res_txt['error'])) {
            return (true);
        }

        return (false);
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

        $res_curl = curl_exec($con);
        $res_info = curl_getinfo($con);

        curl_close($con);

        $res_txt = json_decode($res_curl, true);

        $log = [
            'url' => $url,
            'sent' => [],
            'received' => $res_txt,
            'curl_info' => $res_info ?? [],
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

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);

        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        echo 'Falhou a obter a token ' . $url . '<br>' . $res_curl;
        return (false);
    }

    //              GETS              //

    /**
     * Returns the last curl request made from the logs
     *
     * @return array
     */
    public static function getLog(): array
    {
        return end(self::$logs) ?? [];
    }

    /**
     * Returns the last curl request made from the logs
     *
     * @return array
     */
    public static function getLogs(): array
    {
        return self::$logs ?? [];
    }
}
