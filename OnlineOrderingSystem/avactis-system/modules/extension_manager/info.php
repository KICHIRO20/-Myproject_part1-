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
 * @package Extension Manager
 * @author Alexander Girin
 */

$moduleInfo = array
(
    'name'         => 'Extension_Manager',
    'shortName'    => 'ExtManager',
    'groups'       => 'Main',
    'description'  => 'Module for managing Extensions and Core upgrade',
    'version'      => '0.1.47700',
    'author'       => 'Gaurav Joshi',
    'contact'      => '',
    'systemModule' => false,
    'mainFile'     => 'extension_manager_api.php',
    'resFile'      => 'extension-manager',
    'actions' => array
    (
	'AdminZone' => array(
       		'UninstallExtensionAction' 	=> 'uninstall_extension.php',
		'ActivateDeactivateExtension'	=> 'active_deactive_extension.php',
		),
		'GetCoreUpgradeFile'		=> 'get_core_upgrade_file.php',
        	'GetMarketPlaceExtension' => 'get_marketplace_extension.php',
    ),

    'hooks' => array
    (
    ),

    'views' => array
    (
        'AdminZone' => array
        (
        	'ExtensionManager_ListView'		=> 'extension_list_az.php',
		'NotifyCoreUpgrade' 			=> 'notify_core_upgrade.php',
	       	'ExtensionManager_Manage'		=> 'extension_manage_az.php',
		'ExtensionDetails'			=> 'extension_details_az.php',
        ),
        'CustomerZone' => array
        (

        ),
        'Aliases' => array(

        )
    )
);
?>