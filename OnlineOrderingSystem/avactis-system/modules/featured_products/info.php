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
 * @package FeaturedProducts
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'         => 'Featured_Products',
        'shortName'    => 'FP',
        'groups'       => 'Main',
        'description'  => 'Featured Products module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'featured_products_api.php',
        'resFile'      => 'featured-products-messages',

        'actions' => array
        (
            'AdminZone' => array(
                'save_fp_links' => 'save_fp_links.php'
            ),
        ),

        'hooks' => array
        (
            'DeleteAllFPLinksFromCategories' => array (
                                    'onAction' => 'ConfirmDeleteProducts,ConfirmDeleteCategory'
                                   ,'Hook_File' => 'delete_all_fp_links_from_categories.php'
                                )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'FP_LinksList'    => 'fp_links_list_az.php'
            ),
            'CustomerZone' => array(
                'FeaturedProducts' => 'featured_products_cz.php'
            )
        )
    );

?>