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
 * @package PaymentModuleAllInactive
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'Payment_Module_All_Inactive', # this is also a main class name
        'shortName'    => 'PM_AI',
        'groups'       => 'PaymentModule',
        'description'  => 'AllInactive payment module. See payment documentation',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule' => false,
        'mainFile'     => 'payment_module_all_inactive_api.php',
        'resFile'      => 'payment-module-all-inactive-messages',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
            ),
            'CustomerZone' => array
            (
                'CheckoutPaymentModuleAllInactiveInput'  => 'all_inactive_input_cz.php',
                'CheckoutPaymentModuleAllInactiveOutput' => 'all_inactive_output_cz.php'
            )
        )
    );
?>