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
 * Localization module meta info.
 *
 * @package Localization
 * @author Alexey Florinsky
 */

$moduleInfo = array
    (
        'name'         => 'Localization', # this is also a main class name
        'shortName'    => 'LOCAL',
        'groups'       => 'Main',
        'description'  => 'Localization module',
        'version'      => '0.1.47700',
        'author'       => 'Alexey Florinsky',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile' => 'const.php',
        'mainFile'     => 'localization_api.php',

        'actions' => array
        (
            # We suppose, the action name  matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'UpdateDateTimeFormat'       => 'update_date_time_format_action.php'
               ,'UpdateNumberFormat'         => 'update_number_format_action.php'
               ,'UpdateCurrencyFormat'       => 'update_currency_format_action.php'
               ,'UpdateAcceptedCurrencies'   => 'update_accepted_currencies_action.php'
               ,'UpdateWeightUnit'           => 'update_weight_unit_action.php'
           ),
           'SetDisplayCurrency'         => 'set_display_currency_action.php'
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
                'CurrencySettings'       => 'currency_settings_az.php'
               ,'AcceptedCurrencies'     => 'accepted_currencies_az.php'
               ,'DateTimeSettings'       => 'date_settings_az.php'
               ,'NumberSettings'         => 'number_settings_az.php'
               ,'WeightSettings'         => 'weight_settings_az.php'
            ),
            'CustomerZone' => array
            (
                'CurrencySelector'       => 'currency_selector_cz.php'
            )
        )
    );
?>