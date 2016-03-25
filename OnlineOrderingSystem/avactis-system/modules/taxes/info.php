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
 * Taxes module meta info.
 *
 * @package Taxes
 * @author Alexander Girin
 */

$moduleInfo = array
    (
        'name'         => 'Taxes', # this is also a main class name
        'shortName'    => 'TAXES',
        'groups'       => 'Main',
        'description'  => 'Taxes module',
        'version'      => '0.1.47700',
        'author'       => 'Alexander Girin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'taxes_api.php',
        'constantsFile'=> 'const.php',
        'extraAPIFiles' => array(
            'Taxes_AZ' => 'includes/taxes_api_az.php'
        ),

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name
            'AdminZone' => array(
                'AddProdTaxClass' => 'addprodtaxclass_action.php'
               ,'UpdateProdTaxClass' => 'updateprodtaxclass_action.php'
               ,'DeleteProdTaxClass' => 'deleteprodtaxclass_action.php'
               ,'AddTaxNameAction' => 'addtaxname_action.php'
               ,'UpdateTaxNameAction' => 'updatetaxname_action.php'
               ,'DeleteTaxNameAction' => 'deletetaxname_action.php'
               ,'SetEditableTaxId' => 'seteditabletaxid_action.php'
               ,'SetTaxClassId' => 'settaxclassid_action.php'
               ,'AddTaxDisplayOptionAction' => 'addtaxdisplayoption_action.php'
               ,'UpdateTaxDisplayAction' => 'updatetaxdisplayoption_action.php'
               ,'DeleteTaxDisplayOptionAction' => 'deletetaxdisplayoption_action.php'
               ,'AddTaxRateAction' => 'addtaxrate_action.php'
               ,'UpdateTaxRateAction' => 'updatetaxrate_action.php'
               ,'DeleteTaxRateAction' => 'deletetaxrate_action.php'
            ),
           'AddFromCatalog' => 'addprodtaxclassfromcatalog_action.php'
           ,'TaxCalculateAction' => 'taxcalculator_action.php'
           ,'SetShippingTaxes' => 'setshippingtaxes_action.php'
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
                'ProductTaxClassSettings'         => 'prod-tax-class-settings-az.php',
                'TaxSettings'                     => 'tax-settings-az.php',
                'AddTaxName'                      => 'tax-settings-add-name-az.php',
                'EditTaxName'                     => 'tax-settings-edit-name-az.php',
                'AddTaxDisplayOption'             => 'tax-settings-add-display-az.php',
                'EditTaxDisplayOption'            => 'tax-settings-edit-display-az.php',
                'AddTaxClass'                     => 'tax-settings-add-class-az.php',
                'EditTaxClass'                    => 'tax-settings-edit-class-az.php',
                'AddTaxRate'                      => 'tax-settings-add-rate-az.php',
                'EditTaxRate'                     => 'tax-settings-edit-rate-az.php',
                'TaxCalculator'                   => 'tax-calculator-az.php',
                'ShippingModulesListForTaxes'     => 'sm-list-az.php'
            ),
            'CustomerZone' => array
            (
            )
        )
    );
?>