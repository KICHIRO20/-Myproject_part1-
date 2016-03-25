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
 * @package PaymentModulePaypal
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'Payment_Module_Paypal_CC', # this is also a main class name
        'shortName'    => 'PM_PP_CC',
        'groups'       => 'PaymentModule,OnlineCC',
        'description'  => 'PaypalCC payment module. See payment documentation',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule' => false,
        'mainFile'     => 'payment_module_paypal_cc_api.php',
        'resFile'      => 'payment-module-paypal-messages',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array (
                'update_paypal' => 'update_paypal.php'
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'paypal_cc_input_az' => 'paypal_cc_input_az.php',
            ),
            'CustomerZone' => array
            (
                'CheckoutPaymentModulePaypalCCInput'  => 'paypal_cc_input_cz.php',
                'CheckoutPaymentModulePaypalCCOutput' => 'paypal_cc_output_cz.php'
            )
        )
    );
?>