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
 * Configuration module meta info.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */

$moduleInfo = array
    (
        'name'         => 'Configuration', # this is also a main class name
        'shortName'    => 'CFG',
        'groups'       => 'Main',
        'description'  => 'Configuration module',
        'version'      => '0.1.47700',
        'author'       => 'Alexey Florinsky',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'configuration_api.php',
        'constantsFile'=> 'const.php',
		'resFile'       => 'configuration-messages',
        'extraAPIFiles' => array(
            'Settings' => 'abstract/settings.php'
        ),

        'actions' => array
        (
            # We suppose the action name matches
            # the class name of this a tion.
            # 'action_class_name' => 'action_file_name'
            'AdminZone'    => array(
                'UpdateGeneralSettings' => 'update-general-settings-action.php',
                'UpdateStoreOwnerProfile' => 'update-store-owner-profile-action.php',
                'ClearInstanceAjax' => 'clear-instance-ajax-action.php',
                'UpdateCacheSettings' => 'update-cache-settings-action.php',
                'UpdateCreditCardSettings' => 'update-credit-card-settings-action.php',
        		"UpdateCreditCardAttributes" => 'update-credit-card-attributes-action.php',
                'UpdateApplicationSettings' => 'update-application-settings-action.php',
                'UpdateMailSettings' => 'update-mail-settings-action.php'
            ),
            'ClearCache' => 'clear-cache-action.php',
            'SetSupportMode' => 'set-support-mode-action.php',
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
                'GeneralSettings'         => 'general_settings_az.php'
               ,'StoreOwner'              => 'store_owner_az.php'
               ,'CacheSettings'           => 'cache_settings_az.php'
               ,'CreditCardSettings'      => 'credit_card_settings_az.php'
               ,'SortCreditCardTypes'     => 'sort_credit_card_types_az.php'
               ,'CreditCardAttributes'	  => 'credit_card_attributes_az.php'
               ,'SettingGroupList'        => 'settings_groups_az.php'
               ,'SettingParamList'        => 'settings_params_az.php'
               ,'MailParamList'           => 'mail_params_az.php'
               ),
            'CustomerZone' => array
            (
            )
        )
    );
?>