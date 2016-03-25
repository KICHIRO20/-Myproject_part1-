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
 * offline_cc_input_az view
 *
 * @package PaymentModuleOffline CC
 * @author Alexander Girin
 */
class offline_cc_input_az extends pm_sm_input_az
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *
     */
    function offline_cc_input_az()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
    }

    /**
     * Initializes data from the POST array.
     */
    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST = $SessionPost;
    }

    /**
     * Initializes data from the database.
     */
    function initFormData()
    {
        $this->POST = array();
        $settings = modApiFunc("Payment_Module_Offline_CC", "getSettings");
        foreach ($settings as $key => $value)
        {
            switch($key)
            {
                case "MODULE_NAME":                  $this->POST["ModuleName"] = $value; break;
//                case "EXPRESS_CHECKOUT_METHOD_NAME": $this->POST["ExpressCheckoutMethodName"] = $value; break;
//                case "DIRECT_PAYMENT_METHOD_NAME":   $this->POST["DirectPaymentMethodName"] = $value; break;
//                case "MODULE_DESCR":                 $this->POST["ModuleDescr"] = $value; break;
//                case "MODULE_API_CERTIFICATE":       $this->POST["ModuleApiCertificate"] = $value; break;
//                case "MODULE_API_USERNAME":          $this->POST["ModuleApiUsername"] = $value; break;
//                case "MODULE_API_PASSWORD":          $this->POST["ModuleApiPassword"] = $value; break;
//                case "MODULE_MODE" :                 $this->POST["ModuleMode"] = $value; break;
//                case "MODULE_CART" :                 $this->POST["ModuleCart"] = $value; break;
            }
        }
        $this->POST["status"] = modApiFunc("Payment_Module_Offline_CC", "isActive");
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
                 );
    }

    /**
     * @return String Return html code for hidden form fields representing
     * @var $this->ViewState array.
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     * Outputs errors.
     */
    function outputErrors()
    {
        global $application;
        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $error)
        {
            $this->_error_index++;
            $this->_error = $this->MessageResources->getMessage($error);
            $result .= $this->mTmplFiller->fill("payment_module_offline_cc/", "error.tpl.html", array());
        }
        return $result;
    }

    /**
     * Outputs the module status: Active / non-Active.
     */
    function outputStatus()
    {
        global $application;
        $retval = "";
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked":""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "":"checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);

        if($this->isRSAKeyPairGenerated() === false)
        {
            //             .                   .
            //The key doesn't exist yet. It should be generated.
            $retval.= $this->mTmplFiller->fill("payment_module_offline_cc/", "status_read_only.tpl.html", array());
        }
        else
        {
            //             .                      .
            //The key already exists. It shouldn't be generated.
            $retval.= $this->mTmplFiller->fill("payment_module_offline_cc/", "status.tpl.html", array());
        }
        return $retval;
    }

    /**
     * Generates a pair of RSA keys with PHP.
     */
    function outputGenRSAKeyPairPHP()
    {
        global $application;
        $retval = "";
        $request = $application->getInstance('Request');

        //onload the instruction to go to the required step of the generation/key replacement
        $view_state = $request->getValueByKey('ViewState');
        if(!empty($view_state))
        {
            $FormSubmitValue = $view_state["FormSubmitValue"];
        }
        else
        {
            $FormSubmitValue = "";
        }
        if($FormSubmitValue == "reload_before_check_new_private_key_file_download")
        {
            $javascript_onload =
                "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                "<!--\n" . "\n" .
                "var onload_bak_outputGenRSAKeyPairJavascript = window.onload;" . "\n" .
                "window.onload = function()" . "\n" .
                "{" . "\n" .
                "    if(onload_bak_outputGenRSAKeyPairJavascript){onload_bak_outputGenRSAKeyPairJavascript();}" . "\n" .
                "    GenerateRSAKeyPairInPHPOnSuccessHandler2();" . "\n" .
                "}" . "\n" .
                "//-->" . "\n" .
                "</SCRIPT>" . "\n";
        }
        else
        {
            $javascript_onload = "";
        }

        $this->_Template_Contents = array(
                                          "MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_PHP_001" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_PHP_001')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_002" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_002')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_003" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_003')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_004" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_004')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_007" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_007')
                                         ,"rsa_key_generator_public_key_asc_format" => $request->getValueByKey('rsa_key_generator_public_key_asc_format')
                                         ,"rsa_key_generator_private_key_asc_format" => $request->getValueByKey('rsa_key_generator_private_key_asc_format')
                                         ,"javascript_onload" => $javascript_onload
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_offline_cc/", "rsa_key_pair_generator_php.tpl.html", array());
        return $retval;
    }

    /**
     * Generates a pair of RSA keys with Javascript.
     */
    function outputGenRSAKeyPairJavascript()
    {
        global $application;
        $retval = "";
        $request = $application->getInstance('Request');

        //Prepare random data
        $default_random_generator = create_function('', '$a=explode(" ",microtime());return(int)($a[0]*1000000);');
        //1024 bytes
        //Pure PHP
        //Load RSA classes
        $rsa_obj = new Crypt_RSA;
        $math_wrapper = $rsa_obj -> _math_obj;
        $bytes_cnt = 1024;
        $random_data = $math_wrapper->getRand($bytes_cnt * 8, $default_random_generator);

        $random_data_hex = "";
        for($i=0; $i < $bytes_cnt; $i++)
        {
            $random_data_hex .= bin2hex($random_data[$i]) . " ";
        }

        //onload the instruction to go to the required step of the generation/key replacement
        $view_state = $request->getValueByKey('ViewState');
        if(!empty($view_state))
        {
            $FormSubmitValue = $view_state["FormSubmitValue"];
        }
        else
        {
            $FormSubmitValue = "";
        }
        if($FormSubmitValue == "reload_before_check_new_private_key_file_download")
        {
            $javascript_onload =
                "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                "<!--\n" . "\n" .
                "var onload_bak_outputGenRSAKeyPairJavascript = window.onload;" . "\n" .
                "window.onload = function()" . "\n" .
                "{" . "\n" .
                "    if(onload_bak_outputGenRSAKeyPairJavascript){onload_bak_outputGenRSAKeyPairJavascript();}" . "\n" .
                "    GenerateRSAKeyPairInPHPOnSuccessHandler2();" . "\n" .
                "}" . "\n" .
                "//-->" . "\n" .
                "</SCRIPT>" . "\n";
        }
        else
        {
            $javascript_onload = "";
        }

        $this->_Template_Contents = array(
                                          "MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_JS_001" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_JS_001')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_002" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_002')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_003" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_003')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_004" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_004')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_007" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_007')
                                         ,"RSAKeyPairGeneratorRandomData" => $random_data_hex
                                         ,"rsa_key_generator_public_key_asc_format" => $request->getValueByKey('rsa_key_generator_public_key_asc_format')
                                         ,"rsa_key_generator_private_key_asc_format" => $request->getValueByKey('rsa_key_generator_private_key_asc_format')
                                         ,"javascript_onload" => $javascript_onload
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_offline_cc/", "rsa_key_pair_generator_js.tpl.html", array());
        return $retval;
    }

    /**
     * Has the public RSA key been saved into the system-wide database before?
     */
    function isRSAKeyPairGenerated()
    {
        $rsa_public_key = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInCryptRSAFormat");
        if($rsa_public_key === false)
        {
            //The key doesn't exist yet.
            return false;
        }
        else
        {
            //The key already exists.
            return true;
        }
    }

    /**
     * Is php-library suitable for RSA key pair generation available on the server?
     */
    function isThereAPHPMathLibSuitableForRSAKeyPairGeneration()
    {
        /** If the high-speed math library (1024 bit key for 30 sec) exists,
         * then generate on PHP, otherwise on javascript.
         * As for this realization (2007 feb) the high-speed ones are Bigint and GMP.
         */
        $rsa_obj = new Crypt_RSA();
        if(get_class($rsa_obj->_math_obj))
            return true;

        return false;
    }

    /**
     * If the setting page is opened with http, not with https, then it outputs
     * warning.
     */
    function outputHTTPSRequirement()
    {
        global $application;
        if ($application->getCurrentProtocol() == "http")
        {
            $this->_Template_Contents = array
            (
                "MODULE_PAYMENT_OFFLINE_CC_MSG_AZ_HTTPS_REQUIRED" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_AZ_HTTPS_REQUIRED')
            );
            $application->registerAttributes($this->_Template_Contents);
            $retval = $this->mTmplFiller->fill("payment_module_offline_cc/", "https_requirement.tpl.html", array());
            return $retval;
        }
        else
        {
            return "";
        }
    }

    /**
     * Generates RSA key pair.
     * If a math library exists on the server, it generates
     * 1024-bit key on PHP, otherwise it generates 512-bit key on Javascript.
     */
    function outputGenRSAKeyPair()
    {
        global $application;
        if ($application->getCurrentProtocol() == "http")
        {
            return "";
        }
        else
        {
            if($this->isRSAKeyPairGenerated() === false)
            {
                //The key doesn't exists yet. It should be generated.
                /** If the high-speed math library (1024 bit key for 30 sec) exists,
                 * then generate on PHP, otherwise on javascript.
                 * As for this realization (2007 feb) the high-speed ones are Bigint and GMP.
                 */
                if($this->isThereAPHPMathLibSuitableForRSAKeyPairGeneration() === false)
                {
                    return $this->outputGenRSAKeyPairJavascript();
                }
                else
                {
                    return $this->outputGenRSAKeyPairPHP();
                }
            }
            else
            {
                //The key already exists. It shouldn't be generated.
                return "";
            }
        }
    }

    /**
     * Replaces RSA key pair.
     * It generates the keys with Javascript.
     */
    function outputGenReplacementRSAKeyPairJavascript()
    {
        global $application;
        $retval = "";
        $request = $application->getInstance('Request');
        //Prepare random data
        $default_random_generator = create_function('', '$a=explode(" ",microtime());return(int)($a[0]*1000000);');
        //1024 bytes
        //Pure PHP
        //Load RSA classes.
        $rsa_obj = new Crypt_RSA;
        $math_wrapper = $rsa_obj -> _math_obj;
        $bytes_cnt = 1024;
        $random_data = $math_wrapper->getRand($bytes_cnt * 8, $default_random_generator);
        $random_data_hex = "";
        for($i=0; $i < $bytes_cnt; $i++)
        {
            $random_data_hex .= bin2hex($random_data[$i]) . " ";
        }

        //onload the instruction to go to the required step of the generation/key replacement
        $view_state = $request->getValueByKey('ViewState');
        if(!empty($view_state))
        {
            $FormSubmitValue = $view_state["FormSubmitValue"];
        }
        else
        {
            $FormSubmitValue = "";
        }
        if($FormSubmitValue == "reload_before_check_new_private_key_file_download")
        {
            $javascript_onload =
                "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                "<!--\n" . "\n" .
                "var onload_bak_outputReplaceRSAKeyPairJavascript = window.onload;" . "\n" .
                "window.onload = function()" . "\n" .
                "{" . "\n" .
                "    if(onload_bak_outputReplaceRSAKeyPairJavascript){onload_bak_outputReplaceRSAKeyPairJavascript();}" . "\n" .
                "    ReplaceRSAKeyPairStep2ReecryptTmpDataOnSuccessHandler2();" . "\n" .
                "}" . "\n" .
                "//-->" . "\n" .
                "</SCRIPT>" . "\n";
        }
        else
        {
            $javascript_onload = "";
        }

        $this->_Template_Contents = array(
                                          "RSAKeyPairGeneratorRandomData" => $random_data_hex
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_PHP_001" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_PHP_001')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_JS_001" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_JS_001')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_002" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_002')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_START_REENCRYPTION_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_START_REENCRYPTION_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_003" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_003')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_004" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_004')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_005" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_005')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_006" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_006')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_007" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_007')
                                         ,"replacement_rsa_key_generator_public_key_asc_format" => $request->getValueByKey('replacement_rsa_key_generator_public_key_asc_format')
                                         ,"replacement_rsa_key_generator_private_key_asc_format" => $request->getValueByKey('replacement_rsa_key_generator_private_key_asc_format')
                                         ,"javascript_onload" => $javascript_onload
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_offline_cc/", "rsa_replacement_key_pair_generator_js.tpl.html", array());
        return $retval;
    }

    /**
     * Replaces RSA key pair.
     * It generates the keys with Javascript.
     */
    function outputGenReplacementRSAKeyPairPHP()
    {
        global $application;
        $retval = "";
        $request = $application->getInstance('Request');

        //onload the instruction to go to the required step of the generation/key replacement
        $view_state = $request->getValueByKey('ViewState');
        if(!empty($view_state))
        {
            $FormSubmitValue = $view_state["FormSubmitValue"];
        }
        else
        {
            $FormSubmitValue = "";
        }
        if($FormSubmitValue == "reload_before_check_new_private_key_file_download")
        {
            $javascript_onload =
                "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                "<!--\n" . "\n" .
                "var onload_bak_outputReplaceRSAKeyPairJavascript = window.onload;" . "\n" .
                "window.onload = function()" . "\n" .
                "{" . "\n" .
                "    if(onload_bak_outputReplaceRSAKeyPairJavascript){onload_bak_outputReplaceRSAKeyPairJavascript();}" . "\n" .
                "    ReplaceRSAKeyPairStep2ReecryptTmpDataOnSuccessHandler2();" . "\n" .
                "}" . "\n" .
                "//-->" . "\n" .
                "</SCRIPT>" . "\n";
        }
        else
        {
            $javascript_onload = "";
        }

        $this->_Template_Contents = array(
                                          "MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_PHP_001" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_PHP_001')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_JS_001" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_JS_001')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_002" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_002')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_START_REENCRYPTION_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_START_REENCRYPTION_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_003" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_003')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_004" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_004')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_005" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_005')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_006" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_006')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_005')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_006')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_SAVE_KEY_CONFIRMATION_BUTTON')
                                         ,"MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_007" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_REPLACE_KEY_007')
                                         ,"replacement_rsa_key_generator_public_key_asc_format" => $request->getValueByKey('replacement_rsa_key_generator_public_key_asc_format')
                                         ,"replacement_rsa_key_generator_private_key_asc_format" => $request->getValueByKey('replacement_rsa_key_generator_private_key_asc_format')
                                         ,"javascript_onload" => $javascript_onload
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_offline_cc/", "rsa_replacement_key_pair_generator_php.tpl.html", array());
        return $retval;
    }

    /**
     * Returns the name of the math library, selected by the CryptRSA module.
     */
    function getMathLibName()
    {
        if($this->isThereAPHPMathLibSuitableForRSAKeyPairGeneration() === false)
        {
            //The library doesn't exist.
            return false;
        }
        else
        {
            //The library exists.
            $rsa_obj = new Crypt_RSA();
            $full_name = get_class($rsa_obj->_math_obj);
            return _ml_substr($full_name, _ml_strlen("crypt_rsa_math_"));
        }
    }

    /**
     * Returns the name of the math library, selected by the CryptRSA module.
     */
    function outputMathLibName()
    {
        if($this->isThereAPHPMathLibSuitableForRSAKeyPairGeneration() === false)
        {
            //The library doesn't exist.
            return $this->MessageResources->getMessage('MATH_LIB_NOT_DETECTED');
        }
        else
        {
            //The library exists.
            $math_lib_name = $this->getMathLibName();
            $msg = $this->MessageResources->getMessage('MATH_LIB_DETECTED', array($math_lib_name));
            return $msg;
        }
    }

    /**
     * Generates the replacement of the current RSA key pair with the new RSA
     * key pair.
     * A subtask is to reencrypt all encrypted data.
     */
    function outputGenReplacementRSAKeyPair()
    {
        global $application;
        if ($application->getCurrentProtocol() == "http")
        {
            return "";
        }
        else
        {
            if($this->isRSAKeyPairGenerated() === false)
            {
                return "";
            }
            else
            {
                //The key already exists. Generate the replacement.
                /** If the high-speed math library (1024 key bit for 30 sec) exists,
                 * then generate on PHP, otherwise on javascript.
                 * As for this realization (2007 feb) the high-speed ones are Bigint and GMP.
                 */
                if($this->isThereAPHPMathLibSuitableForRSAKeyPairGeneration() === false)
                {
                    return $this->outputGenReplacementRSAKeyPairJavascript();
                }
                else
                {
                    return $this->outputGenReplacementRSAKeyPairPHP();
                }
            }
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $request = new Request();
        $request->setView('CheckoutPaymentModuleSettings');
        $request->setAction("update_offline_cc");
        $form_action = $request->getURL();

        $request = new Request();
        $request->setView('CheckoutPaymentModuleSettings');
        $checkout_payment_module_settings_url = $request->getURL();

        $template_contents = array(
                                    "EditOfflineCCForm"     => $HtmlForm1->genForm($form_action, "POST", "EditOfflineCCForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()
                                   ,"ModuleType"            => $this->MessageResources->getMessage('MODULE_TYPE')
                                   ,"ModuleName"            => $this->MessageResources->getMessage('MODULE_NAME')
                                   ,"Subtitle"              => $this->MessageResources->getMessage('FORM_SUBTITLE')
                                   ,"Errors"                => $this->outputErrors()
                                   ,"ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME')
                                   ,"ModuleStatusFieldHint" => $this->Hints->getHintLink(array('MODULE_STATUS_FIELD_NAME', 'payment-module-offline-messages'))
                                   ,"ModuleStatusField"     => $this->outputStatus()

                                   ,"ModuleNameFieldName"   => $this->MessageResources->getMessage('MODULE_NAME_FIELD_NAME')
                                   ,"ModuleNameFieldHint"   => $this->Hints->getHintLink(array('MODULE_NAME_FIELD_NAME', 'payment-module-offline-messages'))
                                   ,"ModuleNameField"       => $HtmlForm1->genInputTextField("128", "ModuleName", "60", prepareHTMLDisplay($this->POST["ModuleName"]))

                                   ,"MathLibNameFieldName"  => $this->MessageResources->getMessage('MATH_LIB_NAME_FIELD_NAME')
                                   ,"MathLibNameFieldHint"  => $this->Hints->getHintLink(array('MATH_LIB_NAME_FIELD_NAME', 'payment-module-offline-messages'))
                                   ,"MathLibNameField"      => $this->outputMathLibName()


                                   ,"HTTPSRequirement"      => $this->outputHTTPSRequirement()
                                   ,"RSAKeyPairGenerator"   => $this->outputGenRSAKeyPair()
                                   ,"ReplacementRSAKeyPairGenerator"    => $this->outputGenReplacementRSAKeyPair()
                                   ,"Alert_001"             => $this->MessageResources->getMessage('ALERT_001')
                                   ,"Alert_002"             => $this->MessageResources->getMessage('ALERT_002')
                                   ,"Alert_003"             => $this->MessageResources->getMessage('ALERT_003')
                                   ,"Alert_004"             => $this->MessageResources->getMessage('ALERT_004')
                                   ,"Alert_005"             => $this->MessageResources->getMessage('ALERT_005')
                                   ,"Alert_006"             => $this->MessageResources->getMessage('ALERT_006')
                                   ,"MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_008" => $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_GENERATE_KEY_008')
                                   ,"checkout_payment_module_settings_url" => $checkout_payment_module_settings_url
                                  );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("payment_module_offline_cc/", "list.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        switch($tag)
        {
            case "ErrorIndex":
                $value = $this->_error_index;
                break;
            case "Error":
                $value = $this->_error;
                break;
            default:
                $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
                break;
        }
        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $POST;

    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "image_small.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;

    /**#@-*/

}
?>