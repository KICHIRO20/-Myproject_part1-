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
 * @package WishList
 * @author Sergey Kulitsky
 *
 */
$moduleInfo = array
    (
        'name'          => 'WishList',
        'shortName'     => 'WL',
        'groups'        => 'Main',
        'description'   => 'WishList module',
        'version'       => '0.1.47700',
        'author'        => 'Sergey Kulitsky',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'wishlist_api.php',
        'resFile'       => 'wishlist-messages',
        'actions' => array
        (
            'CustomerZone' => array(
                'AddToWishlist' => 'wl_add_to_wishlist_cz.php',
                'UpdateWishlistContent' => 'wl_update_wishlist_content_cz.php',
                'RemoveProductFromWishlist' => 'wl_remove_from_wishlist_cz.php',
                'SendWishlist' => 'wl_send_wishlist_cz.php'
            ),
        ),
        'views' => array
        (
            'CustomerZone' => array(
                'AddToWishlistButton' => 'wl_add_to_wishlist_button_cz.php',
                'WishlistContent'     => 'wl_wishlist_content_cz.php',
                'SendWishlistContent' => 'wl_send_wishlist_content_cz.php'
            ),
        )
    );
?>