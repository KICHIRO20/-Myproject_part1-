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
 * Cart module meta info.
 *
 * @package Cart
 * @author Alexander Girin
 */

$moduleInfo = array
(
    'name'         => 'Cart',
    'shortName'    => 'CART',
    'groups'       => 'Main',
    'description'  => '',
    'version'      => '0.1.47700',
    'author'       => 'Alexander Girin',
    'contact'      => '',
    'systemModule' => false,
    'mainFile'     => 'cart_api.php',
    'resFile'      => 'bookmarks-messages',

    'actions' => array
    (
        'AddToCart'             => 'addtocart_action.php'
       ,'RemoveProductFromCart' => 'removefromcart_action.php'
       ,'ClearCart'             => 'clearcart_action.php'
       ,'UpdateCartContent'     => 'updatequantityincart_action.php'
    ),

    'hooks' => array
    (
    ),

    'views' => array
    (
        'AdminZone' => array
        (
        ),
        'CustomerZone' => array
        (
            'ShoppingCart'   => 'cart_content.php',
            'CartThumbnail' => 'cart_thumbnail.php',
        ),
        'Aliases' => array(
             'MiniCart' => 'ShoppingCart',
             'CartPreview' => 'ShoppingCart'
        )
    )
);
?>