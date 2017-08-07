<?php
/**
 * 2013-2016 Nosto Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@nosto.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2013-2016 Nosto Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Helper class for managing images.
 *
 */
class NostoTaggingHelperImage
{
    const OPTIMAL_PRODUCT_IMAGE_WIDTH = 800;

    /**
     * Returns all image types configured to be available for product images
     *
     * @return array
     */
    public function getProductImageTypes()
    {
        $product_images = array();
        $image_types = ImageType::getImagesTypes();
        foreach ($image_types as $image_type) {
            if (!empty($image_type['products']) && $image_type['products'] == 1) {
                $product_images[] = $image_type;
            }
        }

        return $product_images;
    }

    /**
     * Chooses the "optimal" image type to use for product image urls.
     *
     * The type is chosen based on which image type has a width closest to
     * `self::OPTIMAL_PRODUCT_IMAGE_WIDTH`.
     *
     * @return false|string the image type name or false if not found.
     */
    public static function chooseOptimalImageType()
    {
        $definition = ObjectModel::getDefinition('ImageType');
        $table_name = isset($definition['table']) ? $definition['table'] : 'image_type';
        $available_image_types = Db::getInstance()->executeS('
			SELECT * FROM `' . pSQL(_DB_PREFIX_ . $table_name) . '`
			WHERE `products` = 1
			ORDER BY `width` ASC
		');
        $optimal = self::OPTIMAL_PRODUCT_IMAGE_WIDTH;
        $found = array();
        foreach ($available_image_types as $available) {
            if (empty($found) || abs($optimal - (int)$found['width']) > abs((int)$available['width'] - $optimal)) {
                $found = $available;
            }
        }
        return isset($found['name']) ? $found['name'] : false;
    }

    /**
     * Returns the image type to be used in product image tagging
     *
     * @param $id_lang
     * @param null $id_shop_group
     * @param null $id_shop
     * @return string
     */
    public static function getTaggingImageTypeName($id_lang, $id_shop_group = null, $id_shop = null)
    {
        /* @var NostoTaggingHelperConfig $helper_config */
        $helper_config = Nosto::helper('nosto_tagging/config');
        $saved_image_type_id = $helper_config->getImageType($id_lang, $id_shop_group, $id_shop);
        if ($saved_image_type_id) {
            $image_type = new ImageType($saved_image_type_id);
            $image_type_name = $image_type->name;
        } else {
            $image_type_name = NostoTaggingHelperImage::chooseOptimalImageType();
        }

        return $image_type_name;
    }
}
