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
 * @package RelatedProducts
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'         => 'Related_Products',
        'shortName'    => 'RP',
        'groups'       => 'Main',
        'description'  => 'Related Products module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'related_products_api.php',
        'resFile'      => 'related-products-messages',

        'actions' => array
        (
            'AdminZone' => array(
                'save_rp_links' => 'save_rp_links.php'
            ),
        ),

        'hooks' => array
        (
            'DeleteAllRPLinksFromProducts' => array (
                                    'onAction' => 'ConfirmDeleteProducts,ConfirmDeleteCategory'
                                   ,'Hook_File' => 'delete_all_rp_links_from_products.php'
                                )
           ,'CopyAllRPLinksFromProductToProduct' => array(
                                    'onAction' => 'CopyToProducts'
                                   ,'Hook_File' => 'copy_all_rp_links_from_product_to_product.php'
                                 )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'RP_LinksList'    => 'rp_links_list_az.php'
            ),
            'CustomerZone' => array(
                'RelatedProducts' => 'related_products_cz.php'
            )
        )
    );

?>