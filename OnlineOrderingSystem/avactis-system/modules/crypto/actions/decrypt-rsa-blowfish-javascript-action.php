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
 * Checkout module.
 * Action handler on DecryptRsaBlowfish.
 *
 * @package Crypto
 * @access  public
 * @author Vadim Lyalikov
 */
class DecryptRsaBlowfishJavascript extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function DecryptRsaBlowfishJavascript()
    {
    }

    /**
     * @ describe the function
     */
    function onAction()
    {
        global $application;

        if ($application->getCurrentProtocol() == "http")
        {
            $this->MessageResources = &$application->getInstance('MessageResources');
            $msg = $this->MessageResources->getMessage('CRYPTO_RSA_NOT_HTTPS_ERROR');
            echo "<script language='javascript'>alert('".$msg."');</script>";
            exit();
        }

        $request = $application->getInstance('Request');

        /**
         * RSA private key comes as a file.
         * Encrypted data comes with the encrypted keys blowfish
         * as a serialized php array.
         */
        /**
         * The result should be returned as javascript code, which changes
         * the values of html elements.
         */

        //group ID of the encoded data
        $group_id = $request->getValueByKey( 'EncryptedDataIndex' );
        if(!empty($group_id))
        {
            //Collect data

            //RSA private key:
            $fname  = "rsa_private_key_" . $group_id;

            // if the file really exists.
            if (array_key_exists($fname,$_FILES) && $_FILES[$fname]['size'] > 0)
            {
                $rsa_private_key_asc_format = file_get_contents($_FILES[$fname]['tmp_name']);
                @unlink($_FILES[$fname]['tmp_name']);
                $rsa_private_key = modApiFunc("Crypto", "convert_rsa_private_key_from_asc_into_cryptrsa_format", $rsa_private_key_asc_format);
                if($rsa_private_key === false)
                {
                    $MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
                    $msg = $MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_008');
                    echo "<script language='javascript'>alert('".$msg."');</script>";
                    exit();
                }
            }
            else
            {
                $MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
                $msg = $MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_009');
                echo "<script language='javascript'>alert('".$msg."');</script>";
                exit();
            }

            $rsa_public_key_cryptrsa_format = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInCryptRSAFormat");

            //Encrypted data and keys Blowfish
            $encrypted_data = $request->getValueByKey("encrypted_data");
            if(!empty($encrypted_data[$group_id]))
            {
                //Decrypt data
                $result = array();
                $rsa_obj = new Crypt_RSA;
                $encrypted_data_array = unserialize(base64_decode($encrypted_data[$group_id]));
                $blowfish_key_cache = array();
                foreach($encrypted_data_array as $id => $info)
                {
                    if(!array_key_exists($info["blowfish_key__rsa_encrypted"], $blowfish_key_cache))
                    {
                        /*
                         If Private key is loaded and stored in the database for the Public key attribute,
                         then output an error message. Don't rewrite anything in the database.
                         */
                        $old_rsa_public_key_asc_format = $info['rsa_public_key_asc_format'];
                        $old_rsa_public_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $old_rsa_public_key_asc_format);

                        if(modApiFunc("Crypto","rsa_do_public_key_match_private_key", $old_rsa_public_key_cryptrsa_format, $rsa_private_key) === true)
                        {
                            $blowfish_key_cache[$info["blowfish_key__rsa_encrypted"]] = $rsa_obj->decrypt($info["blowfish_key__rsa_encrypted"], $rsa_private_key);
                        }
                        else
                        {
                             //Output an error, if loaded Private key doesn't match Public key stored in the database.
                             $this->MessageResources = &$application->getInstance('MessageResources');
                             $msg = $this->MessageResources->getMessage('CRYPTO_RSA_PUBLIC_PRIVATE_KEYS_MISMATCH_DECRYPT_ERROR');
                             echo "<script language='javascript'>alert('".$msg."');</script>";
                             exit();
                        }
                    }
                    $blowfish_key = $blowfish_key_cache[$info["blowfish_key__rsa_encrypted"]];
                    $value = modApiFunc("Crypto", "blowfish_decrypt", base64_decode($info["variable_value__blowfish_encrypted"]), $blowfish_key);
                    $result[$id] = $value;
                }
                //Output data
                $MessageResources = &$application->getInstance('MessageResources');
                $msg = $MessageResources->getMessage('CHECKOUT_ORDER_INFO_INFORMATION_DECRYPTED_SUCCESSFULLY');
$js = "".
"function setValue(element_id, value, b_parent)\n".
"{\n".
"    //default value\n".
"    b_parent = typeof(b_parent) != 'undefined' ? b_parent : false;\n".
"    if(b_parent == true)\n".
"    {\n".
"        el = parent.document.getElementById(element_id);\n".
"    } \n".
"    else\n".
"    {\n".
"        el =        document.getElementById(element_id);\n".
"    }\n".
"\n".
"    //text\n".
"    //td(?)\n".
"    //select-one\n".
"    //alert(el.type);\n".
"    if(el.type == 'text')\n".
"    {\n".
"        el.value = value;\n".
"        el.disabled = false;\n".
"         //<input type='text'>\n".
"    }\n".
"    else if(el.type == 'hidden')\n".
"    {\n".
"        alert('hidden element!');\n".
"        el.value = value;\n".
"         //<input type='hidden'>\n".
"    }\n".
"    else if(el.type == 'select-one')\n".
"    {\n".
"        el.selectedIndex = value;\n".
"        if(el.onchange)\n".
"        {\n".
"            el.onchange();\n".
"        }\n".
"    }\n".
"    else\n".
"    {\n".
"        //undefined type: TD, textarea -        disabled, ... (?)\n".
"       el.innerHTML = '<b><span style=\"color: rgb(0, 80, 153);\">'+ value + '</span></b>';\n".
"    }\n".
"\n".
"}\n".

"function markGroupAsDecrypted(group_id, b_parent)\n".
"{\n".
"    //default value\n".
"    b_parent = typeof(b_parent) != 'undefined' ? b_parent : false;\n".
"    if(b_parent == true)\n".
"    {\n".
"        el = parent.document.getElementById('group_' + group_id + '_is_encrypted');\n".
"    } \n".
"    else\n".
"    {\n".
"        el =        document.getElementById('group_' + group_id + '_is_encrypted');\n".
"    }\n".
"\n".
"    if(el)\n".
"    {\n".
"        el.value = 'false';\n".
"    }\n".
"}".

"function markGroupAsDecryptedExt(group_id, b_parent)\n".
"{\n".
"    //default value\n".
"    b_parent = typeof(b_parent) != 'undefined' ? b_parent : false;\n".
"    if(b_parent == true)\n".
"    {\n".
"        el = parent.document.getElementById('group_' + group_id + '_decryption_message');\n".
"    } \n".
"    else\n".
"    {\n".
"        el =        document.getElementById('group_' + group_id + '_decryption_message');\n".
"    }\n".
"\n".
"    if(el)\n".
"    {\n".
"        el.innerHTML = '<span style=\"color:red;\">".$msg."</span>';\n".
"    }\n".
"}"
;

                $output = '<script language="javascript">';
                $output.= $js;
                foreach($result as $id => $value)
                {
                    //: use JSON for escape strings
                    $el_id = $group_id . "_" . $id;
                    $output .= "setValue('group_".$el_id."', '".addslashes($value)."', true);";
                }
                //Mark the group, as NON-crypted. When SAVE its data goes to the DB.
                $output .= "markGroupAsDecrypted('".$group_id."', true);";
                $output .= "markGroupAsDecryptedExt('".$group_id."', true);";
                $output .= '</script>';

                //Perform Action PurgeCVVFromStoredCreditCardInfo, if necessary
                //Both Actions use the same <input type="file"> and there is
                // a problem with repeated form submit in firefox 2.

                $asc_action1 = $request->getValueByKey('asc_action1');
                if($asc_action1 == "PurgeCVVFromStoredCreditCardInfo")
                {
                    $order_id = $request->getValueByKey( 'order_id' );
                    $person_info_variant_id = $request->getValueByKey( 'person_info_variant_id' );
                    $group_id = $request->getValueByKey( 'EncryptedDataIndex1' );

                    if(!is_numeric($order_id) ||
                       !is_numeric($person_info_variant_id) ||
                       !is_numeric($group_id))
                    {
                        exit();
                        //: report error.
                    }
                    else
                    {
                        modApiFunc("Checkout", "PurgeCVVFromStoredCreditCardInfo", $order_id, $person_info_variant_id, $rsa_private_key);
                    }
                }
                //Perform Action log_decrypted_credit_card_info_review, if necessary.
                //They are combined only to make the work faster. It is logged almost
                // always when it is decrypted.
                $asc_action2 = $request->getValueByKey('asc_action2');
                if($asc_action2 == "log_decrypted_credit_card_info_review")
                {
                    $order_id = $request->getValueByKey( 'order_id' );
                    if(!is_numeric($order_id))
                    {
                        exit();
                        //: report error.
                    }
                    else
                    {
                        modApiFunc("Checkout", "log_decrypted_credit_card_info_review", $order_id);
                    }
                }

                echo $output;
                exit();
            }
            else
            {
                exit;
            }
        }
        else
        {
            exit;
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}

?>