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
namespace Moloni\Services\Product;

use Category;
use Configuration;
use ImageManager;
use ImageType;
use Moloni\Classes\Products;
use PrestaShopDatabaseException;
use Tools;

class GetCategoryFromMoloniProduct
{
    /**
     * Moloni API
     *
     * @var Products|null
     */
    private $api;

    /**
     * Prestashop category ID
     *
     * @var array
     */
    private $prestashopCategories = [];

    /**
     * Moloni product
     *
     * @var array
     */
    private $moloniProduct;


    /**
     * Default shop languague id
     *
     * @var false|string
     */
    private $default_lang;

    /**
     * Constructor
     *
     * @param array|null $moloniProduct Moloni product data
     */
    public function __construct($moloniProduct = [])
    {
        $this->api = new Products();
        $this->moloniProduct = $moloniProduct;

        $this->default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * Service runner
     *
     * @return void
     */
    public function run()
    {
        if (empty($this->moloniProduct)) {
            return;
        }

        $rootCategory = Category::getRootCategory();
        $parentId = $rootCategory->id;

        $prestashopCategoryIds = [$parentId];
        $moloniCategories = $this->getMoloniCategoryTree();

        if (empty($moloniCategories)) {
            $this->prestashopCategories = [$rootCategory->id];

            return;
        }

        foreach ($moloniCategories as $moloniCategory) {
            $query = Category::searchByNameAndParentCategoryId($this->default_lang, $moloniCategory, $parentId);

            if (empty($query)) {
                $insertCategory = new Category();
                $insertCategory->active = 1;
                $insertCategory->name = [$this->default_lang => $moloniCategory['name']];
                $insertCategory->id_parent = $parentId;
                $insertCategory->link_rewrite = [$this->default_lang => Tools::link_rewrite($moloniCategory['name'])];

                if (isset($category['image'])) {
                    $this->saveImage($moloniCategory, $insertCategory);
                }

                $insertCategory->save();

                array_unshift($prestashopCategoryIds, $insertCategory->id);
            } else {
                $parentId = $query['id_category'];

                array_unshift($prestashopCategoryIds, $query['id_category']);
            }
        }

        $this->prestashopCategories = $prestashopCategoryIds;
    }

    /**
     * Get categories ids
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->prestashopCategories;
    }

    //          Privates          //

    /**
     * Fetch category tree names
     */
    protected function getMoloniCategoryTree()
    {
        $failsafe = 0;
        $categoryId = $this->moloniProduct['category_id'];
        $productCategoriesNames = [];

        do {
            $query = $this->getById($categoryId);

            if (empty($query)) {
                break;
            }

            /** Order needs to be inverted */
            array_unshift($productCategoriesNames, $query);

            if (empty($query['parent_id'])) {
                break;
            }

            $categoryId = (int)$query['parent_id'];

            $failsafe++;
        } while ($failsafe < 100);

        return $productCategoriesNames;
    }

    /**
     * Search for category by id
     *
     * @param int $categoryId
     *
     * @return array
     */
    private function getById($categoryId = 0)
    {
        return $this->api->categories->getOne($categoryId);
    }

    //          Auxiliary          //

    /**
     * Save category image
     *
     * @throws PrestaShopDatabaseException
     */
    private function saveImage($category, $insertCategory)
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
        }
    }
}
