<?php

namespace Moloni\Webservice;

use Db;
use Configuration;
use WebserviceKey;

class Webservices
{
    private $key = '';
    private $url = '';
    private $description = 'Moloni Webservice key';

    public function enable()
    {
        if ((int)Configuration::get('PS_WEBSERVICE') === 0) {
            $this->enableWebServices();
        }

        $this->fillKey();
    }

    public function disable()
    {
        if ((int)Configuration::get('PS_WEBSERVICE') === 0) {
            return;
        }

        $this->deleteWebserviceKey();
    }

    public function getWebserviceProductSyncUrl(?array $syncFields = []): string
    {
        if (empty($this->key)) {
            $this->fillKey();
        }

        $params = [
            'operation' => 'syncProducts',
            'sync_fields' => $syncFields
        ];

        return $this->url . '&' . http_build_query($params);
    }

    //           PRIVATES           //

    private function enableWebServices()
    {
        Configuration::updateValue('PS_WEBSERVICE', 1);
    }

    private function setUrl()
    {
        $baseUrlSecure = defined('_PS_BASE_URL_SSL_') ? _PS_BASE_URL_SSL_ : '';

        $this->url = $baseUrlSecure . '/api/moloniresource/?ws_key=' . $this->key;
    }

    private function fillKey()
    {
        $key = $this->fetchWebserviceKey();

        if (empty($key)) {
            $key = $this->createWebserviceKey();
        }

        $this->key = $key;

        $this->setUrl();
    }

    private function createWebserviceKey()
    {
        $randKey = substr(str_shuffle(MD5(microtime())), 0, 32);

        $apiAccess = new WebserviceKey();
        $apiAccess->key = $randKey;
        $apiAccess->description = $this->description;
        $apiAccess->save();

        $permissions = [
            'moloniresource' => [
                'GET' => 1,
                'POST' => 1,
            ],
        ];

        WebserviceKey::setPermissionForAccount($apiAccess->id, $permissions);

        return $apiAccess->key;
    }

    //           QUERIES           //

    private function deleteWebserviceKey()
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'webservice_account WHERE description = "' . pSQL($this->description) .'"';

        $dataBase = Db::getInstance();
        $dataBase->execute($sql);
    }

    private function fetchWebserviceKey()
    {
        $key = '';

        $dataBase = Db::getInstance();
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'webservice_account WHERE description = "' . pSQL($this->description) .'"';
        $query = $dataBase->getRow($sql);


        if ($query !== false) {
            $key = $query['key'];
        }

        return $key;
    }
}
