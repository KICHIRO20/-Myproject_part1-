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
 * Modules_Manager module meta info.
 *
 * @package Modules_Manager
 * @author Alexey Kolesnikov
 */

$moduleInfo = array
    (
        'name'         => 'Modules_Manager', # this is also a main class name
        'shortName'    => 'MODMAN',
        'groups'       => 'Main',
        'description'  => 'Modules_Manager module description',
        'version'      => '0.1.47700',
        'author'       => 'Alexey Kolesnikov',
        'contact'      => '',
        'systemModule' => true,
        'mainFile'     => 'mm_api.php',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'

//            'mm_InstallAction'   => 'install.php',
//            'mm_UninstallAction' => 'uninstall.php'
            'AdminZone' => array(
                'ReinstallModuleAction' => 'reinstall.php',
                'reload_resources' => 'reload_resources.php',
                'combine_php' => 'combine_php.php',
                'reinstall_module' => 'reinstall_module.php',
            ),
            'ActionIsNotSetAction' => 'action_is_not_set.php',

        ),

        'hooks' => array
        (
//            'hook_name' => array ( 'onAction'  => 'Catalog_SetCurrCat',
//                                   'Hook_File' => 'file_name' )
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