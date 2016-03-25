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
 * @package TaxExempts
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'TaxExempts', #
        'shortName'    => 'TAXEXEMPTS',
        'groups'       => '',
        'description'  => 'TaxExempts',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'tax_exempts_api.php',
        'resFile'      => 'tax-exempts-messages',
        'constantsFile'=> 'const.php',

        'actions' => array
        (
        ),
        'hooks' => array
        (
            # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
            #                              'Hook_File' => 'hook_file_name' )
            'ClaimFullTaxExemptHook' => array ( 'onAction'  => 'AddToCart,RemoveProductFromCart,ClearCart,UpdateCartContent,SetCurrStep,JSSetCurrStep',
//            'ClaimFullTaxExemptHook' => array ( 'onAction'  => 'UpdateCartContent',
                                                'Hook_File' => 'claim_full_tax_exempt.php' )
        ),
        'views' => array
        (
            'AdminZone' => array
            (
            ),
            'CustomerZone' => array
            (
                'FullTaxExemptForm' => "full_tax_exempt_form_cz.php"
            )
        )
    );
?>