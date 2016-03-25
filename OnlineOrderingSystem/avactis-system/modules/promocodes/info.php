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
 * @package PromoCodes
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'PromoCodes', #
        'shortName'    => 'PROMOCODES',
        'groups'       => '',
        'description'  => 'PromoCodes',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'promo_codes_api.php',
        'resFile'      => 'promo-codes-messages',
        'constantsFile'=> 'const.php',

        'actions' => array
        (
            #                ,              action'
            #                           action' .
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'AddPromoCodeInfo'        => 'add_promo_code_info_action.php',
                'SetEditablePromoCode'    => 'set_editable_promo_code_action.php',
                'UpdatePromoCodeInfo'     => 'update_promo_code_info_action.php',
                'UpdatePromoCodeArea'     => 'update_promo_code_area_action.php',
                'DelPromoCodeInfo'        => 'del_promo_code_info_action.php'
            ),
            'AddPromoCode'          => 'add_promo_code_action.php'
            ,'RemovePromoCode'       => 'remove_promo_code_action.php'
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'PromoCodesNavigationBar' => 'promo_codes_manage_promo_codes_az.php',
                'AddPromoCode' => 'add_promo_code_az.php',
                'EditPromoCode' => 'edit_promo_code_az.php',
                'EditPromoCodeArea' => 'edit_promo_code_area_az.php'
            ),
            'CustomerZone' => array
            (
                'PromoCodeForm' => "promo_code_form_cz.php"
            )
        )
    );
?>