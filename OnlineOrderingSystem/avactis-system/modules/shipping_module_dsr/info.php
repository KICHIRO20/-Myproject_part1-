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
 * @package ShippingModuleDSR
 * @author Egor V. Derevyankin
 */

$moduleInfo = array
    (
        'name'         => 'Shipping_Module_DSR',
        'shortName'    => 'DSR',
        'groups'       => 'ShippingModule,Offline',
        'description'  => 'Custom Shipping Rates shipping module. See shipping documentation',
        'version'      => '0.1.47700',
        'author'       => 'Ravil Garafutdinov, Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'shipping_module_dsr_api.php',
        'resFile'      => 'shipping-module-dsr-messages',

        'actions' => array
        (
            'AdminZone' => array(
                'update_dsr_settings' => 'update_dsr_settings_action.php'
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'dsr_input_az' => 'dsr_input_az.php',
            ),
            'CustomerZone' => array
            (
            )
        )
    );
?>