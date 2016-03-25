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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 */

$moduleInfo = array
    (
        'name'         => 'Product_Options',
        'shortName'    => 'PO',
        'groups'       => 'Main',
        'description'  => 'Product Options module',
        'version'      => '0.5.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'product_options_api.php',
        'resFile'      => 'product-options-messages',

        'actions' => array
        (
            'AdminZone' => array(
                'add_option_to_entity'     => 'add_option_to_entity.php'
               ,'del_option_from_entity'   => 'del_option_from_entity.php'
               ,'update_option_of_entity'  => 'update_option_of_entity.php'
               ,'add_value_to_option'      => 'add_value_to_option.php'
               ,'del_values_from_option'   => 'del_values_from_option.php'
               ,'update_values_of_option'  => 'update_values_of_option.php'
               ,'update_options_settings'  => 'update_options_settings.php'
               ,'add_inv_record_to_entity' => 'add_inv_record_to_entity.php'
               ,'del_inv_records_from_entity' => 'del_inv_records_from_entity.php'
               ,'update_inventory'         => 'update_inventory.php'
               ,'rebuild_inventory'        => 'rebuild_inventory.php'
               ,'clear_inventory'          => 'clear_inventory.php'
               ,'get_inventory_page'       => 'get_inventory_page.php'
               ,'add_crule_to_entity'      => 'add_crule_to_entity.php'
               ,'del_crules_from_entity'   => 'del_crules_from_entity.php'
               ,'get_crules_list'          => 'get_crules_list.php'
           ),
           'get_uploaded_file'        => 'get_uploaded_file.php'
           ,'update_options_sort'      => 'update_options_sort.php'
           ,'update_values_sort'       => 'update_values_sort.php'

        ),

        'hooks' => array
        (
            'DeleteAllOptionsFromEntities' => array (
                                    'onAction' => 'ConfirmDeleteProducts,ConfirmDeleteCategory,ConfirmDeleteProductTypes'
                                   ,'Hook_File' => 'delete_all_options_from_entities.php'
                                )
           ,'CopyAllOptionsFromEntityToEntity' => array(
                                    'onAction' => 'CopyToProducts,AddProductInfoAction'
                                   ,'Hook_File' => 'copy_all_options_from_entity_to_entity.php'
                                )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'PO_OptionsList'    => 'po_options_list_az.php'
               ,'PO_AddOption'      => 'po_add_option_az.php'
               ,'PO_EditOption'     => 'po_edit_option_az.php'
               ,'PO_InvPage'        => 'po_inv_page_az.php'
               ,'PO_CRulesEditor'   => 'po_crules_editor_az.php'
               ,'PO_CRulesList'     => 'po_crules_list_az.php'
               ,'PO_InvEditor'      => 'po_inv_editor_az.php'
               ,'PO_CheckCRules'    => 'po_check_crules_az.php'
            ),
            'CustomerZone' => array(
                'OptionsChoice'      => 'options-choice-cz.php'
               ,'OptionsCombination' => 'options-combination-cz.php'
               ,'ProductOptionsWarnings'    => 'options-warnings-cz.php'
            ),
            'Aliases' => array(
                 'OptionsChoise' => 'OptionsChoice'
            )
        )
    );
?>