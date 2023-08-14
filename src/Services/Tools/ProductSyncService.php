<?php
/**
 * 2023 - moloni.pt
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
 * @author    Moloni
 * @copyright Moloni
 * @license   https://creativecommons.org/licenses/by-nd/4.0/  Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)
 *
 * @noinspection SqlResolve
 * @noinspection SqlNoDataSourceInspection
 */

namespace Moloni\Services\Tools;

use Combination;
use Configuration;
use Db;
use Moloni\Classes\Products;
use Moloni\Services\Product\Image\UpdatePrestaCombinationImage;
use Moloni\Services\Product\Image\UpdatePrestaProductImage;
use Moloni\Services\Product\ProductImportService;
use Moloni\Services\Product\Tax\FindTaxGroupFromMoloniTax;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use StockAvailable;
use Tools;

class ProductSyncService
{
    private $shouldSyncStock = false;
    private $shouldSyncPrice = false;
    private $shouldSyncName = false;
    private $shouldSyncDescription = false;
    private $shouldSyncEAN = false;
    private $shouldSyncTax = false;
    private $shouldSyncImage = false;

    private $date = null;
    private $page = null;
    private $perPage = 20;

    private $totalProducts = 0;

    /**
     * Produto atributo
     */
    private $currentSyncAttributeProduct;

    /**
     * ID do produto a atualizar, produto simples
     */
    private $currentSyncProductId;

    /**
     * Produto que vem do Moloni
     */
    private $moloniProduct;

    private $updatedResult = [];

    /** Public's */

