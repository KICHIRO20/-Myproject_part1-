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
 * Payment Module.
 *
 * Reencrypt temporary data on the server. The step of replacing RSA keys.
 *
 *
 * @package PaymentModuleOffline CC
 * @access  public
 * @author Vadim Lyalikov
 */
class replace_rsa_key_pair_step2_reencrypt_tmp_data
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function replace_rsa_key_pair_step2_reencrypt_tmp_data()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        //
        //get arrived data
        $request = $application->getInstance('Request');

        //Old RSA private key:
        $old_rsa_private_key_asc_format = $request->getValueByKey('old_rsa_private_key_asc_format');
        if(empty($old_rsa_private_key_asc_format))
        {
            //The key didn't come. It's empty.
            //: report error
            $MessageResources = new MessageResources("payment-module-offline-messages", "AdminZone");
            $msg = $MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_009');
            echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$msg."');</script>";
            exit();
        }

        $old_rsa_private_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_private_key_from_asc_into_cryptrsa_format", $old_rsa_private_key_asc_format);

        if($old_rsa_private_key_cryptrsa_format === false)
        {
            //The key format is invalid.
            //: report error
             $MessageResources = &$application->getInstance('MessageResources');
             $msg = $MessageResources->getMessage('CRYPTO_RSA_PUBLIC_PRIVATE_KEYS_MISMATCH_DECRYPT_ERROR');
             echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$msg."');</script>";
            exit();
        }
        //New RSA public key:
        $new_rsa_public_key_asc_format = $request->getValueByKey('rsa_replacement_public_key_asc_format');
        $new_rsa_public_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $new_rsa_public_key_asc_format);
        if($new_rsa_public_key_cryptrsa_format === false)
        {
            //The key format is invalid.
            //: report error
             $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
             $msg = $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_008');
             echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$msg."');</script>";
            exit();
        }

        /**
         * Check if loaded old private key, which is used to decrypt data,
         * matches the public key existing in the system, which in fact was used
         * to encrypt data.
         */

        $old_rsa_public_key_cryptrsa_format = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInCryptRSAFormat");
        if(modApiFunc("Crypto", "rsa_do_public_key_match_private_key", $old_rsa_public_key_cryptrsa_format, $old_rsa_private_key_cryptrsa_format) === false)
        {
            /**
             * Error: the loaded private key doesn't match the public key,
             * which was used before to encrypt data.
             */
             $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
             $msg = $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_008');
             echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$msg."');</script>";
             exit();
        }
        else
        {
            /**
             * Reencrypt temporary data on the server. The step of replacing RSA keys.
             */
            $res = modApiFunc("Payment_Module_Offline_CC", "ReplaceRSAKeyPairStep2ReencryptTmpData", $old_rsa_private_key_cryptrsa_format, $new_rsa_public_key_asc_format);
            //If an error occurred, output it and exit.
            if(!empty($res['error_msg']))
            {
                echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$res['error_msg']."');</script>";
                exit();
            }
            /**
             * If the data reencrypting is finished, output a javascript which
             * idicates that the current step is performed successfully and starts
             * the jump on the next step.If data reencrypting is not finished,
             * repeat the request. If an error occurred, handle it (not realized yet).
             */
            if($res['b_finished'] === true)
            {
                //Go to the next step.
                echo "<script language='javascript'>parent.progress_bar_set_position('1', '".$res["progress_position"]."');</script>";
                echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnSuccessHandler();</script>";
            }
            else
            {
                //Repeat the request.
                echo
    '<body onload="document.forms[\'ReplaceRSAKeyPairStep2ReencryptTmpData\'].submit();">'.
    '<form action="admin.php" method="post" name="ReplaceRSAKeyPairStep2ReencryptTmpData" id="ReplaceRSAKeyPairStep2ReencryptTmpData">'.
    '<input type="hidden" name="asc_action" id="ReplaceRSAKeyPairStep2ReencryptTmpData_asc_action" value="replace_rsa_key_pair_step2_reencrypt_tmp_data">'.
    '<input type="hidden" name="rsa_replacement_public_key_asc_format" id="rsa_replacement_public_key_asc_format" value="'.$new_rsa_public_key_asc_format.'">'.
    '<input type="hidden" name="old_rsa_private_key_asc_format" id="old_rsa_private_key_asc_format" value="'.$old_rsa_private_key_asc_format.'">'.
    '</form>';
                echo "<script language='javascript'>parent.progress_bar_set_position('1', '".$res["progress_position"]."');</script>";
            }
            exit();
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */
    var $ViewFilename;

    /**#@-*/
}
?>