<?php

class FetchPendingOrders
{
    private $request;
    private $documentList = [];
    private $totalPendingOrders = 0;

    private $languageId;

    private $queryCondition = '';
    private $queryTotalResultsCondition = '';

    public function __construct($request = [])
    {
        $this->request = $request;
        $this->languageId = (int)Configuration::get('PS_LANG_DEFAULT');
    }

    public function run()
    {
        $this->fetchAllOrders();

        return [
            'data' => $this->documentList,
            'recordsTotal' => $this->totalPendingOrders,
            'recordsFiltered' => $this->totalPendingOrders,
        ];
    }

    private function fetchAllOrders()
    {
        // Segmentation for query, based on defined settings
        $this->fetchOrdersFilters();

        // Get total pending documents
        $this->totalPendingOrders = Db::getInstance()
            ->ExecuteS('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'orders' . $this->queryTotalResultsCondition)[0]['COUNT(*)'];

        $orders = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'orders' . $this->queryCondition);

        if ($orders) {
            foreach ($orders as $order) {
                $this->documentList[] = $this->processProduct($order);
            }
        }
    }

    private function fetchOrdersFilters()
    {
        $condition = ' AS O';

        // Manual search
        $search = $this->request['search']['value'] ?: '';

        if (!empty($search)) {
            $condition .= ' INNER JOIN ' . _DB_PREFIX_ . 'address AS A ON (A.id_address = O.id_address_invoice)';
            $condition .= ' INNER JOIN ' . _DB_PREFIX_ . 'customer AS C ON (C.id_customer = O.id_customer)';
            $condition .= ' WHERE (';
            $condition .= ' O.date_add like "%' . $search . '%"';
            $condition .= ' or';
            $condition .= ' O.id_order like "%' . $search . '%"';
            $condition .= ' or';
            $condition .= ' C.email like "%' . $search . '%"';
            $condition .= ' or';
            $condition .= ' A.firstname like "%' . $search . '%"';
            $condition .= ' or';
            $condition .= ' A.lastname like "%' . $search . '%"';
            $condition .= ')';
        }

        // Active status to show
        $selectedStatus = unserialize(defined('ORDER_STATUS') ? ORDER_STATUS : '[]');

        if (!empty($search)) { // if search has value, whe need to append condition, not initialize
            $condition .= ' AND (';
        } else {
            $condition .= ' WHERE (';
        }

        if (is_array($selectedStatus) && $selectedStatus[0] !== '') {
            foreach ($selectedStatus as $status) {
                $condition .= " O.current_state = '" . pSQL($status) . "' OR";
            }

            $condition = Tools::substr($condition, 0, -2);
        } else {
            $condition .= "O.current_state LIKE '%%'";
        }

        $condition .= ')';

        // If in settings is defined a date to filter out older orders
        if (defined('AFTER_DATE') && !empty(AFTER_DATE)) {
            $condition .= " AND O.date_add  > '" . pSQL(AFTER_DATE) . "' ";
        }

        // Do not bring orders that already have been processed by the plugin
        $condition .= ' AND (NOT EXISTS(SELECT order_id FROM ' . _DB_PREFIX_ . 'moloni_invoices WHERE ' . _DB_PREFIX_ . 'moloni_invoices.order_id = O.id_order))';

        // Direction and order
        $column = isset($this->request['order'][0]['column']) ? (int)$this->request['order'][0]['column'] : 0;
        $direction = isset($this->request['order'][0]['dir']) ? strtoupper($this->request['order'][0]['dir']) : 'DESC';

        switch ($column) {
            case 3:
                $field = 'O.date_add'; // Order by order date
                break;
            case 5:
                $field = 'O.total_paid'; // Order by order date
                break;
            case 0:
            default:
                $field = 'O.id_order'; // Order by order id
                break;
        }

        $condition .= ' ORDER BY ' . $field . ' ' . $direction;

        // Save query segmentation for totals here, because it cannot have "LIMIT"
        $this->queryTotalResultsCondition = $condition;

        // Lets limit results
        $offset = $this->request['start'] ?: 0;
        $length = $this->request['length'] ?: 10;

        $condition .= ' LIMIT ' . $offset . ', ' . ($offset + $length);

        $this->queryCondition = $condition;
    }

    private function processProduct($order)
    {
        $address = Db::getInstance()
            ->getRow('SELECT * FROM ' . _DB_PREFIX_ . "address  WHERE id_address = '" . (int)$order['id_address_invoice'] . "'");
        $customer = Db::getInstance()
            ->getRow('SELECT * FROM ' . _DB_PREFIX_ . "customer WHERE id_customer = '" . (int)$order['id_customer'] . "'");
        $state = Db::getInstance()
            ->getRow('SELECT * FROM ' . _DB_PREFIX_ . "order_state_lang WHERE id_order_state = '" . (int)$order['current_state'] . "' and id_lang = '" . $this->languageId . "'");

        return [
            'info' => $order,
            'address' => $address,
            'customer' => $customer,
            'state' => $state,
            'url' => [
                'order' => $this->genURL('AdminOrders', '&id_order=' . $order['id_order'] . '&vieworder'),
                'create' => $this->genURL('MoloniStart', '&action=create&id_order=' . $order['id_order']),
                'clean' => $this->genURL('MoloniStart', '&action=clean&id_order=' . $order['id_order'])
            ]
        ];
    }

    private function genURL($controller, $extra = '')
    {
        return 'index.php?controller=' . $controller . $extra . '&token=' . Tools::getAdminTokenLite($controller);
    }
}
