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

use Configuration;
use Image;
use ImageManager;
use ImageType;
use Moloni\Enums\Domains;
use PrestaShopDatabaseException;
use Shop;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class PrestaImage
{
    protected $languageId;
    protected $moloniImagePath;

    public function __construct(string $moloniImagePath)
    {
        $this->languageId = Configuration::get('PS_LANG_DEFAULT');
        $this->moloniImagePath = $moloniImagePath;
    }

    /**
     * * Adapetd from Prestashop *
     *
     * @param Image $image
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @see \PrestaShop\PrestaShop\Adapter\Import\ImageCopier::copyImg
     */
    protected function saveImage(Image $image): void
    {
        $imageUrl = Domains::MOLONI_MEDIA_API . $this->moloniImagePath;
        $imageUrl = urldecode(trim($imageUrl));

        $tmpDir = Configuration::get('_PS_TMP_IMG_DIR_');
        $tmpFile = tempnam($tmpDir, 'ps_import');

        if (empty($tmpFile)) {
            $tmpFile = tempnam("/tmp", 'ps_import');
        }

        $path = $image->getPathForCreation();

        $origTmpfile = $tmpFile;

        if (Tools::copy($imageUrl, $tmpFile)) {
            if (!ImageManager::checkImageMemoryLimit($tmpFile)) {
                @unlink($tmpFile);

                return;
            }

            $targetWidth = $targetHeight = 0;
            $sourceWidth = $sourceHeight = 0;
            $error = 0;

            ImageManager::resize(
                $tmpFile,
                $path . '.jpg',
                null,
                null,
                'jpg',
                false,
                $error,
                $targetWidth,
                $targetHeight,
                5,
                $sourceWidth,
                $sourceHeight
            );

            $imagesTypes = ImageType::getImagesTypes('products', true);

            $pathInfos = [];
            $pathInfos[] = [$targetWidth, $targetHeight, $path . '.jpg'];

            foreach ($imagesTypes as $imageType) {
                $tmpFile = $this->getBestPath($imageType['width'], $imageType['height'], $pathInfos);

                if (ImageManager::resize(
                    $tmpFile,
                    $path . '-' . stripslashes($imageType['name']) . '.jpg',
                    $imageType['width'],
                    $imageType['height'],
                    'jpg',
                    false,
                    $error,
                    $targetWidth,
                    $targetHeight,
                    5,
                    $sourceWidth,
                    $sourceHeight
                )) {
                    // the last image should not be added in the candidate list if it's bigger than the original image
                    if ($targetWidth <= $sourceWidth && $targetHeight <= $sourceHeight) {
                        $pathInfos[] = [$targetWidth, $targetHeight, $path . '-' . stripslashes($imageType['name']) . '.jpg'];
                    }

                    $file = $tmpDir . 'product_mini_' . (int)$image->id . '.jpg';

                    if (is_file($file)) {
                        unlink($file);
                    }

                    $file = $tmpDir . 'product_mini_' . (int)$image->id . '_' . (int)Shop::getContextShopID() . '.jpg';

                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        } else {
            @unlink($origTmpfile);

            return;
        }

        unlink($origTmpfile);
    }

    /**
     * * Copied from Prestashop *
     *
     * @param int $targetWidth
     * @param int $targetHeight
     * @param array $pathInfos
     *
     * @return string
     * @see \PrestaShop\PrestaShop\Adapter\Import\ImageCopier::getBestPath
     */
    protected function getBestPath(int $targetWidth, int $targetHeight, array $pathInfos): string
    {
        $pathInfos = array_reverse($pathInfos);
        $path = '';

        foreach ($pathInfos as [$width, $height, $path]) {
            if ($width >= $targetWidth && $height >= $targetHeight) {
                return $path;
            }
        }

        return $path;
    }
}
