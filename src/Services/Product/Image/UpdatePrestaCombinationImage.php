<?php
/**
 * 2023 - Moloni.com
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
 * @author    Moloni
 * @copyright Moloni
 * @license   https://creativecommons.org/licenses/by-nd/4.0/
 *
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

namespace Moloni\Services\Product\Image;

use Shop;
use Image;
use Combination;
use PrestaShopException;
use PrestaShopDatabaseException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdatePrestaCombinationImage extends PrestaImage
{
    protected $prestashopCombination;

    public function __construct(Combination $prestashopCombination, string $moloniImagePath)
    {
        parent::__construct($moloniImagePath);

        $this->prestashopCombination = $prestashopCombination;

        $this->handle();
    }

    private function handle()
    {
        if (empty($this->moloniImagePath)) {
            return;
        }

        $shopId = (int)Shop::getContextShopID();
        $combinationImage = Image::getBestImageAttribute($shopId, $this->languageId, $this->prestashopCombination->id_product, $this->prestashopCombination->id);

        if (!empty($combinationImage)) {
            $image = new Image((int)$combinationImage['id_image'], $this->languageId);
            $image->deleteImage(true);
        } else {
            $image = new Image(null, $this->languageId);
            $image->cover = false;
            $image->id_product = $this->prestashopCombination->id_product;

            try {
                $image->save();
            } catch (PrestaShopException $e) {
                return;
            }

            $this->prestashopCombination->setImages([$image->id]);
        }

        try {
            $this->saveImage($image);
        } catch (PrestaShopDatabaseException $e) {
            return;
        }
    }
}