    public function run(): ProductSyncService
    {
        if (empty($this->date)) {
            $this->date = (Tools::getValue('updateSince')) ?: date('Y-m-d H:i:s', strtotime('-1 week'));
        }

        /** Vamos buscar todos */
        $modifiedProducts = $this->getModifiedProducts();
        $this->totalProducts = count($modifiedProducts);

        $this->addHeader();

        if ($modifiedProducts && is_array($modifiedProducts)) {
            foreach ($modifiedProducts as $product) {
                $this->currentSyncProductId = 0;
                $this->currentSyncAttributeProduct = [];

                try {
                    $this->syncProduct($product);
                } catch (PrestaShopDatabaseException|PrestaShopException $e) {
                    $this->addFatalError([
                        'name' => $this->moloniProduct['name'] ?? '',
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $this;
    }

    /** Sets */

    public function instantiateSyncFilters(): void
    {
        $syncFields = Tools::getValue('sync_fields');

        if ($syncFields === false) {
            return;
        }

        if (is_string($syncFields)) {
            $syncFields = explode(',', $syncFields);
        }

        if (in_array('stock', $syncFields, true)) {
            $this->enableStockSync();
        }

        if (in_array('price', $syncFields, true)) {
            $this->enablePriceSync();
        }

        if (in_array('name', $syncFields, true)) {
            $this->enableNameSync();
        }

        if (in_array('description', $syncFields, true)) {
            $this->enableDescriptionSync();
        }

        if (in_array('ean', $syncFields, true)) {
            $this->enableEANSync();
        }

        if (in_array('tax', $syncFields, true)) {
            $this->enableTaxSync();
        }

        if (in_array('image', $syncFields, true)) {
            $this->enableImageSync();
        }
    }

    public function setImportDate($date): void
    {
        $this->date = $date;
    }

    public function setPage($page)
    {
        $this->page = (int)$page;
    }

    /** Gets */

    public function getPage()
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getResults(): ?array
    {
        return $this->updatedResult;
    }

    public function getTotalProducts(): int
    {
        return $this->totalProducts;
    }

    /** Privates */

    /**
     * Metodo que trata da sincronizacao e do tipo de produto que é para sincronizar
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function syncProduct($moloniProduct): void
    {
        $this->moloniProduct = $moloniProduct;
        $this->moloniProduct['reference'] = trim($this->moloniProduct['reference']);

        /** Verifica se o artigo atual é um artigo Atributo (tem um pai) */
        $this->currentSyncAttributeProduct = $this->getAttributeProduct();

        if ($this->currentSyncAttributeProduct) {
            $this->syncAttributeProduct();
            return;
        }

        /** Verifica se o artigo é Simples */
        $this->currentSyncProductId = $this->getProductIdByReference();

        if ($this->currentSyncProductId > 0) {
            $this->syncSimpleProduct();
            return;
        }

        /** Artigo não existe e tem de ser criado */
        $this->updatedResult['not_found'][] = $this->moloniProduct;
        $importProductService = new ProductImportService($this->moloniProduct);

        if ($importProductService->run()) {
            $this->insertSuccess([
                'name' => $this->moloniProduct['name'],
                'price' => $this->moloniProduct['price']
            ]);

            return;
        }

        $this->insertError([
            'name' => $this->moloniProduct['name'],
            'price' => $this->moloniProduct['price']
        ]);

    }

    /**
     * Produtos modificados a partir de cada data ( Pedido a api na classe Products)
     *
     * @return false|mixed|void
     */
    private function getModifiedProducts()
    {
        $api = new Products();

        $values = [
            'lastmodified' => $this->date,
            'qty' => $this->perPage,
            'offset' => 0
        ];

        if (empty($this->page)) {
            $cycles = 0;
            $products = [];

            while ($cycles < 1000) {
                $query = $api->getModifiedSince($values);

                $products = array_merge($products, $query);

                if (count($query) !== $this->perPage) {
                    break;
                }

                $values['offset'] += $this->perPage;
                $cycles++;
            }
        } else {
            $values['offset'] = $this->perPage * ($this->page - 1);

            $products = $api->getModifiedSince($values);
        }

        return $products;
    }

    /** Attribute product */

    /**
     * Sincronizacao de um produto atributo
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function syncAttributeProduct(): void
    {
        /** Sincronizar o stock dos produtos que fazem o produto com atributos e atualizar o produto principal*/
        if ((int)$this->moloniProduct['has_stock'] === 1 && $this->shouldSyncStock) {
            $this->syncAttributeProductStock();
        }

        /** Sincronizar os campos dos produtos que fazem o produto com atributos e atualizar o produto principal*/
        if ($this->shouldSyncPrice) {
            $this->syncAttributeProductPrice();
        }

        if ($this->shouldSyncEAN && $this->isEan13Valid($this->moloniProduct['ean'])) {
            $this->syncAttributeProductEAN();
        }

        if ($this->shouldSyncImage && !empty($this->moloniProduct['image'])) {
            $this->syncAttributeImage();
        }
    }

    /**
     * Sincronizacao do EAN
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function syncAttributeProductEAN(): void
    {
        if ((int)$this->currentSyncAttributeProduct['id_product_attribute'] < 0 || empty($this->currentSyncAttributeProduct['id_product_attribute'])) {
            return;
        }

        $product = new Combination(
            $this->currentSyncAttributeProduct['id_product_attribute']
        );

        $ean = $this->moloniProduct['ean'] ?? '';

        if (!empty($ean) && $ean !== $product->ean13) {
            $this->addUpdateAttributes([
                'ean_before' => $product->ean13,
                'ean_after' => $this->moloniProduct['ean'],
                'shouldSyncStock' => $this->shouldSyncStock
            ]);
            $product->ean13 = $this->moloniProduct['ean'];
            $product->update();
        }
    }

    /**
     * Atualiza os precos de produtos Atributos
     * É guardada a diferenca para o preco do produto "pai"
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    private function syncAttributeProductPrice(): bool
    {
        $parentProduct = $this->getProductById($this->currentSyncAttributeProduct['id_product']);

        if (!$parentProduct) {
            return false;
        }

        if ($this->moloniProduct['price'] > $parentProduct['price']) {
            $priceDiference = $this->moloniProduct['price'] - $parentProduct['price'];
        } else if ($this->moloniProduct['price'] < $parentProduct['price']) {
            $priceDiference = -1 * ($parentProduct['price'] - $this->moloniProduct['price']);
        } else {
            $priceDiference = 0;
        }

        if ((float)$this->moloniProduct['price'] !== ($parentProduct['price'] + $priceDiference)) {
            $attributeProduct = new Combination(
                $this->currentSyncAttributeProduct['id_product_attribute']
            );

            if (!$attributeProduct) {
                return false;
            }

            $this->addUpdateAttributes([
                'price_before' => $parentProduct['price'] + $priceDiference,
                'price_after' => $this->moloniProduct['price'],
                'shouldSyncStock' => $this->shouldSyncStock
            ]);

            $attributeProduct->price = $priceDiference;
            $attributeProduct->update();
        }

        return true;
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    private function syncAttributeProductStock(): void
    {
        $stock = round($this->moloniProduct['stock']);

        $productToUpdate = $this->currentSyncAttributeProduct;

        /** Vamos buscar o stock actual do artigo */
        $stockCheck = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ .
            "stock_available WHERE id_product = '" . (int)$productToUpdate['id_product'] .
            "' and id_product_attribute = '" . (int)$productToUpdate['id_product_attribute'] . "'"
        );

        if ($stock === (float)$stockCheck['quantity']) {
            return;
        }

        /** O artigo tem atributos, vamos atualizar a quantidade do atributo */
        StockAvailable::setQuantity(
            (int)$productToUpdate['id_product'],
            (int)$productToUpdate['id_product_attribute'],
            (int)$stock
        );

        /** Atualizamos a quantidade de stock do artigo "pai" */
        $parent_stock = Db::getInstance()->ExecuteS(
            'SELECT * FROM ' . _DB_PREFIX_ .
            "stock_available WHERE id_product = '" . (int)$productToUpdate['id_product'] .
            "' and id_product_attribute > 0 ORDER BY id_shop ASC"
        );
        $parent_stock_qty = 0;

        foreach ($parent_stock as $key => $parent) {
            $parent_stock_qty += $parent['quantity'];
            if (isset($parent_stock[$key + 1]) && $parent['id_shop'] != $parent_stock[$key + 1]['id_shop']) {
                break;
            }
        }

        StockAvailable::setQuantity(
            (int)$productToUpdate['id_product'],
            0,
            (int)$parent_stock_qty
        );

        /** Verify if stock has been updated */
        $stockCheckAfter = Db::getInstance()->getRow(
            'SELECT quantity FROM ' . _DB_PREFIX_ .
            "stock_available WHERE id_product = '" . (int)$productToUpdate['id_product'] .
            "' and id_product_attribute = '" . (int)$productToUpdate['id_product_attribute'] . "'"
        );

        if ($stock !== (float)$stockCheckAfter['quantity']) {
            $this->addUpdateError([
                'stock_before' => $stockCheck['quantity'],
                'stock_after' => $stock,
                'stock_total' => $parent_stock_qty
            ]);
        } else {
            $this->addUpdateAttributes([
                'stock_before' => $stockCheck['quantity'],
                'stock_after' => $stock,
                'stock_total' => $parent_stock_qty,
                'shouldSyncStock' => $this->shouldSyncStock
            ]);
        }
    }

    /**
     * Update variant cover image
     *
     * @return void
     */
    private function syncAttributeImage(): void
    {
        if (empty($this->moloniProduct['image'])) {
            return;
        }

        $product = new Combination($this->currentSyncAttributeProduct['id_product_attribute']);

        new UpdatePrestaCombinationImage($product, $this->moloniProduct['image']);


        $this->addUpdateAttributes([
            'image_before' => 'Old image',
            'image_after' => 'New image'
        ]);
    }

    /** Simple product */

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function syncSimpleProduct(): void
    {
        if ((int)$this->moloniProduct['has_stock'] === 1 && $this->shouldSyncStock) {
            $this->syncSimpleProductStock();
        }

        if ($this->shouldSyncPrice) {
            $this->syncSimpleProductPrice();
        }

        if ($this->shouldSyncName || $this->shouldSyncDescription || $this->shouldSyncEAN) {
            $this->syncSimpleProductFields();
        }

        if ($this->shouldSyncTax) {
            $this->syncSimpleProductTax();
        }

        if ($this->shouldSyncImage) {
            $this->syncSimpleProductImage();
        }
    }

    /**
     * Atualiza um produto simples
     * Se esse produto tiver combinacoes atualiza a diferenca desses produtos
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function syncSimpleProductPrice(): void
    {
        $product = new Product($this->currentSyncProductId, true, Configuration::get('PS_LANG_DEFAULT'));

        $oldProductPrice = $this->getProductPriceById($this->currentSyncProductId);

        if ($oldProductPrice === (float)$this->moloniProduct['price']) {
            return;
        }

        $this->addUpdateSimple([
            'price_before' => $oldProductPrice,
            'price_after' => $this->moloniProduct['price']
        ]);

        $product->price = $this->moloniProduct['price'];
        $product->update();

        $hasProductAttributes = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . "product_attribute WHERE id_product = '" . pSQL($this->currentSyncProductId) . "'"
        );

        if (is_array($hasProductAttributes) && !empty($hasProductAttributes)) {
            foreach ($hasProductAttributes as $productAttribute) {
                $oldProductAttributePrice = $oldProductPrice + $productAttribute['price'];

                $attributeProductClass = new Combination(
                    $productAttribute['id_product_attribute']
                );

                if ($this->moloniProduct['price'] > $oldProductAttributePrice) {
                    $attributeProductClass->price = -1 * ($this->moloniProduct['price'] - $oldProductAttributePrice);
                } elseif ($this->moloniProduct['price'] < $oldProductAttributePrice) {
                    $attributeProductClass->price = $this->moloniProduct['price'] - $oldProductAttributePrice;
                } else {
                    $attributeProductClass->price = 0;
                }
                $attributeProductClass->update();
            }
        }
    }

    /**
     * Atualiza os campos (Nome , Descricao, EAN) de um produto simples
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    private function syncSimpleProductFields(): void
    {
        $product = new Product($this->currentSyncProductId, true, Configuration::get('PS_LANG_DEFAULT'));
        $changeFlag = false;

        /** Alterações de atributos e array de resultados */
        if ($this->shouldSyncName && $product->name !== $this->moloniProduct['name']) {
            $this->addUpdateSimple([
                'name_before' => $product->name,
                'name_after' => $this->moloniProduct['name']
            ]);
            $product->name = $this->moloniProduct['name'];
            $changeFlag = true;
        }

        if ($this->shouldSyncDescription && $product->description !== $this->moloniProduct['summary']) {
            $this->addUpdateSimple([
                'description_before' => $product->description,
                'description_after' => $this->moloniProduct['summary']
            ]);
            $product->description = $this->moloniProduct['summary'];
            $changeFlag = true;
        }

        if ($this->shouldSyncEAN && $this->isEan13Valid($this->moloniProduct['ean'])) {
            $ean = $this->moloniProduct['ean'] ?? '';

            if (!empty($ean) && $ean !== $product->ean13) {
                $this->addUpdateSimple([
                    'ean_before' => $product->ean13,
                    'ean_after' => $this->moloniProduct['ean']
                ]);
                $product->ean13 = $this->moloniProduct['ean'];
                $changeFlag = true;
            }
        }

        if ($changeFlag) {
            $product->update();
        }
    }

