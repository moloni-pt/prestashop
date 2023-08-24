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

namespace Moloni\Classes\Products;

use Moloni\Classes\Curl;
use Moloni\Classes\MoloniError;
use Moloni\Facades\ModuleFacade;

class Categories
{
    public function check($name)
    {
        $categories = $this->getByName($name, 0);

        if (!empty($categories) && is_array($categories) && isset($categories[0]['category_id'])) {
            return $categories[0]['category_id'];
        }

        $values = [
            'parent_id' => '0',
            'name' => $name,
            'description' => '',
            'pos_enabled' => '1',
        ];

        return $this->insert($values);
    }

    public function getHierarchy($parentId = '0')
    {
        $categories = $this->getAll($parentId);

        foreach ($categories as &$category) {
            if ($category['num_categories'] > 0) {
                $category['childs'] = $this->getHierarchy($category['category_id']);
            }
        }
        return $categories;
    }

    //          Requests          //

    public function getByName($name = '', $parentId = 0)
    {
        $values = [
            'company_id' => COMPANY,
            'parent_id' => $parentId,
            'name' => $name,
            'exact' => 1
        ];

        return Curl::simple("productCategories/getByName", $values);
    }

    public function getOne($categoryId = 0)
    {
        $values = [
            'company_id' => COMPANY,
            'category_id' => $categoryId
        ];

        return Curl::simple("productCategories/getOne", $values);
    }

    public function getAll($parentId = '0')
    {
        $values = [];
        $values['company_id'] = COMPANY;
        $values['parent_id'] = $parentId;
        $result = Curl::simple("productCategories/getAll", $values);
        return ($result);
    }

    public function insert($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple("productCategories/insert", $values, true);
        if (isset($result['category_id'])) {
            return ($result['category_id']);
        } else {
            $message = ModuleFacade::getModule()->l('Error inserting category');

            MoloniError::create("category/insert", $message, $values, $result);
            return (false);
        }
    }

    public function update($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple("productCategories/update", $values);
        return ($result);
    }

    public function delete($values)
    {
        $values['company_id'] = COMPANY;
        $result = Curl::simple("productCategories/delete", $values);
        return ($result);
    }
}
