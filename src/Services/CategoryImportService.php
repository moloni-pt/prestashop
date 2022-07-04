<?php
/**
 * 2022 - moloni.pt
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.a
 *
 * You must not modify, adapt or create derivative works of this source code
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    César Freitas
 * @copyright César Freitas
 * @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 *
 */
namespace Moloni\Services;

use Category;
use Configuration;
use ImageManager;
use ImageType;
use PrestaShopDatabaseException;
use PrestaShopException;
use Products;
use Tools;

class CategoryImportService
{
    private $categoriesExists = [];

    /**
     * @var false|string
     */
    private $default_lang;

    public function __construct()
    {
        $this->default_lang = Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function run(): array
    {
        $rootCategory = Category::getRootCategory();
        $this->categoriesExists['0'] = ['name' => $rootCategory->name, 'ps_id' => $rootCategory->id];

        $products = new Products();
        $categories = $products->categories->getHierarchy();
        $this->checkCategoryExists($categories, $rootCategory->id);

        foreach ($this->categoriesExists as &$category) {
            if (!$category['ps_id']) {
                $insertCategory = new Category();
                $insertCategory->name = [$this->default_lang => $category['name']];
                $insertCategory->active = 1;
                $insertCategory->link_rewrite = [$this->default_lang => Tools::link_rewrite($category['name'])];
                $insertCategory->id_parent = $this->categoriesExists[$category['parent_id']]['ps_id'];

                if ($insertCategory->add()) {
                    $category['ps_id'] = $insertCategory->id;
                }
                // All images bust be placed as JPG
                if (isset($category['image'])) {
                    $this->saveImage($category, $insertCategory);
                }

            }
        }

        return $this->categoriesExists;
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    private function saveImage($category, $insertCategory): void
    {
        $imgExt = explode('.', $category['image']);
        $imgUrl = 'https://www.moloni.pt/_imagens/?macro=&img=' . $category['image'];
        $imgName = _PS_CAT_IMG_DIR_ . $insertCategory->id . '.' . end($imgExt);

        file_put_contents($imgName, file_get_contents($imgUrl));

        if (file_exists($imgName)) {
            $images_types = ImageType::getImagesTypes('categories');

            // Save the image to use as main image
            $infos = getimagesize($imgName);
            ImageManager::resize($imgName, _PS_CAT_IMG_DIR_ . $insertCategory->id . '.jpg', (int)$infos[0], (int)$infos[1]);

            // Save each required image type
            foreach ($images_types as $image_type) {
                ImageManager::resize($imgName, _PS_CAT_IMG_DIR_ . $insertCategory->id . '-' . stripslashes($image_type['name']) . '.jpg', (int)$image_type['width'], (int)$image_type['height']);
            }

            $insertCategory->id_image = $insertCategory->id;
            $insertCategory->save();
        }
    }

    /**
     * @param array $categories
     * @param int $parentId
     *
     * @return void
     */
    private function checkCategoryExists(array $categories, int $parentId): void
    {
        foreach ($categories as $moloniCategory) {
            $search = Category::searchByNameAndParentCategoryId(Configuration::get('PS_LANG_DEFAULT'), $moloniCategory['name'], $parentId);
            $this->categoriesExists[$moloniCategory['category_id']] = [
                'name' => $moloniCategory['name'],
                'ps_id' => $search ? $search['id_category'] : false,
                'parent_id' => $moloniCategory['parent_id'],
                'image' => $moloniCategory['image']
            ];

            if (isset($moloniCategory['childs']) && count($moloniCategory['childs']) > 0) {
                $this->checkCategoryExists($moloniCategory['childs'], ($search ? $search['id_category'] : 0));
            }
        }
    }


}