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
 * @package ShippingModuleFreeShipping
 * @author Ravil Garafutdinov
 */

$moduleInfo = array
    (
        'name'         => 'Shipping_Module_Free_Shipping', # this is also a main class name
        'shortName'    => 'SMFSH',
        'groups'       => 'ShippingModule,Offline',
        'description'  => 'Free Shipping emulation shipping module.',
        'version'      => '0.1.47700',
        'author'       => 'Ravil Garafutdinov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'shipping_module_free_shipping_api.php',
        'resFile'      => 'shipping-module-free-shipping-messages',

        'actions' => array
        (
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
            )
        )
    );
?>