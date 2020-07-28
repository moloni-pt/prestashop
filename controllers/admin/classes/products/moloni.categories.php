<?php

/**
 * 2020 - Moloni.com
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
class Categories
{
    public function __construct()
    {
        
    }

    public function check($name)
    {

        $categories = $this->getAll();
        foreach ($categories as $category) {
            if (mb_strtolower($name) == mb_strtolower($category['name'])) {
                return $category['category_id'];
            }
        }

        $values = array();
        $values['parent_id'] = "0";
        $values['name'] = $name;
        $values['description'] = "";
        $values['pos_enabled'] = "1";

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

    public function getAll($parentId = '0')
    {
        $values = array();
        $values['company_id'] = COMPANY;
        $values['parent_id'] = $parentId;
        $result = curl::simple("productCategories/getAll", $values);
        return($result);
    }

    public function insert($values)
    {
        $values['company_id'] = COMPANY;
        $result = curl::simple("productCategories/insert", $values, true);
        if (isset($result['category_id'])) {
            return($result['category_id']);
        } else {
            MoloniError::create("category/insert", ('Error inserting category'), $values, $result);
            return(false);
        }
    }

    public function update($values)
    {
        $values['company_id'] = COMPANY;
        $result = curl::simple("productCategories/update", $values);
        return($result);
    }

    public function delete($values)
    {
        $values['company_id'] = COMPANY;
        $result = curl::simple("productCategories/delete", $values);
        return($result);
    }

}
