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

use Image;
use PrestaShopDatabaseException;
use PrestaShopException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdatePrestaProductImage extends PrestaImage
{
    protected $prestashopProductId;

    public function __construct(int $prestashopProductId, string $moloniImagePath)
    {
        parent::__construct($moloniImagePath);

        $this->prestashopProductId = $prestashopProductId;

        $this->handle();
    }

    private function handle(): void
    {
        if (empty($this->moloniImagePath)) {
            return;
        }

        /** @var array|null $coverImage */
        $coverImage = Image::getCover($this->prestashopProductId);

        if (!empty($coverImage)) {
            $image = new Image($coverImage['id_image'], $this->languageId);
            $image->deleteImage();
        } else {
            $image = new Image(null, $this->languageId);
            $image->cover = true;
            $image->id_product = $this->prestashopProductId;

            try {
                $image->save();
            } catch (PrestaShopException $e) {
                return;
            }
        }

        try {
            $this->saveImage($image);
        } catch (PrestaShopDatabaseException $e) {
            return;
        }
    }
}
