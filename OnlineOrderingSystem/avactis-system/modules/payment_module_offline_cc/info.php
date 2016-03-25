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
 * @package PaymentModuleOfflineCC
 * @author Vadim Lyalikov
 */

$moduleInfo = array
    (
        'name'         => 'Payment_Module_Offline_CC', # this is also a main class name
        'shortName'    => 'PM_O_CC',
        'groups'       => 'PaymentModule,Offline',
        'description'  => 'OfflineCC payment module. See payment documentation',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule' => false,
        'mainFile'     => 'payment_module_offline_cc_api.php',
        'resFile'      => 'payment-module-offline-messages',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'update_offline_cc' => 'update_offline_cc.php',
                'replace_rsa_key_pair_step1_prepare_server_tmp_data' => 'replace_rsa_key_pair_step1_prepare_server_tmp_data_action.php',
                'replace_rsa_key_pair_step2_reencrypt_tmp_data' => 'replace_rsa_key_pair_step2_reencrypt_tmp_data_action.php',
                'replace_rsa_key_pair_step5_replace_old_encrypted_data_with_new_reencrypted_tmp_data' => 'replace_rsa_key_pair_step5_replace_old_encrypted_data_with_new_reencrypted_tmp_data_action.php',
                'generate_rsa_key_pair_in_php' => 'generate_rsa_key_pair_in_php_action.php'
            ),
            'save_rsa_public_key' => 'save_rsa_public_key.php',
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'offline_cc_input_az' => 'offline_cc_input_az.php',
            ),
            'CustomerZone' => array
            (
                'CheckoutPaymentModuleOfflineCCInput'  => 'offline_cc_input_cz.php',
                'CheckoutPaymentModuleOfflineCCOutput' => 'offline_cc_output_cz.php'
            )
        )
    );
?>