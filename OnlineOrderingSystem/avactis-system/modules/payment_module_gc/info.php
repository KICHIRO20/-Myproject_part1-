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
 * @package PaymentModuleGc
 * @author Ravil Garafutdinov
 */

$moduleInfo = array
    (
        'name'         => 'Payment_Module_Gc', # this is also a main class name
        'shortName'    => 'PM_GC_OFF',
        'groups'       => 'PaymentModule', //      change to "...,Offline in release"
        'description'  => '"Gift Certificate" substitution payment method payment module.',
        'version'      => '1.0.47700',
        'author'       => 'Ravil Garafutdinov',
        'contact'      => '',
        'systemModule' => false,
        'mainFile'     => 'payment_module_gc_api.php',
        'resFile'      => 'payment-module-gc-messages',

        'actions' => array
        (
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
                'CheckoutPaymentModuleGCInput'  => 'gc_input_cz.php',
                'CheckoutPaymentModuleGCOutput' => 'gc_output_cz.php'
            )
        )
   );
?>