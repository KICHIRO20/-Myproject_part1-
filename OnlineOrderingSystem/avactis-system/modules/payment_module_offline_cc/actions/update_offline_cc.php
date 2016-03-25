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
 * This action is responsible for update OfflineCC settings.
 *
 * @package PaymentModuleOffline CC
 * @access  public
 * @author Alexander Girin
 */
class update_offline_cc extends update_pm_sm
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
    function update_offline_cc()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
    }

    function saveDataToDB($SessionPost)
    {
        modApiFunc("Checkout", "setModuleActive", (modApiFunc("Payment_Module_Offline_CC", "getUid")), ($SessionPost["status"]=="active")? true:false);

        $Settings = array(
                          "MODULE_NAME"  => $SessionPost["ModuleName"]
                         );

        modApiFunc("Payment_Module_Offline_CC", "updateSettings", $Settings);
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        switch($SessionPost["ViewState"]["FormSubmitValue"])
        {
            case "save" :
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();

                if($SessionPost["status"] == "active")
                {
                    //         ,       CheckoutFormEditor'
                    //         PersonInfo     : CreditCardInfo.
                    if(modApiFunc("Checkout", "arePersonInfoTypesActive", array("creditCardInfo")) === true)
                    {
                    }
                    else
                    {
                        $SessionPost["status"]= "inactive";
                        $SessionPost["ViewState"]["ErrorsArray"][] = "MODULE_ERROR_NO_PERSON_INFO_TYPES";
                    }
                }

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->saveDataToDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "true";
                }
                break;
            case "resend_old_private_key_file_as_text":
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();

                //Collect data that just came
                //RSA private key:
                $fname  = "UploadOldPrivateKeyInJSInput";

                // if the file really exist.
                if (array_key_exists($fname,$_FILES) && $_FILES[$fname]['size'] > 0)
                {
                    $rsa_private_key_asc_format = file_get_contents($_FILES[$fname]['tmp_name']);
                    @unlink($_FILES[$fname]['tmp_name']);
                    //Check the format:
                    $rsa_private_key = modApiFunc("Crypto", "convert_rsa_private_key_from_asc_into_cryptrsa_format", $rsa_private_key_asc_format);
                    if($rsa_private_key === false)
                    {
                        //: output an error.
                        $MessageResources = &$application->getInstance('MessageResources');
                        $msg = $MessageResources->getMessage('CRYPTO_RSA_PUBLIC_PRIVATE_KEYS_MISMATCH_DECRYPT_ERROR');
                        echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$msg."');</script>";
                        exit();
                    }
                    else
                    {
                        //Output Javascript, saving the key in the hidden field.
                        echo "<script language='javascript'>parent.UploadOldPrivateKeyInJSProcessServerResponce('".$rsa_private_key_asc_format."');</script>";
                        exit();
                    }
                }
                else
                {
                    $MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
                    $msg = $MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_009');
                    echo "<script language='javascript'>parent.ReplaceRSAKeyPairStep2ReecryptTmpDataOnFailureHandler('".$msg."');</script>";
                    exit();
                }
                break;
            case "check_new_private_key_file_download":
                //Don't save anything in the session! Work on the private RSA key.

                //Collect data that just came
                // A sample RSA private key:
                $rsa_private_key_rsa_format_correct = $_POST['private_key_asc_format'];

                //RSA private key, sent by the user as a file:
                $fname  = "UploadNewPrivateKeyInJSInput";
                $b_is_key_correct = false;
                // if the file really exist.
                if (array_key_exists($fname,$_FILES) && $_FILES[$fname]['size'] > 0)
                {
                    $rsa_private_key_asc_format = file_get_contents($_FILES[$fname]['tmp_name']);
                    @unlink($_FILES[$fname]['tmp_name']);
                    //Check the format:
                    $rsa_private_key = modApiFunc("Crypto", "convert_rsa_private_key_from_asc_into_cryptrsa_format", $rsa_private_key_asc_format);
                    if($rsa_private_key !== false &&
                       $rsa_private_key_asc_format === $rsa_private_key_rsa_format_correct)
                    {
                        $b_is_key_correct = true;
                    }
                }

                if($b_is_key_correct === true)
                {
                    //All tests are successfully done.
                    echo "<script language='javascript'>parent.".$_POST['callback_function_on_success']."();</script>";
                    exit();
                }
                else
                {
                    //Some error has occurred.
                    echo "<script language='javascript'>parent.".$_POST['callback_function_on_failure']."();</script>";
                    exit();
                }
                break;
            default :
                _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
                break;
        }

        //Clear RSA Private key info just in case.
        if(isset($SessionPost["private_key_asc_format"]))
        {
            unset($SessionPost["private_key_asc_format"]);
        }

        $Settings = modApiFunc("Payment_Module_Offline_CC", "getSettings");

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
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