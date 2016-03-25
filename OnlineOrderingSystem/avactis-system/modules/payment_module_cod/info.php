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
 * @package PaymentModuleCod
 * @author Egor Makarov
 */

$moduleInfo = array
    (
        'name'         => 'Payment_Module_Cod', # this is also a main class name
        'shortName'    => 'PM_COD',
        'groups'       => 'PaymentModule,Offline', //  change to "...,Offline in release"
        'description'  => '"Cash on delivery" method payment module. See payment documentation',
        'version'      => '0.1.47700',
        'author'       => 'Egor Makarov',
        'contact'      => '',
        'systemModule' => false,
        'mainFile'     => 'payment_module_cod_api.php',
        'resFile'      => 'payment-module-cod-messages',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'update_cod' => 'update_cod.php'
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'CheckoutPaymentModuleCodInputAZ' => 'cod_input_az.php',
            ),
            'CustomerZone' => array
            (
                'CheckoutPaymentModuleCodInput'  => 'cod_input_cz.php',
                'CheckoutPaymentModuleCodOutput' => 'cod_output_cz.php'
            )
        )
    );
?>