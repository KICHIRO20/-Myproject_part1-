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
 * License module meta info.
 *
 * @package License
 * @author Alexander Girin
 */

$moduleInfo = array
    (
        'name'         => 'License', #
        'shortName'    => 'LICENSE',
        'groups'       => 'Main',
        'description'  => 'License module',
        'version'      => '0.1.47700',
        'author'       => 'Alexander Girin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'license_api.php',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array (
                'UpdateLicenseKey'         => 'update_license_key_action.php',
                'RegisterStore'            => 'register_store_action.php',
                'UpdateStoreRegistration'  => 'update_store_registration_action.php'
            ),
        ),

        'hooks' => array
        (
            # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
            #                              'Hook_File' => 'hook_file_name' )
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'LicenseInfo'            => 'license_info_az.php'
               ,'LicenseMention'         => 'license_mention_az.php'
               #,'LicenseCheck'           => 'license_check_az.php'
            ),
            'CustomerZone' => array
            (
            )
        )
    );
?>