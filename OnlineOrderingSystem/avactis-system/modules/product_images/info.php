<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php

/**
 * @package ProductImages
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'         => 'Product_Images',
        'shortName'    => 'PI',
        'groups'       => 'Main',
        'description'  => 'Product Images module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'product_images_api.php',
        'resFile'      => 'product-images-messages',

        'actions' => array
        (
            'AdminZone' => array(
                'upload_image_for_preview' => 'upload_image_for_preview.php'
               ,'add_image_to_product' => 'add_image_to_product.php'
               ,'del_images_from_product' => 'del_images_from_product.php'
               ,'update_images_of_product' => 'update_images_of_product.php'
               ,'update_pi_settings' => 'update_pi_settings.php'
               ,'update_imgs_sort_order' => 'update_imgs_sort_order.php'
           ),
        ),

        'hooks' => array
        (
            'DeleteAllImagesFromProduct' => array(
                                    'onAction' => 'ConfirmDeleteProducts,ConfirmDeleteCategory'
                                   ,'Hook_File' => 'delete_all_images_from_products.php'
                            )
           ,'CopyAllImagesFromProductToProduct' => array(
                                    'onAction' => 'CopyToProducts'
                                   ,'Hook_File' => 'copy_all_images_from_product_to_product.php'
                                 )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'PI_ImagesList' => 'images_list_az.php'
               ,'PI_Settings' => 'settings_az.php'
            ),
            'CustomerZone' => array(
                'ProductDetailedImages' => 'images_block_cz.php'
            )
        )
    );

?>