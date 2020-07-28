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
class Curl
{

    // CURL à API do moloni enviando por exemplo invoices/insert
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

    // Testar a ligação para verificar se existe algum erro
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

    // Login com as credênciais do utilizador
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
        curl_close($con);

        $res_txt = json_decode($res_curl, true);
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

        $res_txt = Tools::jsonDecode($res_curl, true);
        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        echo 'Falhou a obter a token ' . $url . '<br>' . $res_curl;
        return (false);
    }
}