    private function syncSimpleProductStock(): void
    {
        $stock = round($this->moloniProduct['stock']);

        $stockCheck = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ .
            "stock_available WHERE id_product = '" . (int)$this->currentSyncProductId . "'");

        if ((int)$this->moloniProduct['has_stock'] === 1 && (float)$stockCheck['quantity'] !== $stock) {
            $this->addUpdateSimple([
                'stock_before' => $stockCheck['quantity'],
                'stock_after' => $stock
            ]);

            Db::getInstance()->update('stock_available', [
                'quantity' => $stock
            ], "id_product = '" . (int)$this->currentSyncProductId . "' and id_product_attribute = '0'");

            StockAvailable::setQuantity((int)$this->currentSyncProductId, 0, $stock);
        }
    }

    /**
     * Update prestashop product taxes
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function syncSimpleProductTax()
    {
        $product = new Product($this->currentSyncProductId, true, Configuration::get('PS_LANG_DEFAULT'));
        $changeFlag = false;

        $previousTaxRuleGroup = (int)$product->id_tax_rules_group;

        /** Set taxes */
        if (isset($this->moloniProduct['taxes']) && !empty($this->moloniProduct['taxes'])) {
            $moloniTax = $this->moloniProduct['taxes'][0]['tax'] ?? [];

            $newTaxRuleGroup = (new FindTaxGroupFromMoloniTax($moloniTax))->handle();
        } else {
            $newTaxRuleGroup = 0;
        }

        if ($previousTaxRuleGroup !== $newTaxRuleGroup) {
            $product->id_tax_rules_group = $newTaxRuleGroup;

            $changeFlag = true;
        }

        if ($changeFlag) {
            $this->addUpdateSimple([
                'tax_before' => (string)$previousTaxRuleGroup,
                'tax_after' => (string)$newTaxRuleGroup
            ]);

            $product->update();
        }
    }

    /**
     * Update product cover image
     *
     * @return void
     */
    private function syncSimpleProductImage(): void
    {
        if (empty($this->moloniProduct['image'])) {
            return;
        }

        new UpdatePrestaProductImage($this->currentSyncProductId, $this->moloniProduct['image']);

        $this->addUpdateSimple([
            'image_before' => 'Old image',
            'image_after' => 'New image'
        ]);
    }

    /** Gets */

    private function getAttributeProduct()
    {
        $result = Db::getInstance()->getRow(
            'SELECT id_product_attribute, id_product, price FROM ' .
            _DB_PREFIX_ .
            "product_attribute WHERE reference = '" . pSQL($this->moloniProduct['reference']) . "'"
        );

        return $result ?: false;
    }

    private function getProductIdByReference(): int
    {
        $result = Db::getInstance()->getRow(
            'SELECT id_product FROM ' . _DB_PREFIX_ . "product WHERE reference = '" . pSQL($this->moloniProduct['reference']) . "'"
        );

        return $result ? (int)$result['id_product'] : 0;
    }

    private function getProductPriceById($productId): float
    {
        $result = Db::getInstance()->getRow(
            'SELECT price FROM ' . _DB_PREFIX_ . "product WHERE id_product = '" . pSQL($productId) . "'"
        );

        return $result ? (float)$result['price'] : 0;
    }

    private function getProductById(int $id)
    {
        $result = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . "product WHERE id_product = '" . pSQL($id) . "'"
        );


        return $result ?: [];
    }

    /** Results methods */

    private function addHeader(): void
    {
        $this->updatedResult = [
            'header' => [
                'updated_since' => $this->date,
                'products_total' => $this->totalProducts
            ]
        ];
    }

    private function insertSuccess(array $array): void
    {
        $reference = $this->moloniProduct['reference'];
        $this->updatedResult['insert_success'][$reference]['reference'] = $reference;

        foreach ($array as $key => $value) {
            $this->updatedResult['insert_success'][$reference][$key] = $value;
        }
    }

    private function insertError(array $array): void
    {
        $reference = $this->moloniProduct['reference'];
        $this->updatedResult['insert_error'][$reference]['reference'] = $reference;

        foreach ($array as $key => $value) {
            $this->updatedResult['insert_error'][$reference][$key] = $value;
        }
    }

    private function addUpdateError(array $array): void
    {
        $reference = $this->moloniProduct['reference'];
        $this->updatedResult['update_error'][$reference]['reference'] = $reference;

        foreach ($array as $key => $value) {
            $this->updatedResult['update_error'][$reference][$key] = $value;
        }
    }

    private function addUpdateSimple(array $array): void
    {
        $reference = $this->moloniProduct['reference'];
        $this->updatedResult['simple'][$reference]['reference'] = $reference;

        foreach ($array as $key => $value) {
            $this->updatedResult['simple'][$reference][$key] = $value;
        }

    }

    private function addUpdateAttributes(array $array): void
    {
        $reference = $this->moloniProduct['reference'];
        $this->updatedResult['with_attributes'][$reference]['reference'] = $reference;

        foreach ($array as $key => $value) {
            $this->updatedResult['with_attributes'][$reference][$key] = $value;
        }
    }

    private function addFatalError(array $array): void
    {
        $reference = $this->moloniProduct['reference'];
        $this->updatedResult['fatal_error'][$reference]['reference'] = $reference;

        foreach ($array as $key => $value) {
            $this->updatedResult['fatal_error'][$reference][$key] = $value;
        }

    }

    /** Auxiliary */

    /**
     * @param string|int $value
     * @return bool
     */
    private function isEan13Valid($value)
    {
        /**
         * Valid ean regex pattern
         */
        $validPattern = '/^[0-9]{0,13}$/';

        /**
         * Maximum allowed symbols
         */
        $maxLength = 13;

        if (strlen($value) <= $maxLength && preg_match($validPattern, $value)) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    private function enableStockSync(): void
    {
        $this->shouldSyncStock = true;
    }

    /**
     * @return void
     */
    private function enablePriceSync(): void
    {
        $this->shouldSyncPrice = true;
    }

    /**
     * @return void
     */
    private function enableNameSync(): void
    {
        $this->shouldSyncName = true;
    }

    /**
     * @return void
     */
    private function enableDescriptionSync(): void
    {
        $this->shouldSyncDescription = true;
    }

    /**
     * @return void
     */
    private function enableEANSync(): void
    {
        $this->shouldSyncEAN = true;
    }

    /**
     * @return void
     */
    private function enableTaxSync(): void
    {
        $this->shouldSyncTax = true;
    }

    /**
     * @return void
     */
    private function enableImageSync(): void
    {
        $this->shouldSyncImage = true;
    }
}
