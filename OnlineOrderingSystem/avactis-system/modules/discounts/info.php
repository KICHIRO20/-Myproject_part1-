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
 * @package Discounts
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'Discounts', #
        'shortName'    => 'DISCOUNTS',
        'groups'       => '',
        'description'  => 'Discounts',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'discounts_api.php',
        'resFile'      => 'discounts-messages',

        'actions' => array
        (
            #                ,              action'
            #                           action' .
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'update_global_discounts' => 'update_global_discounts_action.php'
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'discounts_manage_global_discounts_az' => 'discounts_manage_global_discounts_az.php'
            ),
            'CustomerZone' => array
            (
            )
        )
    );
?>