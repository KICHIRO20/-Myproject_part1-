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
 * Resources module meta info.
 *
 * @package Resources
 * @author Ravil Garafutdinov
 */

$moduleInfo = array
    (
        'name'         => 'Resources', # this is also a main class name
        'shortName'    => 'RES',
        'groups'       => 'Main',
        'description'  => 'Resources module',
        'version'      => '0.1.47700',
        'author'       => 'Ravil Garafutdinov',
        'contact'      => '',
        'systemModule' => false,
        'constantsFile' => 'const.php',
        'mainFile'     => 'resources_api.php',

        'actions' => array
        (
            # We suppose, the action name  matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
//            'AdminZone' => array(
//                'UpdateDateTimeFormat'       => 'update_date_time_format_action.php'
//           ),
        ),

        'hooks' => array
        (
            # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
            #                              'Hook_File' => 'hook_file_name' )
        ),

        'views' => array
        (
//            'AdminZone' => array
//            (
//                'CurrencySettings'       => 'currency_settings_az.php'
//            ),
//            'CustomerZone' => array
//            (
//            )
        )
    );
?>