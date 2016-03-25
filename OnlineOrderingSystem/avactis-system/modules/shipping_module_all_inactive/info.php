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
 * @package ShippingModuleAllInactive
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'Shipping_Module_All_Inactive', # this is also a main class name
        'shortName'    => 'SMAIA',
        'groups'       => 'ShippingModule',
        'description'  => 'All Inactive shipping module. See shipping documentation',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'shipping_module_all_inactive_api.php',
        'resFile'      => 'shipping-module-all-inactive-messages',

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