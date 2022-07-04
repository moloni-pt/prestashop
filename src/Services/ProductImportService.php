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

use Configuration;
use General;
use Image;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use StockAvailable;

class ProductImportService
{
    private $product;
    private $categoriesExists;
    private $default_lang;

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($productToImport)
    {
        if (is_array($productToImport) && !empty($productToImport)) {
            $this->product = $productToImport;
        }
        $categories = new CategoryImportService();
        $this->categoriesExists = $categories->run();

        $this->default_lang = Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function run()
    {
        if (isset($this->product['name']) && !empty($this->product['name'])) {
            $newProduct = new Product();
            $newProduct->name = [$this->default_lang => $this->getName($this->product['name'])];
            $newProduct->reference = $this->product['reference'];
            $newProduct->link_rewrite = [$this->default_lang => $this->linkRewrite($this->product['name'])];
            $newProduct->price = $this->product['price'];
            $newProduct->ean13 = $this->getEAN($this->product['ean']);
            $newProduct->quantity = ($this->product['has_stock'] ? $this->product['stock'] : 0);
            $newProduct->description = $this->product['summary'] ?: '';
            $newProduct->id_category = $this->categoriesExists[$this->product['category_id']]['ps_id'];
            $newProduct->id_category_default = $this->categoriesExists[$this->product['category_id']]['ps_id'];

            if ($newProduct->add()) {
                StockAvailable::setQuantity((int)$newProduct->id, 0, $newProduct->quantity);

                if (!empty($product['image'])) {
                    $imgUrl = 'https://www.moloni.pt/_imagens/?macro=&img=' . $product['image'];
                    $image = new Image();
                    $image->id_product = $newProduct->id;
                    $image->cover = true;

                    if (($image->validateFields(false, true)) === true
                        && ($image->validateFieldsLang(false, true)) === true
                        && $image->add()
                        && !General::saveImageFromUrl($newProduct->id, $image->id, $imgUrl)) {
                        $image->delete();
                    }
                }

                $newProduct->addToCategories([$this->categoriesExists[$this->product['category_id']]['ps_id']]);

                return $newProduct->id;
            }
        }

        return false;
    }

    /**
     * Cleans EAN field
     *
     * @param string $ean EAN value
     *
     * @return string
     */
    private function getEAN(string $ean): string
    {
        if (!$ean || !preg_match('/^[0-9]{0,13}$/', $ean)) {
            $ean = '';
        }

        return $ean;
    }

    /**
     * Cleans name field
     *
     * @param string $name Name value
     *
     * @return string
     */
    private function getName(string $name): string
    {
        if (!empty($name)) {
            $name = str_replace(['<', '>', ';', '=', '#', '{', '}'], '', $name); // Removes special chars.
        }

        return $name;
    }

    /**
     * Cleans link rewrite field
     *
     * @param string $name Name value
     *
     * @return string
     */
    private function linkRewrite(string $name): string
    {
        if (!empty($name)) {
            $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name); // Removes special chars and spaces.
        }

        return $name;
    }
}