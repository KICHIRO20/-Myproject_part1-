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
 * Finishing the order: choosing shipping, payment methods and processing online payment.
 * Order info is saved to database and becomes available through AZ.
 *
 * 2005-11-03
 * It should be kept a compatability: store block <=> prerequisite.
 * There are several such prerequisites:
 * shipping-info, shipping-method, billing-info, payment-method,
 * customer-registratration-main-info
 * NOTE: Confirmation is NOT a prerequisite.
 *
 * Store blocks are called differently. E.g. ShippingMethodsListInput.
 * It would be better to make a compatability table. For example:
 * shipping-info                      => Address
 * shipping-method                    => ShippingMethodsListInput
 * billing-info                       => Address
 * payment-method                     => PaymentMethodsListInput
 * customer-registratration-main-info => CustomerInfoInput

 * @package Checkout
 * @author  Vadim Lyalikov
 * @access  public
*/
class CheckoutBase
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Checkout constructor.
     *
     * @ finish the functions on this page
     */
    function Checkout()
    {
        global $application;
        $this->currentStepID = NULL;
        $this->currentOrderID = NULL;
        $this->currentOrderCurrencyID = NULL;
        $this->lastPlacedOrderID = NULL;
        $this->currentCustomerID = NULL;
        $this->PrerequisitesValidationResults = NULL;
        $this->order_search_filter = NULL;
        $this->customer_search_filter = NULL;
        $this->CurrentPaymentModuleSettingsViewName = NULL;
        $this->CurrentPaymentShippingModuleSettingsUID = NULL;
        $this->CustomPaymentGatewayPageContents = NULL;
        $this->CurrentShippingModuleSettingsViewName = NULL;
        $this->ordersId = NULL;
        $this->DeleteOrdersFlag = NULL;
        $this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED = NULL;
    }

    function initStoreOwnerInfo()
    {
        //                                           Checkout               storeOwnerInfo.
        //                                       visible/required.
        $store_owner_country = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY);
        $store_owner_state = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE);
        $emulated_post_data = array();
        $emulated_post_data["Country"] = $store_owner_country;
        if(is_numeric($store_owner_state))
        {
            $emulated_post_data["Statemenu"] = $store_owner_state;
        }
        else
        {
            $emulated_post_data["Statetext"] = $store_owner_state;
        }
        Checkout::validateInputForPrerequisite("storeOwnerInfo", $emulated_post_data);
    }

    //                         prerequisite' ,                               ,
    //                                    . CreditCard Info, BankAccount Info.
    //                        -                                     .
    /* static */ function AdditionalPersonInfoSetCurrStepHook($pm_id, $doredirect = true)
    {
        global $application;
        $request = &$application->getInstance("Request");

            //                        CCInfo:
            /*                           */
            $mmObj = &$application->getInstance('Modules_Manager');
            $moduleInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $pm_id);
            if(!empty($moduleInfo))
            {
                $mmObj->includeAPIFileOnce($moduleInfo['APIClassName']);
                /*                                                      */

                if(is_callable(array($moduleInfo['APIClassName'],"getAdditionalPersonInfoVariantTag")))
                {
                    //:
                    //                .
                    $addtitional_prerequisite_types = array
                    (
                        "creditCardInfo",
                        "bankAccountInfo"
                    );
                    foreach($addtitional_prerequisite_types as $prerequisite_type)
                    {
                        $variant_tag = call_user_func(array($moduleInfo['APIClassName'], 'getAdditionalPersonInfoVariantTag'), $prerequisite_type);
                        //false                     ,                       Checkout CZ              -
                        //                                    CCInfo        : AuthorizeNet, PaypalPro.
                        //            ,                                     CCInfo,        ,                      -
                        //         false.                    CCInfo                        .
                        if($variant_tag !== false)
                        {
                            $prerequisite_name = modApiFunc("Checkout", "getAdditionalPrerequisiteName", $prerequisite_type, $pm_id);

                            if ($addtitional_prerequisite_post_data = $request->getValueByKey($prerequisite_name))
                            {
                            	//POST        CCInfo/BankAccount        -
                            	//                   .                          .
                                modApiFunc("Checkout", "validateInputForPrerequisite", $prerequisite_name);
                            }
                            else
                            {
                            	//POST-                                (CCInfo/BankAccount)          .
                            	//         -                          ?
                            	//
                            	//           ,                                             -
                                //
                                //                          ,  . .
                                //    POST        ID                   (                 ,                           ).
                                //            -                    checkout-process        ,
                                //                  payment method.

                            	//                           , ["error_code"] -                     :
                            	//                                                  .
                            	if ($this->PrerequisitesValidationResults[$prerequisite_name]["isMet"] == true ||
                            	    empty($this->PrerequisitesValidationResults[$prerequisite_name]["error_code"]))
                                {
                                    $this->PrerequisitesValidationResults[$prerequisite_name]["isMet"] = false;
                                    $this->PrerequisitesValidationResults[$prerequisite_name]["error_code"] = 'CHECKOUT_ERR_004_NO_POST_DATA_FOR_PREREQUISITE';
                                    $this->PrerequisitesValidationResults[$prerequisite_name]["error_message_parameters"] = array($this->PrerequisitesValidationResults[$prerequisite_name]["visibleName"], $prerequisite_name);
                                }
                            }

                            if($prerequisite_type == "creditCardInfo")
                            {
                                modApiFunc("Checkout", "encrypt_prerequisite_with_checkout_cz_blowfish_key", $prerequisite_name);
                            }

                            $res = modApiFunc("Checkout", "getPrerequisiteValidationResults", $prerequisite_name);
                            if($res["isMet"] === false)
                            {
                                /*               ,                  .        ,
                                 * payment       .
                                 */
                                switch($prerequisite_type)
                                {
                                    case "creditCardInfo":
                                        $error_code = "PAYMENT_MODULE_INVALID_CC_INFO";
                                        break;
                                    case "bankAccountInfo":
                                        $error_code = "PAYMENT_MODULE_INVALID_BANK_ACCOUNT_INFO";
                                        break;
                                    default:
                                        //: report error;
                                        break;
                                }

                                $pm_res = modApiFunc("Checkout", "getPrerequisiteValidationResults", "paymentModule");
                                modApiFunc("Checkout", "setPrerequisitesValidationResultsItem",
                                           'paymentModule', // prerequisiteName,
                                           $pm_res["variant_tag"], // variant_tag,
                                           false, // isMet,
                                           $error_code, // error_code,
                                           "", // error_message_parameters,
                                           $pm_res["validatedData"]);

                                //         ,
                                $step_id = $request->getValueByKey( 'step_id' );
                                if(empty($step_id))
                                {
                                	//                     SetCurrStepId.         ,    ConfirmOrder.
                                	//                      step_id.
                                	$step_id = modApiFunc("Checkout", "getCurrentStepID");
                                }
                                $step_id_to_redirect_to = modApiFunc("Checkout", "getStepIDtoRedirectToAfterPrerequisitesValidationErrors", $step_id);

                                //                                 .          PaymentModuleID                             ,
                                //                      PaymentModule.                       .                            .
                                //                     ,                               ,
                                // input_view_cz                         .
                                $pm_res = modApiFunc("Checkout", "getPrerequisiteValidationResults", "paymentModule");
                                modApiFunc("Checkout", "setPrerequisitesValidationResultsItem",
                                           'paymentModule', // prerequisiteName,
                                           $pm_res["variant_tag"], // variant_tag,
                                           true, //!!! isMet,
                                           $error_code, // error_code,
                                           "", // error_message_parameters,
                                           $pm_res["validatedData"]);

                                if ($doredirect)
                                {
                                    $request = new Request();
                                    $request->setView('CheckoutView');
                                    $request->setAction("SetCurrStep");
                                    $request->setKey   ( 'step_id', $step_id_to_redirect_to);
                                    $request = modApiFunc("Checkout", "appendCheckoutCZGETParameters", $request);

                                    modApiFunc("Checkout", "saveState");
                                    $application->redirect($request);
                                }
                                else
                                {
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        return true;
    }

    //         ,
    //                   ,                                        .
    //            Credit Card Info.
    //"       "                .
    function estimateLostEncryptedData()
    {
        $pm_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
        if($pm_id !== NULL)
        {
            $this -> AdditionalPersonInfoSetCurrStepHook($pm_id);
        }
    }
    /**
     * Initializes "pseudo-session" Blowfish key to enctypt data symetrically
     * into Checkout CZ.
     * It is not stored in the session and is sent only as POST or GET parameter.
     * If it was lost at one of the steps ( it wasn't passed neither by GET,
     * nor by POST), then all data, which was encrypted by the key, is deleted,
     * because it can't be decrypted without key.
     */
    /*static*/ function initCheckoutCZBlowfishKey($request_key = NULL)
    {
        global $application;
        if(NULL === $request_key)
        {
        	//                          Checkout
        	//           Blowfish     .                   Checkout        .

        	//          .
            $key = modApiFunc("Crypto", "blowfish_gen_blowfish_key");
            $this->setPerRequestVariable("CHECKOUT_CZ_BLOWFISH_KEY", $key);

            if(isset($this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED) &&
                     $this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED === TRUE)
            {
            	//                   .                ,             ,
            	//                               .

            	//                          ,                     .
            	//                                  -                       Action'
            	//  SetCurrentStepId   ConfirmOrder
                $this->setPerRequestVariable("CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST", true);

                //clear all encrypted data: it can't be decrypted. The key is lost.
                Checkout::clear_all_checkout_cz_blowfish_encoded_data();
                modApiFunc('Session', 'set', 'PrerequisitesValidationResults', $this->PrerequisitesValidationResults);
            }
            else
            {
            	//                    .                                    (
            	//          )        -                                 "       ".
            	//                          .
            	//          .
                Checkout::encryptAdditionalPrerequisitesValidationResults('creditCardInfo');
            }
        }
        else
        {
        	//              GET
        	//:           ,         -                            ?
            $this->setPerRequestVariable("CHECKOUT_CZ_BLOWFISH_KEY", $request_key);
        }
        $this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED = TRUE;
    }

    //Clear data,which could be encrypted by the key, that is lost now.
    /*static*/ function clear_all_checkout_cz_blowfish_encoded_data()
    {
        //Credit cards:
        Checkout::initAdditionalPrerequisitesValidationResults("creditCardInfo");
        Checkout::initAdditionalPrerequisitesValidationResults("bankAccountInfo");
        //Other encrypted data:

        /**
         * Note: if some not cleared filled encrypted prerequisites are left, it doesn't matter:
         * the key is lost, it will be impossible to decrypt data.
         */
    }

    function encrypt_prerequisite_with_checkout_cz_blowfish_key($name)
    {
        if(isset($this->PrerequisitesValidationResults[$name]))
        {
            foreach($this->PrerequisitesValidationResults[$name]["validatedData"] as $key => $info)
            {
                $plaintext  = $info["value"];
                $cyphertext = (($plaintext) ? modApiFunc("Crypto", "blowfish_encrypt", $plaintext, $this->getPerRequestVariable("CHECKOUT_CZ_BLOWFISH_KEY")) : '');
                $this->PrerequisitesValidationResults[$name]["validatedData"][$key]["value"] = $cyphertext;
            }
        }
    }

    function decrypt_prerequisite_with_checkout_cz_blowfish_key($name)
    {
        if(isset($this->PrerequisitesValidationResults[$name]))
        {
            foreach($this->PrerequisitesValidationResults[$name]["validatedData"] as $key => $info)
            {
                $cyphertext = $info["value"];
                $plaintext  = (!empty($cyphertext) ? modApiFunc("Crypto", "blowfish_decrypt", $cyphertext, $this->getPerRequestVariable("CHECKOUT_CZ_BLOWFISH_KEY")) : '');
                $this->PrerequisitesValidationResults[$name]["validatedData"][$key]["value"] = $plaintext;
            }
        }
    }

    /**
     * Sets up a value of some variable for the time of processing one
     * HTTP request. Something like a flag or a configuration variable.
     */
    function setPerRequestVariable($key, $value)
    {
        if(!isset($this->PerRequestVariables))
        {
            $this->PerRequestVariables = array();
        }
        $this->PerRequestVariables[$key] = $value;
    }

    /**
     * Returns a value of some variable (for the time of processing one
     * HTTP request). See setPerRequestVariable.
     */
    function getPerRequestVariable($key)
    {
        if(!isset($this->PerRequestVariables))
        {
            $this->PerRequestVariables = array();
            return NULL;
        }
        else
        {
            if(isset($this->PerRequestVariables[$key]))
            {
                return $this->PerRequestVariables[$key];
            }
            else
            {
                return NULL;
            }
        }

    }

    function getGiftCertificatePaymentModuleId()
    {
        return "DA8DF2FD-553A-4BFB-9374-43B9225371C2";
    }

    function getGiftCertificatePaymentModuleInfo()
    {
        $mId = Checkout::getGiftCertificatePaymentModuleId();
        $mInfo = Checkout::getPaymentModuleInfo($mId);
        return $mInfo;
    }

    /*static*/ function getAllInactiveModuleClassAPIName($ModuleClass)
    {
        $mId = Checkout::getAllInactiveModuleId($ModuleClass);
        switch ($ModuleClass)
        {
            case "payment":
                //: hardcoded value
                return "Payment_Module_All_Inactive";
                $mInfo = Checkout::getPaymentModuleInfo($mId);
                break;
            case "shipping":
                //: hardcoded value
                return "Shipping_Module_All_Inactive";
                $mInfo = Checkout::getShippingModuleInfo($mId);
                break;
            default:
                _fatal(__FILE__ . " : " . __LINE__);
                break;
        }

        if(isset($mInfo['APIClassName']))
        {
            return $mInfo['APIClassName'];
        }
        else
        {
            //: : move message to resources.
            _fatal(array( "CODE" => "CORE_054"), __FILE__, __LINE__);
        }
    }

    /*static*/ function getAllInactiveModuleId($ModuleClass)
    {
        switch($ModuleClass)
        {
            case "payment":
            {
                $value = "A50F9CD5-9F45-12CB-353C-03EC75493A0A";//"Payment_Module_All_Inactive";
                break;
            }
            case "shipping":
            {
                $value = "6F82BA03-C5B1-585B-CE2E-B8422A1A19F6";//"Shipping_Module_All_Inactive";
                break;
            }
            default:
            {
                _fatal(__FILE__ . " : " . __LINE__);
                break;
            }
        }
        return $value;
    }

    /*static*/ function updateAllInactiveModuleStatus($ModuleClass)
    {
    	$AllInactiveModuleClassAPIName = Checkout::getAllInactiveModuleClassAPIName($ModuleClass);
        //Inquire all Payment modules.
        //If even one Active exists, install AllInactive [payment] module status in Inactive.
        //  Otherwise install AllInactive [payment] module satus in Active.

        $modules = Checkout::getInstalledAndActiveModulesListData($ModuleClass);
        if(count($modules) == 0)
        {
            //No active payment modules exist, install the module status
            //  AllInactive in Active.
            //: optimize SELECT first. If the status is valid, there is no need to do UPDATE.
            Checkout::setModuleActive(Checkout::getAllInactiveModuleId($ModuleClass), true);
        }
        else
        if(count($modules) == 1)
        {
            //If the module AllInactive is active, then fine,
            //  if other module is active, then AllInactive module
            //  is inactive but this is correct too.
        }
        else
        {
            //Make AllInactive module inactive.
            // : optimize SELECT first. If the status is valid, there is no need to do UPDATE.
            Checkout::setModuleActive(Checkout::getAllInactiveModuleId($ModuleClass), false);
        }
    }

    /**
     * Returns a meta description of database tables, defined for storing data
     * of the Catalog module.
     *
     * @return array table meta info
     */
    /*static?*/ function getTables ()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        # the main table ORDERS
        $orders = 'orders';
        $tables[$orders] = array();
        $tables[$orders]['columns'] = array
            (
                'id'                => 'orders.order_id'
               ,'date'              => 'orders.order_date'
               ,'status_id'         => 'orders.order_status_id' // reference to order_status
               ,'payment_status_id' => 'orders.order_payment_status_id' // reference to order_payment_status
               ,'payment_method'    => 'orders.order_payment_method'
               ,'payment_module_id' => 'orders.order_payment_module_id'
               ,'payment_method_detail' => 'orders.order_payment_method_detail'
               ,'payment_processor_order_id' => 'orders.order_payment_processor_order_id'
               ,'shipping_method'   => 'orders.order_shipping_method'
               ,'track_id'          => 'orders.order_track_id'
               ,'person_id'         => 'orders.order_person_id' // reference to persons
               ,'affiliate_id'      => 'orders.affiliate_id' // affiliate id which was in customer GET parameter
               ,"edited"            => "orders.was_manually_edited"
               ,"included_tax"      => "orders.display_included_tax"
               ,"new_type"          => "orders.is_order_new_type" // 0 - old and not editable, 1 - new type and editable
            );
        $tables[$orders]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'date'              => DBQUERY_FIELD_TYPE_DATETIME . " NOT NULL DEFAULT '0000-00-00 00:00:00'"
               ,'status_id'         => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'payment_status_id' => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'payment_method'    => DBQUERY_FIELD_TYPE_CHAR255
               ,'payment_module_id' => DBQUERY_FIELD_TYPE_CHAR255 . " NOT NULL DEFAULT ''"
               ,'payment_method_detail' => DBQUERY_FIELD_TYPE_CHAR255
               ,'payment_processor_order_id' => DBQUERY_FIELD_TYPE_CHAR255
               ,'shipping_method'   => DBQUERY_FIELD_TYPE_CHAR255
               ,'track_id'          => DBQUERY_FIELD_TYPE_TEXT
               ,'person_id'         => DBQUERY_FIELD_TYPE_INT
               ,'affiliate_id'      => DBQUERY_FIELD_TYPE_CHAR255 . " NOT NULL DEFAULT ''"
               ,"edited"            => DBQUERY_FIELD_TYPE_INT . " DEFAULT 0"
               ,"included_tax"      => DBQUERY_FIELD_TYPE_INT . " DEFAULT 0"
               ,"new_type"          => DBQUERY_FIELD_TYPE_INT . " DEFAULT 1"
            );
        $tables[$orders]['primary'] = array
            (
                'id'
            );
        $tables[$orders]['indexes'] = array
            (
                'IDX_date'        => 'date', // @
                'status_id' => 'status_id',
                'person_id' => 'person_id'
            );

        # a table of possible payment status
        $payment_status = 'order_payment_status';
        $tables[$payment_status] = array();
        $tables[$payment_status]['columns'] = array
            (
                'id'                => 'order_payment_status.order_payment_status_id'
               ,'name'              => 'order_payment_status.order_payment_status_name'
               ,'descr'             => 'order_payment_status.order_payment_status_description'
               ,'sort'              => 'order_payment_status.order_payment_status_sort'
               ,'type'              => 'order_payment_status.order_payment_status_type'
               ,'active'            => 'order_payment_status.order_payment_status_active'
            );
        $tables[$payment_status]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50 . " NOT NULL DEFAULT ''"
               ,'descr'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'sort'              => DBQUERY_FIELD_TYPE_INT
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR20 . ' NOT NULL default \'custom\''
               ,'active'            => DBQUERY_FIELD_TYPE_BOOL
            );
        $tables[$payment_status]['primary'] = array
            (
                'id'
            );

        # a table of possible order status
        $order_status = 'order_status';
        $tables[$order_status] = array();
        $tables[$order_status]['columns'] = array
            (
                'id'                => 'order_status.order_status_id'
               ,'name'              => 'order_status.order_status_name'
               ,'descr'             => 'order_status.order_status_description'
               ,'sort'              => 'order_status.order_status_sort'
               ,'type'              => 'order_status.order_status_type'
               ,'active'            => 'order_status.order_status_active'
            );
        $tables[$order_status]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'descr'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'sort'              => DBQUERY_FIELD_TYPE_INT
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR20 . ' NOT NULL default \'custom\''
               ,'active'            => DBQUERY_FIELD_TYPE_BOOL
            );
        $tables[$order_status]['primary'] = array
            (
                'id'
            );

        # a table of order notes
        $order_notes = 'order_notes';
        $tables[$order_notes] = array();
        $tables[$order_notes]['columns'] = array
            (
                'order_id'          => 'order_notes.order_id'
               ,'date'              => 'order_notes.order_note_date'
               ,'microtime'         => 'order_notes.order_microtime'
               ,'content'           => 'order_notes.order_note_content'
               ,'type'              => 'order_notes.order_note_type'
            );
        $tables[$order_notes]['types'] = array
            (
                'order_id'          => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'date'              => DBQUERY_FIELD_TYPE_DATETIME . ' NOT NULL default \'0000-00-00:00:00\''
               ,'microtime'         => DBQUERY_FIELD_TYPE_CHAR50 . ' default 0'
               ,'content'           => DBQUERY_FIELD_TYPE_LONGTEXT . ' NOT NULL '
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
            );

        # a table to store info about  Customer, Billing, Shipping
        $order_person_data = 'order_person_data';
        $tables[$order_person_data] = array();
        $tables[$order_person_data]['columns'] = array
            (
                'id'                => 'order_person_data.order_person_data_id'
               ,'order_id'          => 'order_person_data.order_id' // reference to orders
               ,'variant_id'        => 'order_person_data.person_info_variant_id' // reference to person_info_type
               ,'attribute_id'      => 'order_person_data.person_attribute_id' // reference to person_attribute
               ,'name'              => 'order_person_data.order_person_data_name'
               ,'value'             => 'order_person_data.order_person_data_value'
               ,'desc'              => 'order_person_data.order_person_data_description'
               ,'b_encrypted'       => 'order_person_data.order_person_data_b_encrypted'
               ,'encrypted_secret_key'
                                    => 'order_person_data.order_person_data_encrypted_secret_key'
               ,'rsa_public_key_asc_format'
                                    => 'order_person_data.order_person_data_rsa_public_key_asc_format'
            );
        $tables[$order_person_data]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
               ,'order_id'          => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'variant_id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'attribute_id'      => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'desc'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'b_encrypted'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'encrypted_secret_key'
                                    => DBQUERY_FIELD_TYPE_TEXT //Use CHAR256 instead of CHAR255 for 2048-bit RSA key.
               ,'rsa_public_key_asc_format'
                                    => DBQUERY_FIELD_TYPE_TEXT
            );
        $tables[$order_person_data]['primary'] = array
            (
                'id'
            );
        $tables[$order_person_data]['indexes'] = array
            (
                'IDX_unique' => 'order_id, variant_id, attribute_id, b_encrypted'
            );

        # a table for storing product info in the order
        $order_product = 'order_product';
        $tables[$order_product] = array();
        $tables[$order_product]['columns'] = array
            (
                'id'                => 'order_product.order_product_id'
               ,'order_id'          => 'order_product.order_id' //  reference to orders
               ,'qty'               => 'order_product.order_product_qty'
               ,'name'              => 'order_product.order_product_name'
               ,'type'              => 'order_product.order_product_type'
               ,'store_id'          => 'order_product.order_product_store_id'
               ,'inventory_id'      => 'order_product.order_product_inventory_id'
            );
        $tables[$order_product]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
               ,'order_id'          => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'qty'               => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 1'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'store_id'          => DBQUERY_FIELD_TYPE_INT
               ,'inventory_id'      => DBQUERY_FIELD_TYPE_CHAR255 . ' DEFAULT NULL DEFAULT \'\''
            );
        $tables[$order_product]['primary'] = array
            (
                'id'
            );

        # a table for storing product attribute info in the order
        $order_product_to_attributes = 'order_product_to_attributes';
        $tables[$order_product_to_attributes] = array();
        $tables[$order_product_to_attributes]['columns'] = array
            (
                'attribute_id'      => 'order_product_to_attributes.attribute_id' // reference to attributes in Catalog
               ,'currency_code'     => 'order_product_to_attributes.currency_code'
               ,'currency_type'     => 'order_product_to_attributes.currency_type'
               ,'product_id'        => 'order_product_to_attributes.order_product_id' // reference to order_product
               ,'value'             => 'order_product_to_attributes.order_product_attribute_value'
            );
        $tables[$order_product_to_attributes]['types'] = array
            (
                'attribute_id'      => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'currency_code'     => DBQUERY_FIELD_TYPE_CHAR5
               ,'currency_type'     => DBQUERY_FIELD_TYPE_CURRENCY_TYPE
               ,'product_id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'value'             => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$order_product_to_attributes]['indexes'] = array
            (
                'IDX_attribute_id' => 'attribute_id',
                'IDX_currency_code' => 'currency_code',
                'IDX_order_product_id' => 'product_id'
            );

        # a table for storing product custom attribute info in the order
        $order_product_custom_attributes = 'order_product_custom_attributes';
        $tables[$order_product_custom_attributes] = array();
        $tables[$order_product_custom_attributes]['columns'] = array
            (
                'id'                => 'order_product_custom_attributes.order_product_custom_attribute_id'
               ,'product_id'        => 'order_product_custom_attributes.order_product_id' //  reference to order_product
               ,'tag'               => 'order_product_custom_attributes.order_product_custom_attribute_tag'
               ,'name'              => 'order_product_custom_attributes.order_product_custom_attribute_name'
               ,'value'             => 'order_product_custom_attributes.order_product_custom_attribute_value'
            );
        $tables[$order_product_custom_attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
               ,'product_id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'tag'               => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'value'             => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$order_product_custom_attributes]['primary'] = array
            (
                'id'
            );

        # a table of storing options
        $order_product_options = 'order_product_options';
        $tables[$order_product_options] = array();
        $tables[$order_product_options]['columns'] = array
            (
                'product_option_id'     => 'order_product_options.product_option_id'
               ,'order_product_id'      => 'order_product_options.order_product_id'
               ,'option_name'           => 'order_product_options.option_name'
               ,'option_value'          => 'order_product_options.option_value'
               ,'is_file'               => 'order_product_options.is_file'
            );
        $tables[$order_product_options]['types'] = array
            (
                'product_option_id'     => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
               ,'order_product_id'      => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'option_name'           => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'option_value'          => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'is_file'               => "ENUM ('N','Y') not null default 'N'"
            );
        $tables[$order_product_options]['primary'] = array
            (
                'product_option_id'
            );


        # a table of calculated order prices
        $order_prices = 'order_prices';
        $tables[$order_prices] = array();
        $tables[$order_prices]['columns'] = array
            (
                'order_id'                              => 'order_prices.order_id'
               ,'currency_code'                         => 'order_prices.currency_code'
               ,'currency_type'                         => 'order_prices.currency_type'
               ,'order_total_to_pay'                    => 'order_prices.order_total_to_pay'
               ,'order_total_paid_by_gc'                => 'order_prices.order_total_paid_by_gc'
               ,'order_total'                           => 'order_prices.order_total'
               ,'order_subtotal'                        => 'order_prices.order_subtotal'
               ,'minimum_shipping_cost'                 => 'order_prices.minimum_shipping_cost'
               ,'free_shipping_for_orders_over'         => 'order_prices.free_shipping_for_orders_over'
               ,'free_handling_for_orders_over'         => 'order_prices.free_handling_for_orders_over'
               ,'per_item_shipping_cost_sum'            => 'order_prices.per_item_shipping_cost_sum'
               ,'per_order_shipping_fee'                => 'order_prices.per_order_shipping_fee'
               ,'shipping_method_cost'                  => 'order_prices.shipping_method_cost'
               ,'total_shipping_charge'                 => 'order_prices.total_shipping_charge'
               ,'per_item_handling_cost_sum'            => 'order_prices.per_item_handling_cost_sum'
               ,'per_order_handling_fee'                => 'order_prices.per_order_handling_fee'
               ,'total_handling_charge'                 => 'order_prices.total_handling_charge'
               ,'total_shipping_and_handling_cost'      => 'order_prices.total_shipping_and_handling_cost'
               ,'order_tax_total'                       => 'order_prices.order_tax_total'
               ,'subtotal_global_discount'              => 'order_prices.subtotal_global_discount'
               ,'subtotal_promo_code_discount'          => 'order_prices.subtotal_promo_code_discount'
               ,'quantity_discount'                     => 'order_prices.quantity_discount'
               ,'discounted_subtotal'                   => 'order_prices.discounted_subtotal'
               ,'order_not_included_tax_total'          => 'order_prices.order_not_included_tax_total'
               ,'per_order_payment_module_shipping_fee' => 'order_prices.per_order_payment_module_shipping_fee'
            );
        $tables[$order_prices]['types'] = array
            (
                'order_id'                              => DBQUERY_FIELD_TYPE_INT . ' NOT NULL'
               ,'currency_code'                         => DBQUERY_FIELD_TYPE_CHAR5
               ,'currency_type'                         => DBQUERY_FIELD_TYPE_CURRENCY_TYPE
               ,'order_total_to_pay'                    => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'order_total_paid_by_gc'                => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'order_total'                           => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'order_subtotal'                        => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'minimum_shipping_cost'                 => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'free_shipping_for_orders_over'         => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'free_handling_for_orders_over'         => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'per_item_shipping_cost_sum'            => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'per_order_shipping_fee'                => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'shipping_method_cost'                  => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'total_shipping_charge'                 => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'per_item_handling_cost_sum'            => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'per_order_handling_fee'                => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'total_handling_charge'                 => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'total_shipping_and_handling_cost'      => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'order_tax_total'                       => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'subtotal_global_discount'              => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'subtotal_promo_code_discount'          => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'quantity_discount'                     => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'discounted_subtotal'                   => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'order_not_included_tax_total'          => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,'per_order_payment_module_shipping_fee' => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
            );
        $tables[$order_prices]['indexes'] = array
            (
                'UNIQUE IDX_unique' => 'order_id,currency_code',
                'currency_code'     => 'currency_code'
            );

        #
        $order_taxes = 'order_taxes';
        $tables[$order_taxes] = array();
        $tables[$order_taxes]['columns'] = array
            (
                "id"                => "order_taxes.id"
               ,'order_id'          => 'order_taxes.order_id'
               ,'currency_code'     => 'order_taxes.currency_code'
               ,'currency_type'     => 'order_taxes.currency_type'
               ,'type'              => 'order_taxes.order_tax_type'
               ,'value'             => 'order_taxes.order_tax_value'
               ,"is_included"       => "order_taxes.is_included"
            );
        $tables[$order_taxes]['types'] = array
            (
                "id"                => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
               ,'order_id'          => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'currency_code'     => DBQUERY_FIELD_TYPE_CHAR5
               ,'currency_type'     => DBQUERY_FIELD_TYPE_CURRENCY_TYPE
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'value'             => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,"is_included"       => DBQUERY_FIELD_TYPE_INT . " DEFAULT 2"        // 0 - is not included, 1 - is included, 2 - is not defined
            );
        $tables[$order_taxes]['primary'] = array
            (
                "id"
            );
        $tables[$order_taxes]['indexes'] = array
            (
                'IDX_type' => 'type' // @
            );

        #
        $order_tax_display_options = 'order_tax_display_options';
        $tables[$order_tax_display_options] = array();
        $tables[$order_tax_display_options]['columns'] = array
            (
                "id"                => "order_tax_display_options.id"
               ,'order_id'          => 'order_tax_display_options.order_id'
               ,'currency_code'     => 'order_tax_display_options.currency_code'
               ,'currency_type'     => 'order_tax_display_options.currency_type'
               ,'name'              => 'order_tax_display_options.visible_name'
               ,'value'             => 'order_tax_display_options.order_tax_value'
               ,"formula"           => "order_tax_display_options.formula"
            );
        $tables[$order_tax_display_options]['types'] = array
            (
                "id"                => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
               ,'order_id'          => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'currency_code'     => DBQUERY_FIELD_TYPE_CHAR5
               ,'currency_type'     => DBQUERY_FIELD_TYPE_CURRENCY_TYPE
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'value'             => DBQUERY_FIELD_TYPE_DECIMAL20_5 . ' NOT NULL DEFAULT 0'
               ,"formula"           => DBQUERY_FIELD_TYPE_CHAR50 . " NOT NULL DEFAULT ''"
            );
        $tables[$order_tax_display_options]['primary'] = array
            (
                "id"
            );
        $tables[$order_tax_display_options]['indexes'] = array
            (
                'IDX_type' => 'name' // @
            );

        # the main table PERSONS
        $persons = 'persons';
        $tables[$persons] = array();
        $tables[$persons]['columns'] = array
            (
                'id'                => 'persons.person_id'
               ,'login'             => 'persons.person_login'
               ,'password'          => 'persons.person_password'
            );
        $tables[$persons]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'login'             => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'password'          => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
            );
        $tables[$persons]['primary'] = array
            (
                'id'
            );
        $tables[$persons]['indexes'] = array
            (
                'IDX_login' => 'login'
            );

        # a table of attributes to store person info
        $person_attributes = 'person_attributes';
        $tables[$person_attributes] = array();
        $tables[$person_attributes]['columns'] = array
            (
                'id'                => 'person_attributes.person_attribute_id'
               ,'tag'               => 'person_attributes.person_attribute_view_tag'
               ,'pattern_id'        => 'person_attributes.pattern_id' // reference to patterns (Catalog)
               ,'input_type_id'     => 'person_attributes.input_type_id' //          input_types (Catalog)
               ,'input_validation_func_name'     => 'person_attributes.input_validation_func_name' // the function name, called to check a user input (Checkout)
               ,'min'               => 'person_attributes.attribute_min_value'
               ,'max'               => 'person_attributes.attribute_max_value'
               ,'size'              => 'person_attributes.attribute_html_size'
               ,'is_custom'         => 'person_attributes.is_custom'
            );
        $tables[$person_attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'tag'               => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'pattern_id'        => DBQUERY_FIELD_TYPE_INT
               ,'input_type_id'     => DBQUERY_FIELD_TYPE_INT
               ,'input_validation_func_name' => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'min'               => DBQUERY_FIELD_TYPE_CHAR255
               ,'max'               => DBQUERY_FIELD_TYPE_CHAR255
               ,'size'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'is_custom'         => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
            );
        $tables[$person_attributes]['primary'] = array
            (
                'id'
            );

        # a table of person info type
        $person_info_types = 'person_info_types';
        $tables[$person_info_types] = array();
        $tables[$person_info_types]['columns'] = array
            (
                'id'                => 'person_info_types.person_info_type_id'
               ,'tag'               => 'person_info_types.person_info_type_tag'
               ,'name'              => 'person_info_types.person_info_type_name'
               ,'descr'             => 'person_info_types.person_info_type_description'
               ,'visible_name'      => 'person_info_types.person_info_type_visible_name'
               ,'active'            => 'person_info_types.person_info_type_active'
            );
        $tables[$person_info_types]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'tag'               => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'descr'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'visible_name'      => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'active'            => DBQUERY_FIELD_BOOLEAN_DEFAULT_TRUE
            );
        $tables[$person_info_types]['primary'] = array
            (
                'id'
            );
        $tables[$person_info_types]['indexes'] = array
            (
                'IDX_name' => 'name'
               ,'UNIQUE IDX_tag' => 'tag'
            );

        # a table of info variants for person variants
        $person_info_variants = 'person_info_variants';
        $tables[$person_info_variants] = array();
        $tables[$person_info_variants]['columns'] = array
            (
                'id'                => 'person_info_variants.person_info_variant_id'
                //fk:
               ,'type_id'           => 'person_info_variants.person_info_type_id'
               ,'tag'               => 'person_info_variants.person_info_variant_tag'
               ,'name'              => 'person_info_variants.person_info_variant_name'
               ,'descr'             => 'person_info_variants.person_info_variant_description'
                //similar to person_type:
               ,'visible_name'      => 'person_info_variants.person_info_variant_visible_name'
            );
        $tables[$person_info_variants]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'type_id'           => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'tag'               => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'descr'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'visible_name'      => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
            );
        $tables[$person_info_variants]['primary'] = array
            (
                'id'
            );
        $tables[$person_info_variants]['indexes'] = array
            (
                'IDX_name' => 'name'
               ,'IDX_type_id' => 'type_id'
               ,'UNIQUE IDX_tag_type_id' => 'type_id, tag'
            );

        # a table links attributes to person info variants
        $person_info_variants_to_attributes = 'person_info_variants_to_attributes';
        $tables[$person_info_variants_to_attributes] = array();
        $tables[$person_info_variants_to_attributes]['columns'] = array
            (
                'id'                => 'person_info_variants_to_attributes.person_info_id'
               ,'variant_id'        => 'person_info_variants_to_attributes.person_info_variant_id' //           person_info_variants
               ,'attribute_id'      => 'person_info_variants_to_attributes.person_attribute_id' //           person_attributes
               ,'name'              => 'person_info_variants_to_attributes.person_attribute_visible_name'
               ,'descr'             => 'person_info_variants_to_attributes.person_attribute_description'
               ,'unremovable'       => 'person_info_variants_to_attributes.person_attribute_unremovable'
               ,'visible'           => 'person_info_variants_to_attributes.person_attribute_visible'
               ,'required'          => 'person_info_variants_to_attributes.person_attribute_required'
               ,'sort'              => 'person_info_variants_to_attributes.person_attribute_sort_order'
               ,'field_type'        => 'person_info_variants_to_attributes.field_type'
               ,'field_params'      => 'person_info_variants_to_attributes.field_params'
            );
        $tables[$person_info_variants_to_attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'variant_id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'attribute_id'      => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'descr'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'unremovable'       => DBQUERY_FIELD_TYPE_BOOL
               ,'visible'           => DBQUERY_FIELD_TYPE_BOOL
               ,'required'          => DBQUERY_FIELD_TYPE_BOOL
               ,'sort'              => DBQUERY_FIELD_TYPE_INT
               ,'field_type'        => DBQUERY_FIELD_CUSTOM_TYPE . ' NOT NULL DEFAULT \'CUSTOM_FIELD_TYPE_STANDARD\''
               ,'field_params'      => DBQUERY_FIELD_TYPE_LONGTEXT . ' NOT NULL '
            );
        $tables[$person_info_variants_to_attributes]['primary'] = array
            (
                'id'
            );
        $tables[$person_info_variants_to_attributes]['indexes'] = array
            (
                'UNIQUE KEY IDS' => 'variant_id, attribute_id'
            );


        # a table of real attribute values for persons
        $persons_data = 'persons_data';
        $tables[$persons_data] = array();
        $tables[$persons_data]['columns'] = array
            (
                'variant_id'        => 'persons_data.person_info_variant_id'
               ,'attribute_id'      => 'persons_data.person_attribute_id'
               ,'person_id'         => 'persons_data.person_id'
               ,'value'             => 'persons_data.person_attribute_value'
            );
        $tables[$persons_data]['types'] = array
            (
                'variant_id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'attribute_id'      => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'person_id'         => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'value'             => DBQUERY_FIELD_TYPE_LONGTEXT . ' NOT NULL '
            );
        $tables[$persons_data]['primary'] = array
            (
                'variant_id', 'attribute_id', 'person_id'
            );

        # a table links persons, person info variants and attribute values
        $person_to_info_variants = 'person_to_info_variants';
        $tables[$person_to_info_variants] = array();
        $tables[$person_to_info_variants]['columns'] = array
            (
                'person_id'         => 'person_to_info_variants.person_id' //  reference to persons
               ,'variant_id'        => 'person_to_info_variants.person_info_variant_id' //  reference to person_info_types
            );
        $tables[$person_to_info_variants]['types'] = array
            (
                'person_id'         => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
               ,'variant_id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
            );
        $tables[$person_to_info_variants]['primary'] = array
            (
                'person_id', 'variant_id'
            );

        $pm_sm_settings = 'checkout_pm_sm_settings';
        $tables[$pm_sm_settings] = array();
        $tables[$pm_sm_settings]['columns'] = array
            (
                'id'                         => $pm_sm_settings.'.checkout_pm_sm_settings_id'
               ,'module_id'                  => $pm_sm_settings.'.checkout_pm_sm_settings_module_id'
               ,'module_class_name'          => $pm_sm_settings.'.checkout_pm_sm_settings_module_class_name'
               ,'module_group'               => $pm_sm_settings.'.checkout_pm_sm_settings_module_group'
               ,'status_active_value_id'     => $pm_sm_settings.'.checkout_pm_sm_settings_status_active_value_id'
               ,'status_selected_value_id'   => $pm_sm_settings.'.checkout_pm_sm_settings_status_selected_value_id'
               ,'sort_order'                 => $pm_sm_settings.'.checkout_pm_sm_settings_sort_order'

            );
        $tables[$pm_sm_settings]['types'] = array
            (
                'id'                         => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'module_id'                  => DBQUERY_FIELD_TYPE_CHAR50
               ,'module_class_name'          => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'module_group'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'status_active_value_id'     => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'status_selected_value_id'   => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'sort_order'                 => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
            );
        $tables[$pm_sm_settings]['primary'] = array
            (
                'id'
            );
        $tables[$pm_sm_settings]['indexes'] = array
            (
                'IDX_unique' => 'module_id'
            );

        $pm_sm_accepted_currencies = 'checkout_pm_sm_accepted_currencies';
        $tables[$pm_sm_accepted_currencies] = array();
        $tables[$pm_sm_accepted_currencies]['columns'] = array
            (
                'module_id'                  => $pm_sm_accepted_currencies.'.checkout_pm_sm_accepted_currencies_module_id'
               ,'currency_code'              => $pm_sm_accepted_currencies.'.checkout_pm_sm_accepted_currencies_currency_code'
               ,'currency_status'            => $pm_sm_accepted_currencies.'.checkout_pm_sm_accepted_currencies_currency_status'
            );
        $tables[$pm_sm_accepted_currencies]['types'] = array
            (
                'module_id'                  => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'currency_code'              => DBQUERY_FIELD_TYPE_CHAR5
               ,'currency_status'            => "ENUM ('". ACCEPTED. "',"
                                                     ."'". NOT_ACCEPTED. "',"
                                                     ."'". THE_ONE_ONLY_ACCEPTED. "') "
                                               ." NOT NULL DEFAULT '". ACCEPTED. "'"
            );
        $tables[$pm_sm_accepted_currencies]['primary'] = array
            (
                'module_id', 'currency_code'
            );

        $pm_sm_currency_acceptance_rules = 'checkout_pm_sm_currency_acceptance_rules';
        $tables[$pm_sm_currency_acceptance_rules] = array();
        $tables[$pm_sm_currency_acceptance_rules]['columns'] = array
            (
                'module_id'                  => $pm_sm_currency_acceptance_rules.'.checkout_pm_sm_currency_acceptance_rules_module_id'
               ,'rule_name'                  => $pm_sm_currency_acceptance_rules.'.checkout_pm_sm_currency_acceptance_rules_rule_name'
               ,'rule_selected'              => $pm_sm_currency_acceptance_rules.'.checkout_pm_sm_currency_acceptance_rules_rule_selected'
            );
        $tables[$pm_sm_currency_acceptance_rules]['types'] = array
            (
                'module_id'                  => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
               ,'rule_name'                  => "ENUM ('". ACTIVE_AND_SELECTED_BY_CUSTOMER ."',"
                                                     ."'". ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER. "',"
                                                     ."'". THE_ONLY_ACCEPTED. "',"
                                                     ."'". MAIN_STORE_CURRENCY. "') "
                                               ." NOT NULL DEFAULT '". MAIN_STORE_CURRENCY ."'"
               ,'rule_selected'              => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
            );
        $tables[$pm_sm_currency_acceptance_rules]['primary'] = array
            (
                'module_id', 'rule_name'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function setPrerequisitesValidationResults($NewPrerequisitesValidationResults)
    {
        $this->PrerequisitesValidationResults = $NewPrerequisitesValidationResults;
    }

    function getPrerequisitesValidationResults()
    {
        return $this->PrerequisitesValidationResults;
    }

    function clearAllNotMetPrerequisitesValidationResultsData()
    {
        foreach($this->PrerequisitesValidationResults as $PrerequisiteName => $ValidatedData)
        {
            $ValidatedData = &$this->PrerequisitesValidationResults[$PrerequisiteName];

            if($ValidatedData["isMet"] == false)
            {
                $ValidatedData["error_code"] = "";
                if(!empty($ValidatedData["validatedData"]))
                {
                    foreach($ValidatedData["validatedData"] as $PersonInfoAttributeName => $data)
                    {
                        $data = &$ValidatedData["validatedData"][$PersonInfoAttributeName];
                        if(($data["error_code_full"] != "") ||
                           ($data["error_code_short"] != ""))
                        {
                            $data["value"] = "";
                            $data["error_code_full"] = "";
                            $data["error_code_short"] = "";
                        }
                        unset($data);
                    }
                }
            }

            unset($ValidatedData);
        }
    }

   /*
     If we are at step i, clear error info for i+1 step and for
     all posterior ones: i+2 , ...
     A designer's decision.
     Mainly to remove outputting the error 004, where possible.
    */
    function clearNotMetPrerequisitesValidationResultsDataForAllPosteriorSteps($step_id)
    {
        $prerequisitesForAllSteps = $this->getPrerequisitesINI();

       /*  the consequence of the steps (which of them follows which,
        how many steps at all...) in the Checkout CZ is not specified. Suppose,
        that the number of prerequisites can increase during the process.
        */
        $next_step_id = $step_id + 1;

        //$step_id can be the last step
        if(isset($prerequisitesForAllSteps[$next_step_id]))
        {
            foreach($this->PrerequisitesValidationResults as $PrerequisiteName => $PrerequisiteInfo)
            {
                if(!in_array($PrerequisiteName, $prerequisitesForAllSteps[$next_step_id])
                &&  multi_array_search($PrerequisiteName, $prerequisitesForAllSteps))
                {
                    $ValidatedData = &$this->PrerequisitesValidationResults[$PrerequisiteName];
                    if($ValidatedData["isMet"] == false)
                    {
                        $ValidatedData["error_code"] = "";
                        if(!empty($ValidatedData["validatedData"]))
                        {
                            foreach($ValidatedData["validatedData"] as $PersonInfoAttributeName => $data)
                            {
//                                echo '_'.$PersonInfoAttributeName.'<br>';
                                $data = &$ValidatedData["validatedData"][$PersonInfoAttributeName];
                                if(($data["error_code_full"] != "") ||
                                   ($data["error_code_short"] != ""))
                                {
                                    $data["value"] = "";
                                    $data["error_code_full"] = "";
                                    $data["error_code_short"] = "";
                                }
                                unset($data);
                            }
                        }
                    }
                    unset($ValidatedData);
                }
            }
        }
    }

   /* At the input, there are two copies of PersonInfo in the format prerequisite
       validated data, see Checkout::getValidatedDataStructure
       Fills all attributes in $to, existed in $from.
       The fields "error_code_full" and "error_code_short" are copied too.
    */
    function mergePersonInfo($from, &$to)
    {
        foreach($from as $attribute_tag => $attribute_data)
        {
            if(array_key_exists($attribute_tag, $to))
            {
                $to[$attribute_tag] = $from[$attribute_tag];
            }
        }
    }

    /**
     *              PersonInfo          :
     *        Checkout                                             .
     *                       isMet                         checkout-sequence:
     *                                                     (         )
     *                      .
     *  . .             shipping                                                             .
     *
     *                                                                         (restore)         :
     *                          (      ),                  ,                    ,                 .
     *
     *                               :
     *                                 (                                         ),
     *                        "default"
     *
     * :                     ,                               default,            ?
     *
     * @param unknown_type $type_tag
     * @param unknown_type $new_person_info_variant_tag
     */

    function changeCurrentPersonInfoVariant($type_tag /* prerequisite name */ , $new_person_info_variant_tag)
    {
        if(isset($this->PrerequisitesValidationResults[$type_tag]) &&
                 $this->PrerequisitesValidationResults[$type_tag]["variant_tag"] != $new_person_info_variant_tag)
        {
            $from = $this->PrerequisitesValidationResults[$type_tag];
            $prerequisite_name = $type_tag;
            $to = array();
            $to["validatedData"] = $this->getValidatedDataStructure($prerequisite_name, $new_person_info_variant_tag);
            $this->mergePersonInfo($from["validatedData"], $to["validatedData"]);

            $this->setPrerequisitesValidationResultsItem($prerequisite_name
                                                        ,$new_person_info_variant_tag
                                                        , /* isMet */ false
                                                        , /* error_code */ ""
                                                        , /* error_message_parameters */ array()
                                                        , $to["validatedData"]);
        }
    }

    function getValidatedDataStructure($prerequisite_name, $person_info_variant_tag = NULL)
    /* prerequisite_name <=> type_id,
       (type_id, variant_tag) - UNIQUE
    */
    {
         //Delete the suffix of CCInfo prerequisites.
//$prerequisite_name_copy = $prerequisite_name;
        loadCoreFile('UUIDUtils.php');
        $prerequisite_name = UUIDUtils::cut_uuid_suffix($prerequisite_name, "js");
       /*
         Define the prerequisite variant: default, Paypal Pro, ...
         If it's possible to get it from the session, then take it.
         Otherwise - common.
        */

        global $application;
        // Possible values of $prerequisite_name:
        //  customerInfo;
        //  billingInfo;
        //  shippingInfo;
        //  shippingModuleAndMethod;
        //  paymentMethod;
        // Structure is in this case not a structure as internal arrangement, but more as
        //     a data set or an array, as in C++
        $value = array();
        if (_ml_strpos($prerequisite_name, "Module"))
        {
            switch ($prerequisite_name)
            {
                case "shippingModuleAndMethod":
                $value["method_code"] = array( 'view_tag'   => 'Shipping_Module_and_Method_Code',
                                                   'id'         => '',
                                                   'input_type_id' => '1',
                                                   'attribute_visible_name' => 'Shipping Method Code',
                                                   'attribute_description' => '',
                                                   'attribute_required' => true,
                                                   'value'      => '', //Method code, smth like 2_11 ,
                                                                       // where 2 = shipping module id
                                                                       //      11 = its submethod id
                                                   'pattern_id' => ''
                                                   ,'input_validation_func_name' => "is_valid_shipping_method_code"
                                                   ,'error_code_full' => ''
                                                   ,'error_code_short' => ''
                                                  );
                    break;
                case "paymentModule":
                $value["method_code"] = array( 'view_tag'   => 'PaymentModuleCode',
                                                   'id'         => '',
                                                   'input_type_id' => '1',
                                                   'attribute_visible_name' => 'Payment Method Code',
                                                   'attribute_description' => '',
                                                   'attribute_required' => true,
                                                   'value'      => '', //Method code, smth like 2_11 ,
                                                                       // where 2 = shipping module id
                                                                       //      11 = its submethod id
                                                   'pattern_id' => ''
                                                   ,'input_validation_func_name' => "is_valid_payment_method_code"
                                                   ,'error_code_full' => ''
                                                   ,'error_code_short' => ''
                                                  );
                    break;
                default:
                    //: output error message
                    break;
            }
        }
        elseif($prerequisite_name == 'subscriptionTopics') {
            $value['Topics'] = array( 'view_tag'   => 'SubscriptionTopics',
                                                   'id'         => '',
                                                   'input_type_id' => '1',
                                                   'attribute_visible_name' => 'Subscription Topics',
                                                   'attribute_description' => '',
                                                   'attribute_required' => false,
                                                   'value'      => '',
                                                   'pattern_id' => '',
                                                   'input_validation_func_name' => 'is_valid_no_validation',
                                                   'error_code_full' => '',
                                                   'error_code_short' => '',
                                                  );
        }
        else
        {
            $params = array();
            $params['person_info_variant_tag'] = $person_info_variant_tag;
            $params['prerequisite_name'] = $prerequisite_name;

            if(    isset($this->PrerequisitesValidationResults[$prerequisite_name]['variant_tag'])
               && !empty($this->PrerequisitesValidationResults[$prerequisite_name]['variant_tag']))
            {
                $params['PrerequisitesValidationResults'] = $this->PrerequisitesValidationResults[$prerequisite_name]['variant_tag'];
            }

            $result = execQuery('SELECT_VALIDATED_DATA_STRUCTURE', $params);

            foreach ($result as $attr)
            {
                // : ONE database person_attribute "State" is translated into TWO session attributes: "StateMenu" and "StateText".
                if($attr["view_tag"] == "State")
                {
                    $state_menu_name = "Statemenu";
                    $state_text_name = "Statetext";

                    $state_menu_pattern_id = "1"; //"integer"
                    $state_text_pattern_id = "3"; //"string"

                    $state_menu_validation_func_name = "is_valid_state_menu"; //"integer"
                    $state_text_validation_func_name = "is_valid_state_text"; //"string"

                    $value[$state_menu_name] = array
                                 (
                                    'view_tag'  => $state_menu_name
                                   ,'id'         => $attr["id"] //: sharing the same id among TWO sesssion attributes
                                   ,'input_type_id' => '3'
                                   ,'attribute_visible_name' => $attr["attribute_visible_name"]
                                   ,'attribute_description' => $attr["attribute_description"]
                                   ,'attribute_required' => $attr["attribute_required"]
                                   ,'value'     => ''
                                   ,'pattern_id'=> $state_menu_pattern_id
                                   ,'input_validation_func_name' => $state_menu_validation_func_name
                                   ,'error_code_full' => ''
                                   ,'error_code_short' => ''
                                   ,'max' => ''
                                   ,'size' => ''
                                 );

                    $value[$state_text_name] = array
                                 (
                                    'view_tag'  => $state_text_name
                                   ,'id'         => '' // $attr["id"] //: sharing the same id among TWO sesssion attributes
                                   ,'input_type_id' => '1'
                                   ,'attribute_visible_name' => $attr["attribute_visible_name"]
                                   ,'attribute_description' => $attr["attribute_description"]
                                   ,'attribute_required' => ''
                                   ,'value'     => ''
                                   ,'pattern_id'=> $state_text_pattern_id
                                   ,'input_validation_func_name' => $state_text_validation_func_name
                                   ,'error_code_full' => ''
                                   ,'error_code_short' => ''
                                   ,'max' => $attr["max"]
                                   ,'size' => $attr["size"]
                                 );
                }
                else
                {
                    $value[$attr["view_tag"]] = array(
                                                        'view_tag'  => $attr["view_tag"]
                                                       ,'id'         => $attr["id"]
                                                       ,'input_type_id' => $attr["input_type_id"]
                                                       ,'attribute_visible_name' => $attr["attribute_visible_name"]
                                                       ,'attribute_description' => $attr["attribute_description"]
                                                       ,'attribute_required' => $attr["attribute_required"]
                                                       ,'value'     => ''
                                                       ,'pattern_id'=> $attr["pattern_id"]
                                                       ,'input_validation_func_name' => $attr["input_validation_func_name"]
                                                       ,'max' => $attr['max']
                                                       ,'size' => $attr['size']
                                                       ,'error_code_full' => ''
                                                       ,'error_code_short' => ''
                                                     );
                }
            }
        }
        return $value;
    }

    /**
     * WARNING:
     * It is called statically only. Before LoadState is called. At this moment
     * it is called in the middle of the constructor.
     * It updates session data, which will be used then in LoadState and
     * will be data of the Checkout class.
     */
    /*static*/ function initSinglePrerequisiteValidationResults($variant_view_tag, $variant_tag, $variant_type_id, $variant_visible_name)
    {
        global $application;
        loadCoreFile('UUIDUtils.php');

        //Cut the suffix. CCInfo prerequisites.
        // : may be to pass in PersonInfoVariantID to getValidatedDataStructure, not the tag.
        $variant_view_tag_for_data_structure = UUIDUtils::cut_uuid_suffix($variant_view_tag, "js");

        {
            /**
             * Call either before the construcor finishes its work,
             * or satatically.
             * Work on the sesson only.
             */
            //Check, if data is already in the session:
            if(isset($this->PrerequisitesValidationResults))
//            if(modApiFunc('Session', 'is_Set', 'PrerequisitesValidationResults') === true)
            {
                $PrerequisitesValidationResults = $this->PrerequisitesValidationResults;
                // changing the visible name for checkout form fields
                // usage: customer changes the language on the checkout
                /*
                if (is_array($PrerequisitesValidationResults))
                    foreach($PrerequisitesValidationResults as $k => $v)
                        if (isset($v['validatedData']) && is_array($v['validatedData']))
                            foreach($v['validatedData'] as $kk => $vv)
                            {
                                $new_data = execQuery('SELECT_VALIDATED_DATA_VISIBLE_LABELS', array('vid' => @$v['id'], 'aid' => @$vv['id']));
                                if ($new_data)
                                {
                                    if (isset($vv['attribute_visible_name']))
                                        $PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_visible_name'] = $new_data[0]['attribute_visible_name'];
                                    if (isset($vv['attribute_description']))
                                        $PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_description'] = $new_data[0]['attribute_description'];
                                }
                            }
                */
                if (is_array($this->PrerequisitesValidationResults)) {
                    $labels = $this->getValidatedDataVisibleLabels();
                    foreach($this->PrerequisitesValidationResults as $k => $v)
                        if (isset($v['validatedData']) && is_array($v['validatedData']))
                            foreach($v['validatedData'] as $kk => $vv)
                            {
                                if (@ $labels[ $v['id'] ][ $vv['id'] ])
                                {
                                    if (isset($vv['attribute_visible_name']))
                                        $this->PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_visible_name'] = @ $labels[ $v['id'] ][ $vv['id'] ]['attribute_visible_name'];
                                    if (isset($vv['attribute_description']))
                                        $this->PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_description'] = @ $labels[ $v['id'] ][ $vv['id'] ]['attribute_description'];
                                }
                            }
                }
            }
            else
            {
                //Initialize from the beginning.
                $PrerequisitesValidationResults = array();
            }
            $PrerequisitesValidationResults[$variant_view_tag] = array
                (
                    'variant_tag'   => $variant_tag,
                    'isMet'         => false,
                    'id'            => $variant_type_id,
                    'visibleName'   => $variant_visible_name,
                    'error_code'    => '',
                    'error_message_parameters' => '',
                    'validatedData' => $this->getValidatedDataStructure($variant_view_tag_for_data_structure)
                );

            //         shipping_method,                           ,  . .
            //                                     ,                         .
            //                   CheckoutFormEditor -                              .
            if($variant_view_tag == "shippingModuleAndMethod")
            {
                $b_is_shipping_method_required = Checkout::isPrerequisiteRequired("shippingModuleAndMethod");
                if($b_is_shipping_method_required === false ||
                   Checkout::arePersonInfoTypesActive(array("shippingModuleAndMethod")) === false)
                {
                    //                             shipping method: AllInactive
                    $PrerequisitesValidationResults[$variant_view_tag]['isMet'] = true;
                    $PrerequisitesValidationResults[$variant_view_tag]['validatedData']['method_code']['value'] =
                        Checkout::getAllInactiveModuleId("shipping")
                      . "_"
                      . modApiFunc(Checkout::getAllInactiveModuleClassAPIName("shipping"), "getSingleAvailableMethodId");
                }
            }
            //         payment_method,                           ,  . .
            //                                     ,                         .
            //                   CheckoutFormEditor -                              .
            if($variant_view_tag == "paymentModule")
            {
                $b_is_payment_method_required = Checkout::isPrerequisiteRequired("paymentModule");
                if($b_is_payment_method_required === false ||
                   Checkout::arePersonInfoTypesActive(array("paymentModule")) === false)
                {
                    //                             payment method: AllInactive
                    $PrerequisitesValidationResults[$variant_view_tag]['isMet'] = true;
                    $PrerequisitesValidationResults[$variant_view_tag]['validatedData']['method_code']['value'] =
                        Checkout::getAllInactiveModuleId("payment");
                }
            }
            if($variant_view_tag == 'subscriptionTopics') {
                $b_is_subscription_required = Checkout::isPrerequisiteRequired("subscriptionTopics");
                if($b_is_subscription_required === false ||
                        Checkout::arePersonInfoTypesActive(array("subscriptionTopics")) === false) {
                    $PrerequisitesValidationResults[$variant_view_tag]['isMet'] = true;
                    $PrerequisitesValidationResults[$variant_view_tag]['validatedData']['Topics']['value'] = '';
                }

            }

            $this->PrerequisitesValidationResults = $PrerequisitesValidationResults;
            //modApiFunc('Session', 'set', 'PrerequisitesValidationResults', $PrerequisitesValidationResults);
        }
    }

    /* static */ function initPrerequisitesValidationResults()
    {
        $result = execQuery('SELECT_PREREQUISITES_VALIDATION_RESULTS', array());
        foreach ($result as $variant)
        {
            Checkout::initSinglePrerequisiteValidationResults($variant["view_tag"], $variant["variant_tag"], $variant["type_id"], $variant["visible_name"]);
        }
        /**
         * Hard-coded prerequisites
         */
        Checkout::initSinglePrerequisiteValidationResults('shippingModuleAndMethod', '', '', 'Shipping Module and Method');
        Checkout::initSinglePrerequisiteValidationResults('paymentModule', '', '', 'Payment Method');
        Checkout::initSinglePrerequisiteValidationResults('subscriptionTopics', '', '', 'Subscription Topics');
    }

    /**
     *            -   payment              Bank Account     Credit Card                  ,
     *                                       Checkout.
     * Checkout                    prerequisite,
     *                                               .
     */
    /*static*/ function initAdditionalPrerequisitesValidationResults($prerequisite_type, $force_rewrite = true)//creditCardInfo, bankAccountInfo
    {
        /* Inquire all Payment modules.
           If the method getInitPrerequisitesValidationResultsData exists - call it
           and add a corresponding prerequisite.
        */
        global $application;
        /* create the object Modules_Manager to upload all required modules */
        $mmObj = &$application->getInstance('Modules_Manager');
        $modules = Checkout::getInstalledAndActiveModulesListData("payment");
        foreach($modules as $moduleInfo)
        {
            /* Load a required file */
            $mmObj->includeAPIFileOnce($moduleInfo->name);
            /* This condition can be checked only after loading */
            if(is_callable(array($moduleInfo->name,"getAdditionalPersonInfoVariantTag")))
            {
                /* It MUST be called statically,
                 * otherwise, if modApiFunc is used, the object of this module
                 * will be created, which will lead to creation
                 * of the checkout object, so the payment/shipping module will
                 * ask the checkout object for isActive, and that will lead to
                 * the complete cycling. */
                $variant_tag = call_user_func(array($moduleInfo->name, 'getAdditionalPersonInfoVariantTag'), $prerequisite_type);
                //false                     ,                       Checkout CZ              -
                //                                    CCInfo        : AuthorizeNet, PaypalPro.
                //            ,                                     CCInfo,        ,                      -
                //         false.                    CCInfo                        .
                if($variant_tag !== false)
                {
                    $prerequisite_name = Checkout::getAdditionalPrerequisiteName($prerequisite_type, call_user_func(array($moduleInfo->name, "getUid")));
                    if ($force_rewrite
                        || !isset($this -> PrerequisitesValidationResults[$prerequisite_name]))
                    {

                        $variant_id = Checkout::getPersonInfoVariantId($prerequisite_type, $variant_tag);
                        $variant_info = Checkout::getPersonVariantInfo($variant_id);

                        $variant_view_tag = $prerequisite_name;
                        $prerequisite_data =
                        array
                        (
                            "variant_view_tag"     => $variant_view_tag
                           ,"variant_tag"          => $variant_info['variant_tag']
                           ,"variant_type_id"      => $variant_info['type_id']
                           ,"variant_visible_name" => $variant_info['visible_name']
                        );

                        Checkout::initSinglePrerequisiteValidationResults
                        (
                            $prerequisite_data["variant_view_tag"]
                           ,$prerequisite_data["variant_tag"]
                           ,$prerequisite_data["variant_type_id"]
                           ,$prerequisite_data["variant_visible_name"]
                        );
                    }
                }
            }
            else
            {
                //echo "DIE not callable: ". $moduleInfo->name . " getInitPrerequisitesValidationResultsData();" . "<br>";
            }
        }
    }

    /**
     * Returns complete info description of the specified variant.
     * As for now it isn't used and has errors. Test and correct it
     * after defining how to work with the persons.
     */
    function getPersonVariantInfo($variant_id)
    {
        $result = execQuery('SELECT_PERSON_VARIANT_INFO',array('variant_id'=>$variant_id));
        if(sizeof($result) == 1)
        {
            return $result[0];
        }
        else
        {
            return false;
        }
    }

    /*static*/ function encryptAdditionalPrerequisitesValidationResults($prerequisite_type)//creditCardInfo, bankAccountInfo
    {
        /* Inquire all Payment modules.
           If the method getInitPrerequisitesValidationResultsData exists - call it
           and add a corresponding prerequisite.
        */
        global $application;
        /* create the object Modules_Manager to upload all required modules */
        $mmObj = &$application->getInstance('Modules_Manager');
        $modules = Checkout::getInstalledAndActiveModulesListData("payment");
        foreach($modules as $moduleInfo)
        {
            /* Load a required file */
            $mmObj->includeAPIFileOnce($moduleInfo->name);
            /* This condition can be checked only after loading */
            if(is_callable(array($moduleInfo->name,"getAdditionalPersonInfoVariantTag")))
            {
                /* It MUST be called statically,
                 * otherwise, if modApiFunc is used, the object of this module
                 * will be created, which will lead to creation
                 * of the checkout object, so the payment/shipping module will
                 * ask the checkout object for isActive, and that will lead to
                 * the complete cycling. */
                $variant_tag = call_user_func(array($moduleInfo->name, 'getAdditionalPersonInfoVariantTag'), $prerequisite_type);
                //false                     ,                       Checkout CZ              -
                //                                    CCInfo        : AuthorizeNet, PaypalPro.
                //            ,                                     CCInfo,        ,                      -
                //         false.                    CCInfo                        .
                if($variant_tag !== false)
                {
                    $prerequisite_name = Checkout::getAdditionalPrerequisiteName($prerequisite_type, call_user_func(array($moduleInfo->name, "getUid")));
                    Checkout::encrypt_prerequisite_with_checkout_cz_blowfish_key($prerequisite_name);
                }
            }
            else
            {
                //echo "DIE not callable: ". $moduleInfo->name . " getInitPrerequisitesValidationResultsData();" . "<br>";
            }
        }
    }

    function setChosenShippingMethod($module_id, $method_id)
    {
        if($this->areValidShippingModuleAndMethodIDs($module_id, $method_id))
        {
            $shippingModuleAndMethod = $this->getPrerequisiteValidationResults("shippingModuleAndMethod");
            $validatedData = $shippingModuleAndMethod['validatedData'];
            $validatedData["method_code"]["value"] = $module_id . '_' . $method_id;
            $this->setPrerequisitesValidationResultsItem('shippingModuleAndMethod' /*prerequisiteName*/,
                                                         '' /*variant_tag*/,
                                                         true /*isMet*/,
                                                         '' /*error_code*/,
                                                         '' /*error_message_parameters*/,
                                                         $validatedData);
        }
    }

    /**
     * Restores the module state.
     * IMPORTANT:                         LoadState               -            ,
     *               SaveState -                                                 .
     */
    function loadState()
    {
    	global $application;

        if(modApiFunc('Session', 'is_Set', 'currentStepID'))
        {
            $this->setCurrentStepID(modApiFunc('Session', 'get', 'currentStepID'));
        }
        else
        {
            //. Is it at all correct to SET currentStepID, if its value is not even defined?
            $this->currentStepID = 1;
        }

        if(modApiFunc('Session', 'is_Set', 'currentOrderID'))
        {
            $this->currentOrderID = modApiFunc('Session', 'get', 'currentOrderID');
        }

        if(modApiFunc('Session', 'is_Set', 'currentOrderCurrencyID'))
        {
            $this->currentOrderCurrencyID = modApiFunc('Session', 'get', 'currentOrderCurrencyID');
        }
        else
        {
        	$this->currentOrderCurrencyID = NULL;
        }

        if(modApiFunc('Session', 'is_Set', 'lastPlacedOrderID'))
        {
            $this->lastPlacedOrderID = modApiFunc('Session', 'get', 'lastPlacedOrderID');
        }

        if(modApiFunc('Session', 'is_Set', 'currentCustomerID'))
        {
            $this->currentCustomerID = modApiFunc('Session', 'get', 'currentCustomerID');
        }

        if(modApiFunc('Session', 'is_Set', 'PrerequisitesValidationResults'))
        {
            $this->setPrerequisitesValidationResults(modApiFunc('Session', 'get', 'PrerequisitesValidationResults'));
            /*
            // saved for testing or emergency purposes
            if (is_array($this->PrerequisitesValidationResults))
                foreach($this->PrerequisitesValidationResults as $k => $v)
                    if (isset($v['validatedData']) && is_array($v['validatedData']))
                        foreach($v['validatedData'] as $kk => $vv)
                        {
                            $new_data = execQuery('SELECT_VALIDATED_DATA_VISIBLE_LABELS', array('vid' => @$v['id'], 'aid' => @$vv['id']));
                            if ($new_data)
                            {
                                if (isset($vv['attribute_visible_name']))
                                    $this->PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_visible_name'] = $new_data[0]['attribute_visible_name'];
                                if (isset($vv['attribute_description']))
                                    $this->PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_description'] = $new_data[0]['attribute_description'];
                            }
                        }
            */

            if (is_array($this->PrerequisitesValidationResults)) {
                $labels = $this->getValidatedDataVisibleLabels();
                foreach($this->PrerequisitesValidationResults as $k => $v)
                    if (isset($v['validatedData']) && is_array($v['validatedData']))
                        foreach($v['validatedData'] as $kk => $vv)
                        {
                            if (@ $labels[ $v['id'] ][ $vv['id'] ])
                            {
                                if (isset($vv['attribute_visible_name']))
                                    $this->PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_visible_name'] = @ $labels[ $v['id'] ][ $vv['id'] ]['attribute_visible_name'];
                                if (isset($vv['attribute_description']))
                                    $this->PrerequisitesValidationResults[$k]['validatedData'][$kk]['attribute_description'] = @ $labels[ $v['id'] ][ $vv['id'] ]['attribute_description'];
                            }
                        }
            }
        }
        else
        {
//	        if(!modApiFunc('Session', 'is_Set', 'PrerequisitesValidationResults'))
//	        {
	            //: ERROR arises if not installed
	            Checkout::initPrerequisitesValidationResults();

	            //                                ,
	            // $this->initStoreOwnerInfo();
	            //           LoadState
//	        }
//            $err_params = array(
//                                "CODE"    => "CHECKOUT_011"
//                               );
//            _fatal($err_params);

            //$this->initPrerequisitesValidationResults();
        }

        Checkout::initAdditionalPrerequisitesValidationResults("creditCardInfo", false);
        Checkout::initAdditionalPrerequisitesValidationResults("bankAccountInfo", false);

        if(modApiFunc('Session', 'is_Set', 'CheckoutOrderSearch'))
        {
            $this->order_search_filter = modApiFunc('Session', 'get', 'CheckoutOrderSearch');
        }
        else
        {
            $this->order_search_filter = array(
                'filter_status_id' => '1'
               ,'status_id' => null
               ,'payment_status_id' => null
               ,'from_day' => null
               ,'from_month' => null
               ,'from_year' => null
               ,'to_day' => null
               ,'to_month' => null
               ,'to_year' => null
               ,'order_id' => null
               ,'search_by' => 'status'
            );
            modApiFunc('Session', 'set', 'CheckoutOrderSearch', $this->order_search_filter);
        }

        if(modApiFunc('Session', 'is_Set', 'CheckoutCustomerSearch'))
        {
            $this->customer_search_filter = modApiFunc('Session', 'get', 'CheckoutCustomerSearch');
        }
        else
        {
            $this->customer_search_filter = array(
               'search_by' => 'letter' // 'letter' or 'field'
              ,'letter' => null
              ,'field_name' => null
              ,'field_value' => null
            );
            modApiFunc('Session', 'set', 'CheckoutCustomerSearch', $this->customer_search_filter);
        }

        if(modApiFunc('Session', 'is_Set', 'CurrentPaymentModuleSettingsViewName'))
        {
            $this->setCurrentPaymentModuleSettingsViewName(modApiFunc('Session', 'get', 'CurrentPaymentModuleSettingsViewName'));
        }
        else
        {
            $this->CurrentPaymentModuleSettingsViewName = NULL;
        }

        if(modApiFunc('Session', 'is_Set', 'CurrentPaymentShippingModuleSettingsUID'))
        {
            $this->setCurrentPaymentShippingModuleSettingsUID(modApiFunc('Session', 'get', 'CurrentPaymentShippingModuleSettingsUID'));
        }
        else
        {
            $this->CurrentPaymentShippingModuleSettingsUID = NULL;
        }

        if(modApiFunc('Session', 'is_Set', 'CustomPaymentGatewayPageContents'))
        {
            $this->setCustomPaymentGatewayPageContents(modApiFunc('Session', 'get', 'CustomPaymentGatewayPageContents'));
        }
        else
        {
            $this->CustomPaymentGatewayPageContents = NULL;
        }


        if(modApiFunc('Session', 'is_Set', 'CurrentShippingModuleSettingsViewName'))
        {
            $this->setCurrentShippingModuleSettingsViewName(modApiFunc('Session', 'get', 'CurrentShippingModuleSettingsViewName'));
        }
        else
        {
            $this->CurrentShippingModuleSettingsViewName = NULL;
        }

        if(modApiFunc('Session', 'is_Set', 'OrdersForDelete'))
        {
            $this->ordersId = modApiFunc('Session', 'get', 'OrdersForDelete');
        }
        else
        {
            $this->ordersId = array();
        }

        if(modApiFunc('Session', 'is_Set', 'DeleteOrdersFlag'))
        {
            $this->DeleteOrdersFlag = modApiFunc('Session', 'get', 'DeleteOrdersFlag');
            modApiFunc('Session', 'un_Set', 'DeleteOrdersFlag');
        }
        else
        {
            $this->DeleteOrdersFlag = 'false';
        }

        if(modApiFunc('Session', 'is_Set', 'CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED'))
        {
            $this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED = modApiFunc('Session', 'get', 'CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED');
        }

        $request = $application->getInstance('Request');
        $checkout_cz_blowfish_key = $request->getValueByKey("CHECKOUT_CZ_BLOWFISH_KEY");
        $this -> initCheckoutCZBlowfishKey($checkout_cz_blowfish_key);
//        //:                                         ,                         ,
//        //                                          -                    .
//        //                          .                   prerequisite.
//        $this->initStoreOwnerInfo();
    }

    function getValidatedDataVisibleLabels()
    {
        $data = execQuery('SELECT_VALIDATED_DATA_ALL_VISIBLE_LABELS', array());
        $result = array();
        foreach ($data as $rec) {
            $result[ $rec['person_info_variant_id'] ][ $rec['person_attribute_id'] ] = array(
                'attribute_visible_name' => $rec['attribute_visible_name'],
                'attribute_description' => $rec['attribute_description'],
            );
        }
        return $result;
    }

    /**
     * Saves the module state.
     */
    function saveState()
    {
        modApiFunc('Session', 'set', 'currentStepID', $this->currentStepID);
        modApiFunc('Session', 'set', 'currentOrderID', $this->currentOrderID);
        modApiFunc('Session', 'set', 'currentOrderCurrencyID', $this->currentOrderCurrencyID);
        modApiFunc('Session', 'set', 'lastPlacedOrderID', $this->lastPlacedOrderID);
        modApiFunc('Session', 'set', 'currentCustomerID', $this->currentCustomerID);
        modApiFunc('Session', 'set', 'PrerequisitesValidationResults', $this->PrerequisitesValidationResults);
        modApiFunc('Session', 'set', 'CheckoutOrderSearch', $this->order_search_filter);
        modApiFunc('Session', 'set', 'CheckoutCustomerSearch', $this->customer_search_filter);
        modApiFunc('Session', 'set', 'CurrentPaymentModuleSettingsViewName', $this->CurrentPaymentModuleSettingsViewName);
        modApiFunc('Session', 'set', 'CurrentPaymentShippingModuleSettingsUID', $this->CurrentPaymentShippingModuleSettingsUID);
        modApiFunc('Session', 'set', 'CustomPaymentGatewayPageContents', $this->CustomPaymentGatewayPageContents);
        modApiFunc('Session', 'set', 'CurrentShippingModuleSettingsViewName', $this->CurrentShippingModuleSettingsViewName);
        modApiFunc('Session', 'set', 'OrdersForDelete', $this->ordersId);
        modApiFunc('Session', 'set', 'DeleteOrdersFlag', $this->DeleteOrdersFlag);
        //                 .                      .      ,                       (                       ).
        if(isset($this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED))
        {
            modApiFunc('Session', 'set', 'CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED', $this->CHECKOUT_CZ_BLOWFISH_KEY_IS_ALREADY_DEFINED);
        }
    }

    /**
     * Checks if module has been installed.
     *
     * @ finish the functions on this page
     * @return bool TRUE if module has been installed, FALSE otherwise
     */
    function isInstalled()
    {
    	return Modules_Manager::isModuleInstalled("Checkout");
    }

    /* static */ function getActiveModules($modulesType)
    {
        $sql_result = execQuery('SELECT_ACTIVE_PM_SM_MODULES', array('modulesType'=>$modulesType));

        $result = array();
        for ($i=0; $i<sizeof($sql_result); $i++)
        {
            $result[$sql_result[$i]["module_id"]] = array
            (
                "module_id"              => $sql_result[$i]["module_id"]
               ,"module_class_name"      => $sql_result[$i]["module_class_name"]
            );
        }

        if(empty($result))
        {
        	Checkout::activateAllInactiveModule($modulesType);
        	$result = Checkout::getActiveModules($modulesType);
        	return $result;
        }
        else
        {
        	return $result;
        }
    }

    function activateAllInactiveModule($module_type)
    {
        $all_inactive_module_id = Checkout::getAllInactiveModuleId($module_type);
        $all_inactive_module_name = Checkout::getAllInactiveModuleClassAPIName($module_type);
        $all_inactive_module  = array
        (
            "module_id"              => $all_inactive_module_id
           ,"module_group"           => $module_type
//               ,"b_is_active"            => true
           ,"b_is_selected"          => true
           ,"sort_order"             => 0
        );
        $modules = array();
        $modules[$all_inactive_module_id] = $all_inactive_module;
        Checkout::setSelectedModules($modules, $module_type, true);
        Checkout::update_pm_sm_currency_settings($all_inactive_module_id, modApiStaticFunc($all_inactive_module_name, "getInitialCurrencySettings"));
        Checkout::setModuleActive($all_inactive_module_id, true);
    }

    //modulesType -   lower case
    function getSelectedModules($modulesType)
    {
        $sql_result = execQuery('SELECT_SELECTED_PM_SM_MODULES', array('modulesType'=>$modulesType));
        $result = array();
        $default_currency_settings = pm_sm_api::getInitialCurrencySettings();
        for ($i=0; $i<sizeof($sql_result); $i++)
        {
            $result[$sql_result[$i]["module_id"]] = array
            (
                "module_id"              => $sql_result[$i]["module_id"]
               ,"module_class_name"      => $sql_result[$i]["module_class_name"]
               ,"module_group"           => $modulesType
               ,"b_is_active"            => $sql_result[$i]["status_active_value_id"] == 1 ? true : false
//               ,"b_is_selected"          => $result[$i]["status_selected_value_id"] == 1 ? true : false
               ,"sort_order"             => $sql_result[$i]["sort_order"]
            );
            $result[$sql_result[$i]["module_id"]] = array_merge_recursive($result[$sql_result[$i]["module_id"]], $default_currency_settings);
        }

        //          accepted currencies
        $sql_result = execQuery('SELECT_PM_SM_ACCEPTED_CURRENCIES', NULL);
        $accepted_currencies = array();
        for ($i=0; $i<sizeof($sql_result); $i++)
        {
            if(!isset($accepted_currencies[$sql_result[$i]["module_id"]]))
            {
                $accepted_currencies[$sql_result[$i]["module_id"]] = array();
            }
            $accepted_currencies[$sql_result[$i]["module_id"]][] = array
            (
                "currency_code"     => $sql_result[$i]["currency_code"]
               ,"currency_status"   => $sql_result[$i]["currency_status"]
            );
        }
        //                               :
        //                    .
        foreach($accepted_currencies as $module_id => $currencies)
        {
            if(isset($result[$module_id]))
            {
                $result[$module_id]["accepted_currencies"] = $currencies;
            }
        }

        //                                                 .
        $sql_result = execQuery('SELECT_PM_SM_CURRENCY_ACCEPTANCE_RULES', NULL);
        $rules = array();
        for ($i=0; $i<sizeof($sql_result); $i++)
        {
        	if(!isset($rules[$sql_result[$i]["module_id"]]))
        	{
        		$rules[$sql_result[$i]["module_id"]] = array();
        	}
        	$rules[$sql_result[$i]["module_id"]][] = array
        	(
        	    "rule_name"     => $sql_result[$i]["rule_name"]
        	   ,"rule_selected" => $sql_result[$i]["rule_selected"]
        	);
        }
        //                               :
        //                    .
        foreach($rules as $module_id => $rules)
        {
        	if(isset($result[$module_id]))
        	{
        		$result[$module_id]["currency_acceptance_rules"] = $rules;
        	}
        }


        //If such modules don't exist, make all-inactive be selected and active. Save the settings.
        if(empty($result))
        {
            Checkout::activateAllInactiveModule($modulesType);
            return $this->getSelectedModules($modulesType);
        }
        else
        {
            return $result;
        }
    }

    /**
     * Installs the module.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Checkout::getTables() instead of $this->getTables()
     *
     * @ copy to create only necessary tables and data.
     */
    function install()
    {
        _use(dirname(__FILE__).'/includes/install.inc');
    }

    /**
     * Uninstalls the module.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Checkout::getTables() instead of $this->getTables().
     *
     * @ finish the functions on this page
     */
    function uninstall()
    {
    }

    /**
     * Sets the current checkout step ID.
     *
     * @return mixed Current step ID or NULL if category is not selected
     */
    function setCurrentStepID($step_id)
    {
        $this->currentStepID = $step_id;
    }

    /**
     * Gets the current checkout step ID.
     *
     * @return mixed Current step ID or NULL if step is not defined
     */
    function getCurrentStepID()
    {
        return $this->currentStepID;
    }

    /**
     * Returns info about current used chain Checkout.
     * For each step :
     * ID,
     * Name,
     * Link (a reference to the page, by clicking it, a user
     *       goes to the appropriate step checkout)
     */
    function getStepsInfo()
    {
        global $application;
        $prerequisitesINI = $this->getPrerequisitesINI();
        $retval = array();
        $step_id = 0;
        foreach($prerequisitesINI as $prerequisiteINI)
        {
            $step_id++;
            $request = new Request();
            $request->setAction('SetCurrStep');
            $request->setView('CheckoutView');
            $request->setKey('step_id', $step_id);
            $request = $this->appendCheckoutCZGETParameters($request);

            $retval[] = array("ID" => $step_id,
                              "Name" => $step_id,
                              "Link" => $request->getURL()
                             );
        }
        return $retval;
    }

    /**
     * Get/Set function. See the description of the variable
     * CustomPaymentGatewayPageContents.
     */
    function setCustomPaymentGatewayPageContents($NewCustomPaymentGatewayPageContents)
    {
        $this->CustomPaymentGatewayPageContents = $NewCustomPaymentGatewayPageContents;
    }

    /**
     *  Get/Set function. See the description of the variable
     * CustomPaymentGatewayPageContents.
     */
    function getCustomPaymentGatewayPageContents()
    {
        return $this->CustomPaymentGatewayPageContents;
    }

    /**
     * Sets currently selected Order ID.
     */
    function setCurrentOrderID($order_id)
    {
        $this->currentOrderID = $order_id;
    }

    function setCurrentOrderCurrencyID($order_currency_id)
    {
        $this->currentOrderCurrencyID = $order_currency_id;
    }

    /**
     * Sets last placed order Order ID.
     */
    function setLastPlacedOrderID($order_id)
    {
        $this->lastPlacedOrderID = $order_id;
        if ($order_id)
            modApiFunc('Session', 'set', '_lastPlacedOrderID', $order_id);
    }

    /**
     * Returns last placed Order ID.
     */
    function getLastPlacedOrderID()
    {
        return $this->lastPlacedOrderID;
    }

    function getPrerequisitesListForStep($step_id)
    {
        $prerequisitesForAllSteps = $this->getPrerequisitesINI();
		if($step_id>4) $step_id=4;
        if(!isset($prerequisitesForAllSteps[$step_id]))
        {
            $err_params = array(
                                "CODE"    => "CHECKOUT_CONFIG_INI_001",
                                "FILE"    => "checkout-config.ini",
                                "SECTION" => "CheckoutNormal",
                                "DIRECTIVE" => "Option-prerequisite" . $step_id
                                );
            _fatal($err_params, $step_id);
        }
        else
        {
            return $prerequisitesForAllSteps[$step_id];
        }
    }

    /**
     *                                 checkout-sequence,         -required.
     *       - required.
     */
    function isPrerequisiteRequired($prerequisite_name)
    {
        $prerequisites = Checkout::getPrerequisitesINI();
        foreach($prerequisites as $step_id => $names)
        {
            if(in_array($prerequisite_name, $names))
            {
                return true;
            }
        }
        return false;
    }

    function getPrerequisitesINI()
    {
        global $application;
        $prerequisites = array();
        $prerequisites_tag = "prerequisite";

        $template = $application->getBlockTemplate('CheckoutView');

        if ($template == null)
            return $prerequisites;

        // Move the check to application and make it more strict.
        for($i=1 /* 2 */ ;;$i++)
        {
            $prerequisite = $application->getBlockOption($template, $prerequisites_tag . $i);
            if( $prerequisite === null)
            {
                break;
            }
            else
            {
                if(trim($prerequisite) == "")
                {   //to avoid array(0=>"") as a result
                    $prerequisites[$i] = array();
                }
                else
                {
                    $tmp = explode(",", trim($prerequisite));
                    $prerequisites[$i] = array();
                    foreach($tmp as $prerequisite_name)
                    {
                        $prerequisites[$i][] = trim($prerequisite_name);
                    }
                }
                //break;
            }
        }

        return $prerequisites;
    }

    function setPrerequisitesValidationResultsItem($index, $variant_tag, $isMet, $error_code, $error_message_parameters, $validatedData)
    //"customer-info", data)
    {
        // check if $this->PrerequisitesValidationResults array exists at all.
        $this->PrerequisitesValidationResults[$index]['variant_tag'] = $variant_tag;
        $this->PrerequisitesValidationResults[$index]['isMet'] = $isMet;
        $this->PrerequisitesValidationResults[$index]['error_code'] = $error_code;
        $this->PrerequisitesValidationResults[$index]['error_message_parameters'] = $error_message_parameters;
        $this->PrerequisitesValidationResults[$index]['validatedData'] = $validatedData;
        // replace with $this->PrerequisitesValidationResults[$index] = array (...) ?
    }

//    function setPrerequisitesValidationResultsItemError($index, $error_code, $error_message_parameters)
//    {
//        // check if $this->PrerequisitesValidationResults array exists at all.
//        $this->PrerequisitesValidationResults[$index]['error_code'] = $error_code;
//        $this->PrerequisitesValidationResults[$index]['error_message_parameters'] = $error_message_parameters;
//        // replace with $this->PrerequisitesValidationResults[$index] = array (...) ?
//    }

    function is_valid_shipping_method_code($val)
    {
        //// Module and Method
        $shipping_module_and_method_pattern = "/^([0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12})_(\d+)$/";

        $matches = array();
        if(preg_match($shipping_module_and_method_pattern, $val, $matches))
        {
            if ($this->areValidShippingModuleAndMethodIDs($matches[1], $matches[2])
                || $matches[1] == 'F3B38526-3910-40D4-869B-0A7133A9CABE')
            {
                return true;
            }
            else
            {
                //Method id forge attempt.
                return array('error_code_full' => 'CHECKOUT_ERR_SHIPPING_METHOD_002',
                             'error_code_short' => 'CHECKOUT_ERR_SHIPPING_METHOD_002',
                             'error_message_parameters' => array($matches[1], $matches[2]));
            }
        }
        else
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_002'

            ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_002'
//            ,'error_message_parameters' => array($this->PrerequisitesValidationResults["shippingModuleAndMethod"]["validatedData"]["method_code"]["view_tag"], "shippingModuleAndMethod")
                        );
        }
    }

    function is_valid_payment_method_code($val)
    {
        //// Module (without Method)
        $payment_module_pattern = "/^([0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12})$/";

        $matches = array();
        if (preg_match($payment_module_pattern, $val, $matches))
        {
            $gc_pm_id = modApiFunc("Checkout", "getGiftCertificatePaymentModuleId");
            $total_to_pay = modApiFunc("Checkout", "getOrderPrice", "TotalToPay", modApiFunc("Localization", "getMainStoreCurrency"));
            if (floatval($total_to_pay) == 0.0
                && $gc_pm_id == $matches[1])
            {
                return true;
            }
            else if ($this->isValidPaymentModuleID($matches[1]))
            {
                return true;
            }
            else
            {
                return array('error_code_full' => 'CHECKOUT_ERR_PAYMENT_METHOD_002',
                             'error_code_short' => 'CHECKOUT_ERR_PAYMENT_METHOD_002',
                             'error_message_parameters' => array($matches[1]));
            }
        }
        else
        {
            return array('error_code_full' => 'CHECKOUT_ERR_PAYMENT_METHOD_001'
                        ,'error_code_short' => 'CHECKOUT_ERR_PAYMENT_METHOD_001');
        }
    }

    function is_valid_email($val)
    {
        //// Email
        if(!empty($val))
        {
            if(modApiFunc("Users", "isValidEmail", $val))
            {
                return true;
            }
            else
            {
                return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_002'
                ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_002'
                ,'error_message_parameters' => array());//array($this->PrerequisitesValidationResults["shippingModuleAndMethod"]["validatedData"]["method_code"]["view_tag"], "shippingModuleAndMethod"));
            }
        }
        else
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_001'
            ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_001');
        }
    }

    function is_valid_street_line_1($val)
    {
        return true;
    }

    function is_valid_street_line_2($val)
    {
        return true;
    }

    function is_valid_city($val)
    {
        return true;
    }

    function is_valid_state($val)
    {
        //                  .
        //          ,       ID                            .  . .                  ID          _POST
        //            ID/             (               )                                             .
        //:                                                                                                  ?
        return true;
    }

    function is_valid_state_menu($val, $country_id)
    //: "State Menu" attribute strongly depends on "Country" attribute
    {
        //Its necessary to know the country.
        //The country ID should be validated. The cases when the country ID in _POST
        // follows the state ID/name (both of them are correct) should be processed correctly and separately.
        //: is it possible to force the attribute validation, on which the current validated attribute depends?
        $states = modApiFunc("Location", "getStates", $country_id);
        if((!is_array($states)) || (sizeof($states) == 0))
        {
            return true;
        }
        else
        {
            if(empty($val))
            {
                return array('error_code_full' => 'CHECKOUT_ERR_DROPDOWN_FULL_002'
                            ,'error_code_short' => 'CHECKOUT_ERR_DROPDOWN_SHORT_002');
            }
            else
            {
                $sl = modApiFunc("Location", "getStates", $country_id);
                if(array_key_exists($val, $sl))
                {
                    return true;
                }
                else
                {
                    return array('error_code_full' => 'CHECKOUT_ERR_DROPDOWN_FULL_001'
                                ,'error_code_short' => 'CHECKOUT_ERR_DROPDOWN_SHORT_001');
                }
            }
        }
    }

    function is_valid_state_text($val, $country_id)
    //: "State Text" attribute strongly depends on "Country" attribute
    {
        //: force the check of "State Menu" before checking "State Text". Because, "State Menu"
        // can be empty only if "State Menu"="Manual Input"

        //If at least one state of this country exists in the database,
        // return true. It's necessary to avoid outputting an error. It can
        // occur, because at this moment (Feb. 2006) from one DB-attribute "State"
        // is created two session-attributes "State Menu" and "State Text". A customer
        // should have chosen a state from the list "State Menu".
        $states = modApiFunc("Location", "getStates", $country_id);
        if(is_array($states) && (sizeof($states) > 0))
        {
            return true;
        }
        else
        {
            $hide_state = modApiFunc('Settings', 'getParamValue', 'CHECKOUT_PROCESS', 'DO_NOT_SHOW_EMPTY_STATE_FIELD');
            if(empty($val) && $hide_state !== 'YES')
            {
                return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_001'
                            ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_001');
            }
            else
            {
                return true;
            }
        }
    }

    function is_valid_postcode($val)
    {
        return true;
    }

    //Returns the Javascript-code to synchronize the lists "Countries" and "States"
    function getJavascriptSynchronizeCountriesAndStatesLists()
    {
        return
        "<script type=\"text/javascript\">\n".
        "<!--\n".

        "function removeGroups(select_id)". "\n".
        "{". "\n".
        "". "\n".
        "   try". "\n".
        "   {". "\n".
        "      var select_list = document.getElementById(select_id);". "\n".
        "      while (select_list.firstChild) { select_list.removeChild(select_list.firstChild); }  ". "\n".
        "   }". "\n".
        "   catch(er){ }". "\n".
        "}". "\n".

        "function refreshStatesList(country_list_id, state_list_id, state_input_id)". "\n".
        "{". "\n".
        "    var country_list = document.getElementById(country_list_id);". "\n".
        "    if (! country_list) return;\n".
        "    var country_id   = country_list.options[country_list.selectedIndex].value;". "\n".
        "    if (!country_id) return; ". "\n".
        "". "\n".
        "    var state_list = document.getElementById(state_list_id);". "\n".
        "    if(!state_list)". "\n".
        "        return;". "\n".
        "    var state_list_tr = document.getElementById('tr_' + state_list_id);". "\n".
        "    //Save currently selected element". "\n".
//        "    var state_list_selected_id = state_list.selectedIndex;". "\n".
        "    var state_list_selected_id = state_list.value;". "\n".
        "    var state_input_text_field = document.getElementById(state_input_id);". "\n".
        //"    state_list.options.length = 0;". "\n".
        //"    state_list.length = 0;". "\n".
        "    removeGroups(state_list_id);". "\n".
        "    if(countryIdToStatesIdArray[country_id] == undefined)". "\n".
        "    {". "\n".
        "        //Country has no states. Activate \"Manual Input\" control.". "\n".
        "        //Hide \"State Menu\"". "\n".
////        "        state_list.style.visibility='hidden';". "\n".
        "        if(state_list)state_list.style.display='none';". "\n".
        "        if(state_list_tr)state_list_tr.style.display='none';". "\n".
//:
//  work out a variant with states, so that, when using Javascript, the values of both attributes
// state_menu and state_text are passed to the POST data.
/*   var form = document.forms['checkout'];

   var el = document.createElement("input");
   el.type = "hidden";
   el.name = "myHiddenField";
   el.id = "myHiddenField";
   el.value = "myValue";
   form.appendChild(el);        */


        "        //Unhide manual input element". "\n".
//        "        state_input_text_field.style.visibility='visible'". "\n".
        "        if(state_input_text_field) { ".  "\n".
        "            state_list.value = ''; ".  "\n".
        "            state_input_text_field.style.display='block';". "\n".
        "        } "."\n".
        "". "\n".
        "    }". "\n".
        "    else". "\n".
        "    {". "\n".
        "        //Hide manual input element.". "\n".
        "        if(state_list)state_list.style.display='block';". "\n".
        "        if(state_list_tr)state_list_tr.style.display='';". "\n".
//        "        state_list.style.visibility='visible';". "\n".
        "        //Unhide \"State Menu\" element". "\n".
//        "        state_input_text_field.style.visibility='hidden'". "\n".
        "        if(state_input_text_field)". "\n".
        "            state_input_text_field.style.display='none'". "\n".
        "". "\n".
        "        var state_id_array = countryIdToStatesIdArray[country_id];". "\n".
        "        if (!state_list_selected_id)\n".
        "        {\n".
        "           state_list_selected_id = defaultStatesIdArray[country_id];\n".
        "        }\n".
        "        for(i=0; i< state_id_array.length; i+=1)". "\n".
        "        {". "\n".
        "            state_list.options[i] = new Option(statesArray[state_id_array[i]], state_id_array[i]);". "\n".
        "            if(state_id_array[i] == state_list_selected_id)". "\n".
        "            {". "\n".
        "                 state_list.options[i].selected = true;" . "\n".
        "            }". "\n".
        "        }". "\n".
        "    }". "\n".
        "}".
        "//-->\n".
        "</script>";
    }

    //Returns the Javascript-code to synchronize two different Person Info.
    function getJavascriptCopyPersonInfo()
    {
        return file_get_contents(dirname(__FILE__).'/includes/JavascriptCopyPersonInfo.html');
    }

    //Returns the HTML-code to choose a country.
    function genCountrySelectList($country_id, $with_states_only = false, $bWithAllOtherCountry = false)
    {
        global $application;
        //WARNING:
        //  Country ="" (empty string) means that user have not been prompted yet to
        //      choose a country

        $value = "";
        $countries = modApiFunc("Location", "getCountries", true, array(), $with_states_only);

        if (!$country_id)
        {
            $country_id = modApiFunc("Location", "getDefaultCountryId");
        }

        foreach($countries as $_country_id => $_country_name)
        {
            $Selected = ($country_id == $_country_id) ? 'selected="selected"' : "";
            $value .= "<option value='" . $_country_id . "' " . $Selected . ">" . $_country_name . "</option>";
        }
        if($bWithAllOtherCountry === true)
        {
            $Selected = ($country_id == modApiFunc("Location", "get_constant", "ALL_OTHER_COUNTRIES_COUNTRY_ID")) ? 'selected="selected"' : "";
            $value = "<option value='" . modApiFunc("Location", "get_constant", "ALL_OTHER_COUNTRIES_COUNTRY_ID") . "' " . $Selected . ">" . getMsg('SYS',"COUNTRY_ALL_LABEL") . "</option>"
                    .$value;
        }
        return $value;
    }

    //Returns the HTML-code to select the type of credit card.
    //@ to do a setting tool for this list in AZ
    function genCreditCardTypeSelectList($cc_type_id)
    {
        $options = modApiFunc("Configuration", "getCreditCardSettings");
        $value = "";
        foreach($options as $id => $info)
        {
            $Selected = ($id == $cc_type_id) ? 'selected="selected"' : "";
            $value .= "<option value='" . $id . "' " . $Selected . ">" . $info["name"] . "</option>";
        }
        return $value;
    }

    function getMonthNames()
    {
        global $application;
        $mr = &$application->getInstance('MessageResources');
        return array("01" => $mr->getMessage("GENERAL_MONTH_01")
                    ,"02" => $mr->getMessage("GENERAL_MONTH_02")
                    ,"03" => $mr->getMessage("GENERAL_MONTH_03")
                    ,"04" => $mr->getMessage("GENERAL_MONTH_04")
                    ,"05" => $mr->getMessage("GENERAL_MONTH_05")
                    ,"06" => $mr->getMessage("GENERAL_MONTH_06")
                    ,"07" => $mr->getMessage("GENERAL_MONTH_07")
                    ,"08" => $mr->getMessage("GENERAL_MONTH_08")
                    ,"09" => $mr->getMessage("GENERAL_MONTH_09")
                    ,"10" => $mr->getMessage("GENERAL_MONTH_10")
                    ,"11" => $mr->getMessage("GENERAL_MONTH_11")
                    ,"12" => $mr->getMessage("GENERAL_MONTH_12"));
    }

    //Returns the HTML-code to select a month.
    function genMonthSelectList($selected_id)
    {
        $options = $this->getMonthNames();
        $value = "";
        foreach($options as $id => $name)
        {
            $Selected = ($id == $selected_id) ? 'selected="selected"' : "";
            $value .= "<option value='" . $id . "' " . $Selected . ">" . $name . "</option>";
        }
        return $value;
    }

    function genCustomFieldValues($values, $selected)
    {
        $value = "";
        foreach($values as $i => $v)
        {
            $Selected = ($v == $selected) ? 'selected="selected"' : "";
            $value .= "<option value='" . $v . "' " . $Selected . ">" . $v . "</option>";
        }
        return $value;
    }

    function getCCYearNames()
    {
    	$cur_year_4digits = date("Y");
    	$start = $cur_year_4digits;
    	$size = 10;
        $res = array();
        for($i=0; $i< $size; $i++)
        {
        	$res[$start + $i] = $start + $i;
        }
        return $res;
    }

    function getCCValidFromYearNames()
    {
        $cur_year_4digits = date("Y");
        $size = 10;
        $start = $cur_year_4digits - $size + 1;
        $res = array();
        for($i=0; $i< $size; $i++)
        {
            $res[$start + $i] = $start + $i;
        }
        return $res;
    }

    //Returns the HTML-code to select a year.
    function genYearSelectList($selected_id)
    {
        $value = "";
        $options = $this->getCCYearNames();
        foreach($options as $id => $name)
        {
            $Selected = ($id == $selected_id) ? 'selected="selected"' : "";
            $value .= "<option value='" . $id . "' " . $Selected . ">" . $name . "</option>";
        }
        return $value;
    }

    function genValidFromYearSelectList($selected_id)
    {
        $value = "";
        $options = $this->getCCValidFromYearNames();
        foreach($options as $id => $name)
        {
            $Selected = ($id == $selected_id) ? 'selected="selected"' : "";
            $value .= "<option value='" . $id . "' " . $Selected . ">" . $name . "</option>";
        }
        return $value;
    }

    //Returns HTML-code to choose a country. The states are groupped by countries.
    //  For those countries, that have no specified state, it is outputted only one item:
    //  "Manual input"
    function genStateSelectList($state_id, $country_id, $bWithAllOtherState = false)
    //: 'state' "person attribute" strongly depends on 'Country' "person attribute".
    {
        global $application;
        //WARNING:
        //  State ="" (empty string) means that user have not been prompted yet to
        //      choose a state
        //  State =NULL (NULL value, MySQL, PHP) means that user already chose
        //  the Country with no states and inputed the State name manually.
        $obj = &$application->getInstance('MessageResources');
        $MANUAL_STATE_INPUT_MESSAGE = $obj->getMessage("CHECKOUT_STATE_001");

//        $value = "<select name='state'>";
        $value = "";
        $countries = modApiFunc("Location", "getCountries"); //modApiFunc("Location", "getCountries");
        $bThereIsACountryWithoutStates = false;
        $ManualStateInputSelected = false;

        if (!$country_id)
        {
            $country_id = modApiFunc("Location", "getDefaultCountryId");
        }
        if (!$state_id)
        {
            $state_id = modApiFunc("Location", "getDefaultStateId", $country_id);
        }

        foreach($countries as $_country_id => $_country_name)
        {
            $states = modApiFunc("Location", "getStates", $_country_id); //modApiFunc("Location", "getStates", $_country_id);
            if(empty($states))
            {
                $bThereIsACountryWithoutStates = true;
                $ManualStateInputSelected = ($country_id == $_country_id && $state_id === NULL) ? 'selected="selected"' : "";
//                $bSelected = ($country_id == $_country_id && $state_id === NULL) ? "SELECTED" : "";
//                $value .= "<option value='NULL' $bSelected>" . $MANUAL_STATE_INPUT_MESSAGE . "</option>";
            }
            else
            {
                $value .= "<optgroup label='" . $_country_name. "'>";
                foreach($states as $_state_id => $_state_name)
                {
                    $Selected = ($country_id == $_country_id && $state_id == $_state_id) ? 'selected="selected"' : "";

                    $value .= "<option value='" .$_state_id. "' " . $Selected . ">" .$_state_name. "</option>";
                }
                if($bWithAllOtherState === true)
                {
                    $Selected = ($country_id == $_country_id && $state_id == modApiFunc("Location", "get_constant", "ALL_OTHER_STATES_STATE_ID")) ? 'selected="selected"' : "";
                    $value = "<option value='" .modApiFunc("Location", "get_constant", "ALL_OTHER_STATES_STATE_ID"). "' " . $Selected . ">" .getMsg('SYS',"STATE_ALL_LABEL"). "</option>"
                            .$value;
                }
                $value .= "</optgroup>";
            }

        }
        if($bThereIsACountryWithoutStates)
        {
            $value = "<option value='NULL' " . $ManualStateInputSelected . ">" . $MANUAL_STATE_INPUT_MESSAGE . "</option>" . $value;
        }
//        $value .= "</select>";
        return $value;
    }

    function is_valid_country($val)
    {
        //The country id is given at the input.
        //Make a query to the DB. If the country with such ID is already in the DB, return true.
        if(empty($val))
        {
            return array('error_code_full' => 'CHECKOUT_ERR_DROPDOWN_FULL_002'
                        ,'error_code_short' => 'CHECKOUT_ERR_DROPDOWN_SHORT_002');
        }
        else
        {
            $cl = modApiFunc("Location", "getCountries");
            if(array_key_exists($val, $cl))
            {
                return true;
            }
            else
            {
                return array('error_code_full' => 'CHECKOUT_ERR_DROPDOWN_FULL_001'
                            ,'error_code_short' => 'CHECKOUT_ERR_DROPDOWN_SHORT_001');
            }
        }

    }

    function is_valid_phone($val)
    {
        //If the value is an empty string, ask to input again.
        if(empty($val))
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_001'
                        ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_001');
        }
        else
        {
            return true;
        }
    }

    function is_valid_string1024_null($val)
    {
        /*
        //how to portably match new line character?
        $pattern = "/^(.{0,1024})$/";

        $matches = array();
        if(preg_match($pattern, $val, $matches))
        {
            return true;
        }*/
        if(_ml_strlen($val) <= 1024)
        {
            return true;
        }
        else
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_002'
                        ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_002'
                        );
        }
    }

    function is_valid_text_line($val)
    {
        if (_ml_strlen($val) <= 128)
        {
            return true;
        }
        else
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_002'
                        ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_002'
                        );
        }
    }

    function is_valid_credit_card_type($cc_type)
    {
        $list = modApiFunc("Configuration", "getCreditCardSettings");
        if(array_key_exists($cc_type, $list))
        {
            return true;
        }
        else
        {
            return array('error_code_full' => 'CCTYPE_ERR_002'
                        ,'error_code_short' => 'CCTYPE_ERR_002'
                        );
        }
    }

    function is_valid_debit_card_number($dc_number, $dc_rules)
    {
        for ($i = 0; $i < sizeof($dc_rules); $i++)
        {
            $rule = $dc_rules[$i];
            $rule_details = explode(",", $rule);

            $hi_prefix        = $rule_details[0];
            $lo_prefix        = $rule_details[1];
            $val_length       = intval($rule_details[2]);
            $issue_length     = $rule_details[3];
            $start_date_length = isset($rule_details[4])? $rule_details[4]:"";

            $card_prefix = _ml_substr($dc_number, 0, _ml_strlen($hi_prefix));
            if ($card_prefix >= $hi_prefix && $card_prefix <= $lo_prefix && _ml_strlen($dc_number) == $val_length)
            {
                return true;
            }
        }
        return false;
    }

    function is_valid_credit_card_number($cc_number, $cc_type)
    {
        $cc_number = trim($cc_number);
        switch($cc_type)
        {
            case "Visa":
                $valid = preg_match("/^4[0-9]{12}([0-9]{3})?$/", $cc_number);
                if (!$valid)
                {
                    return array('error_code_full' => 'CCNUM_ERR_002'
                                ,'error_code_short' => 'CCNUM_ERR_002');
                }
                break;
            case "MasterCard":
                $valid = preg_match("/^5[1-5][0-9]{14}$/", $cc_number);
                if (!$valid)
                {
                    return array('error_code_full' => 'CCNUM_ERR_003'
                                ,'error_code_short' => 'CCNUM_ERR_003');
                }
                break;
            case "Discover":
                $valid = preg_match("/^6011[0-9]{12}$/", $cc_number);
                if (!$valid)
                {
                    return array('error_code_full' => 'CCNUM_ERR_004'
                                ,'error_code_short' => 'CCNUM_ERR_004');
                }
                break;
            case "Amex":
                $valid = preg_match("/^3[47][0-9]{13}$/", $cc_number);
                if (!$valid)
                {
                    return array('error_code_full' => 'CCNUM_ERR_005'
                                ,'error_code_short' => 'CCNUM_ERR_005');
                }
                break;
            case "Maestro":
                $MaestroRules = array(
                "490302,490309,18,1","490335,490339,18,1","491101,491102,16,1","491174,491182,18,1","493600,493699,19,1","564182,564182,16,2","633300,633300,16,0","633301,633301,19,1","633302,633349,16,0","675900,675900,16,0","675901,675901,19,1","675902,675904,16,0","675905,675905,19,1","675906,675917,16,0","675918,675918,19,1","675919,675937,16,0","675938,675940,18,1","675941,675949,16,0","675950,675962,19,1","675963,675997,16,0","675998,675998,19,1","675999,675999,16,0"
                                     );
                $valid = $this->is_valid_debit_card_number($cc_number, $MaestroRules);
                if (!$valid)
                {
                    return array('error_code_full' => 'CCNUM_ERR_007'
                                ,'error_code_short' => 'CCNUM_ERR_007');
                }
                break;
            case "Solo":
                $SoloRules = array(
                "633450,633453,16,0","633454,633457,16,0","633458,633460,16,0","633461,633461,18,1","633462,633472,16,0","633473,633473,18,1","633474,633475,16,0","633476,633476,19,1","633477,633477,16,0","633478,633478,18,1","633479,633480,16,0","633481,633481,19,1","633482,633489,16,0","633490,633493,16,1","633494,633494,18,1","633495,633497,16,2","633498,633498,19,1","633499,633499,18,1","676700,676700,16,0","676701,676701,19,1","676702,676702,16,0","676703,676703,18,1","676704,676704,16,0","676705,676705,19,1","676706,676707,16,2","676708,676711,16,0","676712,676715,16,0","676716,676717,16,0","676718,676718,19,1","676719,676739,16,0","676740,676740,18,1","676741,676749,16,0","676750,676762,19,1","676763,676769,16,0","676770,676770,19,1","676771,676773,16,0","676774,676774,18,1","676775,676778,16,0","676779,676779,18,1","676780,676781,16,0","676782,676782,18,1","676783,676794,16,0","676795,676795,18,1","676796,676797,16,0","676798,676798,19,1","676799,676799,16,0"
                                     );
                $valid = $this->is_valid_debit_card_number($cc_number, $SoloRules);
                if (!$valid)
                {
                    return array('error_code_full' => 'CCNUM_ERR_008'
                                ,'error_code_short' => 'CCNUM_ERR_008');
                }
                break;
            default:
                {
                    $list = modApiFunc("Configuration", "getCreditCardSettings");
                    if(array_key_exists($cc_type, $list))
                    {
                        //                                    .
                        //                       .
                        return true;
                    }
                    else
                    {
                        return array('error_code_full' => 'CCTYPE_ERR_002'
                                    ,'error_code_short' => 'CCTYPE_ERR_002');
                    }
                }
        }
        $cc_number = _ml_strrev($cc_number);
        $numSum = 0;
        for($i = 0; $i < _ml_strlen($cc_number); $i++)
        {
            $currentNum = _ml_substr($cc_number, $i, 1);
            // Double every second digit
            if($i % 2 == 1)
            {
              $currentNum *= 2;
            }
            // Add digits of 2-digit numbers together
            if($currentNum > 9)
            {
              $firstNum = $currentNum % 10;
              $secondNum = ($currentNum - $firstNum) / 10;
              $currentNum = $firstNum + $secondNum;
            }
            $numSum += $currentNum;
        }
        if (!($numSum % 10 == 0))
        {
            return array('error_code_full' => 'CCNUM_ERR_006'
                        ,'error_code_short' => 'CCNUM_ERR_006');
        }
        else
        {
            return true;
        }
    }

    function is_valid_credit_card_verification_number($cc_cvv)
    {
        if(_ml_strlen($cc_cvv) <= 10)
        {
            $valid = preg_match("/^[0-9a-zA-Z]*$/", $cc_cvv);
            if (!$valid)
            {
                return array('error_code_full' => 'CVV2_ERR_001_FULL'
                            ,'error_code_short' => 'CVV2_ERR_002_SHORT');
            }
            else
            {
                return true;
            }
        }
        else
        {
            return array('error_code_full' => 'CVV2_ERR_001_FULL'
                        ,'error_code_short' => 'CVV2_ERR_002_SHORT');
        }
    }

    function is_valid_credit_card_issue_number($cc_issue_number)
    {
        return true;
    }

    function str2dec_int($string)
    {
       $y = ltrim($string, '0');
       $z = 0 + $y;
       return $z;
    }

    /**
     *                            .       Expiration Date
     *             .
     */
    function is_valid_month($month, $year)
    {
        $month  = $this->str2dec_int($month);
        if($month >0 &&
           $month <13)
        {
            $cur_year_4digits = date("Y");
            if($year > $cur_year_4digits)
            {
                return true;
            }
            else if($year == $cur_year_4digits)
            {
                $cur_month_1_2_digits = date("n");
                if($month >= $cur_month_1_2_digits)
                {
                    return true;
                }
                else
                {
                    return array('error_code_full' => 'CCEXPMON_ERR_003'
                                ,'error_code_short' => 'CCEXPMON_ERR_003');
                }
            }
            else
            {
                return array('error_code_full' => 'CCEXPMON_ERR_003'
                            ,'error_code_short' => 'CCEXPMON_ERR_003');
            }
        }
        else
        {
            return array('error_code_full' => 'CCEXPMON_ERR_003'
                        ,'error_code_short' => 'CCEXPMON_ERR_003');
        }
    }

    /**
     *                            .       'valid from' Date
     *             .
     */
    function is_valid_month_valid_from($month, $year)
    {
        $month  = $this->str2dec_int($month);
        if($month >0 &&
           $month <13)
        {
            $cur_year_4digits = date("Y");
            if($year < $cur_year_4digits)
            {
                return true;
            }
            else if($year == $cur_year_4digits)
            {
                $cur_month_1_2_digits = date("n");
                if($month <= $cur_month_1_2_digits)
                {
                    return true;
                }
                else
                {
                    return array('error_code_full' => 'CC_VALID_FROM_MON_ERR_003'
                                ,'error_code_short' => 'CC_VALID_FROM_MON_ERR_003');
                }
            }
            else
            {
                return array('error_code_full' => 'CC_VALID_FROM_MON_ERR_003'
                            ,'error_code_short' => 'CC_VALID_FROM_MON_ERR_003');
            }
        }
        else
        {
            return array('error_code_full' => 'CC_VALID_FROM_MON_ERR_003'
                        ,'error_code_short' => 'CC_VALID_FROM_MON_ERR_003');
        }
    }

    function is_valid_year($year)
    {
        $year  = $this->str2dec_int($year);
        if($year >0)
        {
            return true;
        }
        else
        {
            return array('error_code_full' => 'CCEXPYEAR_ERR_002'
                        ,'error_code_short' => 'CCEXPYEAR_ERR_002');
        }
    }

    function is_valid_year_valid_from($year)
    {
        $year  = $this->str2dec_int($year);
        $cur_year_4digits = date("Y");
        if($year <= $cur_year_4digits)
        {
            return true;
        }
        else
        {
            return array('error_code_full' => 'CC_VALID_FROM_YEAR_ERR_002'
                        ,'error_code_short' => 'CC_VALID_FROM_YEAR_ERR_002');
        }
    }


    function is_valid_first_name($val)
    {
        //If the value is an empty string, ask to input again.
        if(empty($val))
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_001'
                        ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_001');
        }
        else
        {
            return true;
        }
    }

    function is_valid_last_name($val)
    {
        //If the value is an empty string, ask to input again.
        if(empty($val))
        {
            return array('error_code_full' => 'CHECKOUT_ERR_FORM_FIELD_FULL_001'
                        ,'error_code_short' => 'CHECKOUT_ERR_FORM_FIELD_SHORT_001');
        }
        else
        {
            return true;
        }
    }

    function is_valid_bank_account_name($val)
    {
        return true;
    }

    function is_valid_bank_routing_number($val)
    {
        return true;
    }

    function is_valid_bank_account_number($val)
    {
        return true;
    }

    function is_valid_bank_state_branch($val)
    {
        return true;
    }

    function is_valid_bank_international_bank_account_number($val)
    {
        return true;
    }

    /**
     *                                      "              "
     *             .
     *
     * @param unknown_type $val
     * @return unknown
     */
    function is_valid_no_validation($val)
    {
        return true;
    }

    /** Input validation */
    /*
     * $post_data -                      .
     *                 .                              ,
     *                                                   Checkout,
     *         "    "                    -                     , required/visible, ...
     * */
    function validateInputForPrerequisite($prerequisiteName, $post_data = CHECKOUT_POST_DATA_NOT_EMULATED)
    {
        global $application;

        $validatedData = $this->getValidatedDataStructure($prerequisiteName);

        //simple stub
        if($post_data === CHECKOUT_POST_DATA_NOT_EMULATED)
        {
            $request = $application->getInstance('Request');
            $post_data = $request->getValueByKey($prerequisiteName);
        }
        $credit_card_type = '';
        $isMet = true;
        $error_code = "";
        $error_message_parameters = array();

        if ($prerequisiteName == 'subscriptionTopics') {
            $post_data['Topics'] = is_array($post_data['Topics']) ? implode(',', $post_data['Topics']) : '';
        }

        if(NULL === $post_data)
        {
            /* As we get into this function, then the Block-tag, for example, Billing
               Info, was however outputted. If no data array goes with it,
               then probably, all its marked as "invisible".
             */
            switch($prerequisiteName)
            /* prerequisites "shippingModuleAndMethod" and "paymentModule" are not
               adjusted through AZ and processed differently.
              They have the customized error messages.
             */
            {
                case "shippingModuleAndMethod":
                {
                    break;
                }
                case "paymentModule":
                {
                    break;
                }
                case "subscriptionTopics":
                {
                    break;
                }
                default:
                {
                    $post_data = array();
                }
            }
        }
        if(NULL !== $post_data)
        {
            //Sort the prerequisite fields: those, that require preliminary check
            //should be put to the end. E.g.: the state (requires a country),
            //the number of the credit card (requires the type of the credit card).
            $validatedData_copy_with_proper_sort_order = $validatedData;

            asc_assoc_array_move_back($validatedData, "Statemenu");
            asc_assoc_array_move_back($validatedData, "Statetext");

            // move all CreditCardInfo attrs to the bottom
            // to ensure that CreditCardType is above all other attrs
            asc_assoc_array_move_back($validatedData, "CreditCardType");
            asc_assoc_array_move_back($validatedData, "CreditCardNumber");
            asc_assoc_array_move_back($validatedData, "CreditCardVerificationNumber");
            asc_assoc_array_move_back($validatedData, "ExpirationYear");
            asc_assoc_array_move_back($validatedData, "ValidFromYear");
            asc_assoc_array_move_back($validatedData, "CreditCardIssueNumber");
            asc_assoc_array_move_back($validatedData, "ExpirationMonth");
            asc_assoc_array_move_back($validatedData, "ValidFromMonth");

            if (preg_match('/^creditCardInfo/', $prerequisiteName))
            {
                $cc_types = modApiFunc("Configuration", "getCreditCardSettings", true);
            }

            //Get a list of all fields, which make this prerequisite:
            foreach($validatedData as $validatedDataItemKey => $validatedDataItemVal)
            {
                if (preg_match('/^creditCardInfo/', $prerequisiteName))
                {
                    if ($credit_card_type != '' && isset($cc_types[$credit_card_type]['id']))
                    {
                        $cc_attrs = modApiFunc("Configuration", "getAttributesForCardType", $cc_types[$credit_card_type]['id']);
                        $attr_id = $validatedDataItemVal['id'];
                        $vis = $cc_attrs[$attr_id]['visible'];
                        $req = $cc_attrs[$attr_id]['required'];
                        $validatedDataItemVal['attribute_required'] = ($vis && $req) ? '1' : '0';
                    }
                }

                if(!method_exists($this, $validatedDataItemVal["input_validation_func_name"]))
                {
                    //The definition of validation function doesn't exist. It helps to check if
                    // an inputted value is correct.
                    $isMet = false;
                    $validatedData[$validatedDataItemKey]['error_code_full'] = 'CHECKOUT_ERR_002';
                    $validatedData[$validatedDataItemKey]['error_code_short'] = 'CHECKOUT_ERR_002';
                    $validatedData[$validatedDataItemKey]['error_message_parameters'] = array($validatedDataItemVal["input_validation_func_name"], $validatedDataItemKey);
                }
                else
                {
                    //Check if data was passed by POST:
                    if(!isset($post_data[$validatedDataItemKey]))
                    {
                        if(//: work out a variant, when the fields are
                           // inserted to POST using JavaScript
                            $validatedDataItemKey != 'Statemenu' &&
                            $validatedDataItemKey != 'Statetext' &&
                            $validatedDataItemVal['attribute_required'] == true
                          )
                        {
                            //: process separartely a checkbox group which is not ticked off.
                            //Example: if admistrator can choose a list of
                            //  inputted fields for filling them and he chooses
                            //  only one field and it will be CHECKBOX list
                            //  and a buyer won't choose any of the CHECKBOX.
                            //  Then nothing will passed to the POST.

                            //Data wasn't passed (such index doesn't exist in _POST).
                            // Sush error may occur, when for example, the administrator added
                            //a field to person info, and forgot to update a customized template.

                            //It is a required attribute
                            // OR the shop is NOT Online
                            // and it's better to send an error message.
                            // Validation failed.Save the error.
                            $isMet = false;
                            $validatedData[$validatedDataItemKey]['error_code_full'] = 'CHECKOUT_ERR_003';
                            $validatedData[$validatedDataItemKey]['error_code_short'] = 'CHECKOUT_ERR_003';
                            $validatedData[$validatedDataItemKey]['error_message_parameters'] = array($this->PrerequisitesValidationResults[$prerequisiteName]["validatedData"][$validatedDataItemKey]["attribute_visible_name"], $this->PrerequisitesValidationResults[$prerequisiteName]["visibleName"]);
                            continue;
                        }
                        else
                        {
                            //: process separartely a checkbox group which is not ticked off.

                            //Data wasn't passed (such index doesn't exist in _POST).
                            // The attribute is not required
                            // OR the shop is NOT Online
                            // and an error message should be sent.

                            //Assign a value "empty string" to the attribute
                            // and don't send an error message on this step.

                            //Pretend as if the attribute value in the request was: "empty string".
                            $post_data[$validatedDataItemKey] = "";
                        }
                    }

                    //On this step either a value has been assigned to the attribute artificially (empty
                    // string), or it has existed in the POST data. Go on to validate.

                    //Data was passed through POST.
                    //Perhaps it's true, that the passed value is an empty string,
                    //  i.e. a user, for example, didn't fill out an input field.

                    if(!($validatedDataItemKey == 'Statemenu' ||
                         $validatedDataItemKey == 'Statetext') &&
                       empty($post_data[$validatedDataItemKey])
//: What is the condition input_validation_func_name == false for?
//$validatedDataItemVal["input_validation_func_name"] == false
                      )
                    {
                        if((bool)$validatedDataItemVal['attribute_required'] == true)
                        {
//+ (a branch has been tested)
                            //If an attribute is required and
                            //    1) http data (_POST) didn't come for it
                            //    2) or the arrived value is an empty string
                            // (discussed with af on 02(feb)-01-2006)
                            // - output an error before validation fumction is called. Like
                            // "required attribute"
                            $isMet = false;
                            $validatedData[$validatedDataItemKey]['error_code_full'] = 'CHECKOUT_ERR_FORM_FIELD_FULL_001';
                            $validatedData[$validatedDataItemKey]['error_code_short'] = 'CHECKOUT_ERR_FORM_FIELD_SHORT_001';
                            $validatedData[$validatedDataItemKey]['error_message_parameters'] = array($this->PrerequisitesValidationResults[$prerequisiteName]["validatedData"][$validatedDataItemKey]["attribute_visible_name"], $this->PrerequisitesValidationResults[$prerequisiteName]["visibleName"]);
                            continue;
                        }
                        else
                        {
                            //If an attribute is not required and
                            //     1) http data (_POST) didn't come for it
                            //     2) or the arrived value is an empty string
                            // - Validation is successful. Don't call the validation function.
                            $r = true;
                        }
                    }
                    else
                    {
                        // Data was passed through the POST.
                        //The value is not an empty string (the states are separately)
                        // if it is called a validation function.

                        //Validate the inputted data.
                        //There are special cases, when not only inputted by user
                        // attribute values but optional parameters are passed, listed below:
                        // 1) a state (is selected from menu),
                        // 2) a state ( the text is inputted manually for countries, that don't
                        //    have any state )
                        // in both cases the country id should be passed.
                        //
                        // :         :
                        //                    .          ,              id
                        //                    prerequisite,           .
                        // : a problem: the country should already be checked before the state checking.
                        //     Both the country id and the state are is in the same prerequisite block.
                        switch($validatedDataItemKey)
                        {
                            case "Statemenu":
                            case "Statetext":
                            {
                                $country_id = $validatedData['Country']['value'];
                                $r = call_user_func(array(&$this, $validatedDataItemVal["input_validation_func_name"]), $post_data[$validatedDataItemKey], $country_id);
                                break;
                            }
                            case "CreditCardNumber":
                            {
                                $credit_card_type = $validatedData['CreditCardType']['value'];
                                $r = call_user_func(array(&$this, $validatedDataItemVal["input_validation_func_name"]), $post_data[$validatedDataItemKey], $credit_card_type);
                                break;
                            }
                            case "ExpirationMonth":
                            {
                                $year = $validatedData['ExpirationYear']['value'];
                                $r = call_user_func(array(&$this, $validatedDataItemVal["input_validation_func_name"]), $post_data[$validatedDataItemKey], $year);
                                break;
                            }
                            case "ValidFromMonth":
                            {
                                $year = $validatedData['ValidFromYear']['value'];
                                $r = call_user_func(array(&$this, $validatedDataItemVal["input_validation_func_name"]), $post_data[$validatedDataItemKey], $year);
                                break;
                            }
                            default:
                            {
                                $r = call_user_func(array(&$this, $validatedDataItemVal["input_validation_func_name"]), $post_data[$validatedDataItemKey]);
                                break;
                            }
                        }
                    }

                    if(!is_array($r) && ($r == true))
                    {
                        //data are validated successfully.
                        //save the checked value.
                        $validatedData[$validatedDataItemKey]['value'] = $post_data[$validatedDataItemKey];
                        if ($validatedDataItemKey == 'CreditCardType')
                        {
                            $credit_card_type = $validatedData[$validatedDataItemKey]['value'];
                        }
                    }
                    else
                    {
                        //data are not validated.
                        $validatedData[$validatedDataItemKey]['value'] = $post_data[$validatedDataItemKey];
                        $isMet = false;
                        if(is_array($r))
                        {
//       --- (a branch is not tested)
                            if(!empty($r['error_code_full']) || !empty($r['error_code_short']))
                            {
//        --- (a branch is not tested)
                                $validatedData[$validatedDataItemKey]['error_code_full'] = $r['error_code_full'];
                                $validatedData[$validatedDataItemKey]['error_code_short'] = $r['error_code_short'];
                                if(!empty($r['error_message_parameters']))
                                {
//         --- (a branch is not tested)
                                    $validatedData[$validatedDataItemKey]['error_message_parameters'] = $r['error_message_parameters'];
                                }

                                //There is a special handling of some error types. Add error
                                //  parameters, which can't be defined into the called
                                //  checking function. For example, the name of the current Person Info.
                                switch($validatedData[$validatedDataItemKey]['error_code_full'])
                                {
                                    case "CHECKOUT_ERR_FORM_FIELD_FULL_001":
                                    case "CHECKOUT_ERR_FORM_FIELD_FULL_002":
                                    case "CHECKOUT_ERR_DROPDOWN_FULL_002":
                                    case "CHECKOUT_ERR_DROPDOWN_FULL_001":
                                    case "CHECKOUT_ERR_FORM_FIELD_FULL_001":
                                    {
                                        //WARNING: The old parameter values are not saved!
                                        $form_name = $this->PrerequisitesValidationResults[$prerequisiteName]["visibleName"];
                                        switch($validatedDataItemKey)
                                        {
                                            case "Statetext":
                                            {
                                                $fakeDataItemKey = "Statemenu";
                                                $attribute_name = $this->PrerequisitesValidationResults[$prerequisiteName]["validatedData"][$fakeDataItemKey]["attribute_visible_name"];
                                                break;
                                            }
                                            default:
                                            {
                                                $attribute_name = $this->PrerequisitesValidationResults[$prerequisiteName]["validatedData"][$validatedDataItemKey]["attribute_visible_name"];
                                                break;
                                            }
                                        }

                                        $validatedData[$validatedDataItemKey]['error_message_parameters'] = array($attribute_name, $form_name);
                                        break;
                                    }
                                    default:
                                    {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //Restore the order of the items.
            foreach($validatedData as $validatedDataItemKey => $validatedDataItemVal)
            {
                $validatedData_copy_with_proper_sort_order[$validatedDataItemKey] = $validatedDataItemVal;
            }
            $validatedData = $validatedData_copy_with_proper_sort_order;
            //END restore the order of the items.
        }
        else
        {
//          --- (a branch is tested)
            $isMet = false;

            switch($prerequisiteName)
            //customize the error message
            {
                case "shippingModuleAndMethod":
                {
                    if($this->isShippingModulesListEmpty() == false)
                    {
                        $error_code = 'CHECKOUT_ERR_004_SHIPPING_MODULE_AND_METHOD_AVAILABLE_BUT_NONE_SELECTED';
                    }
                    else
                    {
                        $error_code = 'CHECKOUT_ERR_004_SHIPPING_MODULE_AND_METHOD_NOT_AVAILABLE_AND_NOT_SELECTED';
                    }
                    break;
                }
                case "paymentModule":
                {
                    if($this->isPaymentModulesListEmpty() == false)
                    {
                        $error_code = 'CHECKOUT_ERR_004_PAYMENT_MODULE_AVAILABLE_BUT_NONE_SELECTED';
                    }
                    else
                    {
                        $error_code = 'CHECKOUT_ERR_004_PAYMENT_MODULE_NOT_AVAILABLE_AND_NOT_SELECTED';
                    }
                    break;
                }
                default:
                {
                    $error_code = 'CHECKOUT_ERR_004_NO_POST_DATA_FOR_PREREQUISITE';
                    break;
                }
            }
            $error_message_parameters = array($this->PrerequisitesValidationResults[$prerequisiteName]["visibleName"], $prerequisiteName);

//            $this->PrerequisitesValidationResults[$prerequisiteName]["isMet"] = false;
//            $this->PrerequisitesValidationResults[$prerequisiteName]["error_code"] = 'CHECKOUT_ERR_004';
//            $this->PrerequisitesValidationResults[$prerequisiteName]["error_message_parameters"] = array($this->PrerequisitesValidationResults[$prerequisiteName]["visibleName"], $prerequisiteName);

//            $isMet = false;
///            $err_params = array(
///                                "CODE"    => "CHECKOUT_ERR_004"
///                               );
///            _fatal($err_params);
        }

/*           --- (a branch is not tested).
            The branch works when no shipping methods matching
            the shipping address exist. */
        $variant_tag = $this->PrerequisitesValidationResults[$prerequisiteName]['variant_tag'];
        //$variant_id = $this->PrerequisitesValidationResults[$prerequisiteName]['variant_id'];
        if( empty($variant_tag) &&
            (($prerequisiteName != "shippingModuleAndMethod") && ($prerequisiteName != "paymentModule") && $prerequisiteName != 'subscriptionTopics'))
        {/* a fast check, with specified names */
            /*fatal error. The data are initialized in the constructor and had
             to be filled already */
            _fatal(__FILE__ . " : " . __LINE__  . " : " . $prerequisiteName);
        }
        else
        {
            $this->setPrerequisitesValidationResultsItem($prerequisiteName, $variant_tag, $isMet, $error_code, $error_message_parameters, $validatedData);
        }
        //return false;
    }

    function areValidShippingModuleAndMethodIDs($module_id, $method_id)
    {
        if($module_id == $this->getNotNeedShippingModuleID() and $method_id == $this->getNotNeedShippingMethodID())
            return true;

        $pm_list = Checkout::getInstalledAndActiveModulesListData("shipping");

        $items = array();
        $new_selected_module_sort_order = 0;
        foreach ($pm_list as $pm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($pm_item->name);

            $pmInfo = modApiFunc($name, "getInfo");

            if($pmInfo['GlobalUniqueShippingModuleID'] != $module_id)
            {
                continue;
            }
            else
            {
                return modApiFunc($pm_item->name, "isValidShippingMethodId", $method_id);
            }
        }
        return false;
    }

    function isValidPaymentModuleID($module_id)
    {
        $pm_list = Checkout::getInstalledAndActiveModulesListData("payment");

        $items = array();
        $new_selected_module_sort_order = 0;
        foreach ($pm_list as $pm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($pm_item->name);

            $pmInfo = modApiFunc($name, "getInfo");

            if($pmInfo['GlobalUniquePaymentModuleID'] != $module_id)
            {
                continue;
            }
            else
            {
                return true;
            }
        }
        return false;
    }

    function getPrerequisiteValidationResults($prerequisite_name)
    {
        // check if $prerequisite_name is acceptable.
        // check the realization of the arrays in af.
        if(isset($this->PrerequisitesValidationResults[$prerequisite_name]))
        {
            return $this->PrerequisitesValidationResults[$prerequisite_name];
        }
        else
        {
            return false;
        }
    }

    function getPrerequisiteStoreBlock($prerequisite_name)
    {
        // check if $prerequisite_name is acceptable.
        return $this->PrerequisiteToStoreBlockTable[$prerequisite_name];
    }

    function getPrerequisiteValidationFunction($prerequisite_name)
    {
        // check if $prerequisite_name is acceptable.
        // Second question PrerequisiteValidation or maybe StoreBlockValidation ?
        return $this->PrerequisitesValidationFunctionsTable[$prerequisite_name];
    }

    function getPrerequisiteErrorToStepIDTable()
    {
        //Read out the table.
        // The step checkout => prerequisite1, prerequisite2, prerequisite3 ...
        // Then, note, that each prerequisite appears at some step and is required
        // at the next steps.
        // To define at which step to redirect the customer, we should see all
        // prerequisites, and find for each of them that initial step, where they
        // become discontent. Select a min value from all the found prerequisites.
        // The last step of the algorythm (the selection of the value from all discontent ones)
        // is not included in the function getPrerequisiteToStepIDTable().

        // : it's a little bit inconsequent. When checking errors, it is considered that,
        // if some prerequisite was required at step i, then it must be required at the next steps too.
        // But in the ini file in the table Option-prerequisite can be written a configuration that
        // doesn't correspond this idea. What should be changed in this case: the error handling
        //  or the flexibility of the table prerequisites should be reduced
        // (e.g. legalize the idea described above) is not clear.

        $prerequisites_table = $this->getPrerequisitesINI();
        // Select all available prerequisites from the list of lists. Then for each of them find the first step in the
        // checkout where this prerequisite occurs.
        $prerequisites_list = array();
        foreach($prerequisites_table as $step_id => $prerequisites_list_for_some_step)
        {
            foreach($prerequisites_list_for_some_step as $prerequisite)
            {
                if(!array_key_exists($prerequisite, $prerequisites_list))
                {
                    $prerequisites_list[$prerequisite] = $step_id - 1; //NOTE "-1" !
                    //Under assumption that if prerequisite is already required at step i,
                    //  then it should be entered at step i-1 or earlier.
                }
            }
        }

        return $prerequisites_list;
    }

    /**#@-*/

    /* remove this function after replacing it with new logic. It is not consistent
    with dynamic & flexible checkout cz nature */
    function isLastStepWithPrerequisites($step_id)
    {
        //
        //Simple search stub
        //WARNING: prerequisites may come not in alphanumeric order in .ini
        //  e.g.
        //  Option-prerequisite2 = ...
        //  Option-prerequisite1 = ...
        //  Option-prerequisite3 = ...
        $prerequisitesTable = $this->getPrerequisitesINI();
        for($i=1;;$i++)
        {
            if(!array_key_exists($i, $prerequisitesTable))
            {
                //Suppose, $i is the last step with prerequisites
                return ($i-1 == $step_id);
            }
        }
    }

    /**
     * Is the outputted view is CheckoutConfirmation?
     * It is used to replace the address in the form, for example, to send
     * data to the payment gateway.
     */
    function isCheckoutConfirmationStep($step_id)
    {
        //
        //Simple stub
        //Suppose for the time being, that "the last step (with the max number in the config file)" =>
        // "CheckoutConfirmation"
        //The one genuine checking is to find in the template a block of
        // "CheckoutConfirmation" entries.
        //As a variant, always add checkout to the last step, which is
        // CheckoutConfirmation and remove it from the configuration file.
        return $this->isLastStepWithPrerequisites($step_id);
    }

    function getPaymentModuleShippingSettings($module_id)
    {
        global $application;

        if($module_id === NULL)
        {
        	return array("PerOrderPaymentModuleShippingFee" => PRICE_N_A);
        }
        else
        {
	        $mInfo = Checkout::getPaymentModuleInfo($module_id);
	        if($mInfo === NULL)
	        {
	            return array("PerOrderPaymentModuleShippingFee" => PRICE_N_A);
	        }
	        else
	        {
	            if(is_callable(array($mInfo["APIClassName"],"getPerOrderPaymentModuleShippingFee")))
	            {
	                return array("PerOrderPaymentModuleShippingFee" => call_user_func(array($mInfo["APIClassName"], 'getPerOrderPaymentModuleShippingFee')));
	            }
	            else
	            {
	                return array("PerOrderPaymentModuleShippingFee" => PRICE_N_A);
	            }
	        }
        }
    }

    function getCacheObj()
    {
        return CCacheFactory::getCache('checkout');
    }

    function clearCacheObj()
    {
        $this->getCacheObj()->erase();
    }

    /**
     * Outputs the price of the defined products, services (shipping, payment).
     * It is used to specify the names of different costs: Subtotal,
     * Total and others. It is important, when for example the shipping cost
     * depends on payment method (example: the shipping cost of some services
     * is higher if a payment method is cash on delivery). That's why it's
     * important what goes first the payment method description or the shipping
     * method.
     *
     * If the required price type is not defined, output null.
     *
     * @param $shipping_method_cost - calculate per item of
     * and total shipping cost, in case the cost of the selected method
     * is known and is shipping_method_cost. It might be used to output
     * shipping cocts on the page of shipping method selection.
     *
     * It is called by the shipping modules.
     */
    function getOrderPrice($type, $currency_id, $shipping_method_cost = NULL)
    {
        $__cache__ = $this->getCacheObj();
        $__cache_key__ = md5( serialize($type) .
                              serialize($currency_id) .
                              serialize($shipping_method_cost) .
                              serialize($this->lastPlacedOrderID) .
                              serialize($this->getChosenShippingMethodIdCZ())
                         );

        $__cached_value__ = $__cache__->read($__cache_key__);
        if ($__cached_value__ !== null)
        {
            return $__cached_value__;
        }

        //_print(debug_backtrace());
    	$main_store_currency = modApiFunc("Localization", "getMainStoreCurrency");
        $value = null;

       /*
        What for is the order info used here?

        All order information is in the session and should be determined,
        untill the order is created in the database.

        Once the order is created, nothing exists in the session, all order info
        can be taken from the database.

        Here you can get info about the last order (the last order in
        this session, if a customer will start the checkout again,
        then lastPlacedOrderID is zeroed).

        Warning: to make all parameters of the price ($type) available
        after creating the order, it's necessary to save them correctly to the
        database in the method createOrderInDB and to load them correctly from
        the database in the method getOrderInfo.
        */
        if(!empty($this->lastPlacedOrderID))
        {
            $orderInfo = $this->getOrderInfo($this->lastPlacedOrderID, $currency_id);
        }
        else
        {
            $orderInfo = array();
        }

       /* Initialize the array $_shipping_cost_array. It should be done before
           switch, as this code is used in almost all cases.
        */
        if($this->getChosenShippingModuleIdCZ() === NULL)
        {
            //Shipping Module (shipping method) is not selected yet.
            $_shipping_cost_array = null;
        }
        else
        {
            $shipping_method_id = $this->getChosenShippingMethodIdCZ();
            $shipping_module_info = Checkout::getShippingModuleInfo($this->getChosenShippingModuleIdCZ());

            $formatted_cart = modApiFunc("Shipping_Cost_Calculator","formatCart",modApiFunc("Cart","getCartContentExt"));
            modApiFunc("Shipping_Cost_Calculator","setShippingInfo",modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo"));
            $payment_module_info = Checkout::getPaymentModuleShippingSettings($this->getChosenPaymentModuleIdCZ());
            modApiFunc("Shipping_Cost_Calculator","setPaymentModuleInfo",$payment_module_info);
            modApiFunc("Shipping_Cost_Calculator","setCart",$formatted_cart);
            $_shipping_cost_array = modApiFunc("Shipping_Cost_Calculator","getCalculatedMethod",$shipping_module_info["APIClassName"],$shipping_method_id);
        }

        switch($type)
        {
            case "Subtotal":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Subtotal"];
                }
                else
                {
                    /*      SalePriceExcludingTaxes               .
                               SalePrice,                ,
                       Included                  Default Address,               ,
                                             ,                                       .

                                     Checkout             SalePriceExcludingTaxes.
                                                              .
                                             .              (                     ,
                                               ,               )                (
                                         ,                             ,
                                 ,                   ).
                                       Subtotal.                                               .
                                       Shipping.
                                       Total.   Subtotal'                                  ,
                                                        Shipping.
                    */

                    $value = modApiFunc("Cart", "getCartSubtotalExcludingTaxes");

                    $display_totals_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");

                    if($display_totals_including_taxes == DB_TRUE)
                    {
                        $taxes = modApiFunc("Taxes", "getTax", true);
                        switch(modApiFunc("TaxExempts", "getFullTaxExemptStatus"))
                        {
                            case DB_TRUE:
                                break;
                            default:
                                $value = modApiFunc("Cart", "getCartSubtotal");
                                break;
                        }
                    }
                }
                break;
            }
            case "SubtotalExcludingIncludedTax":
            {
                $included_tax = $this->getOrderPrice("IncludedTax", $main_store_currency);
                $included_tax = ($included_tax == PRICE_N_A) ? 0.0 : $included_tax;
                $value = $this->getOrderPrice("Subtotal", $main_store_currency) - $included_tax;
                if ($value < 0.00)
                    $value = 0.00;
                break;
            }
            case "DiscountedSubtotal":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["DiscountedSubtotal"];
                }
                else
                {
                    $order_subtotal = $this->getOrderPrice("Subtotal", $main_store_currency);
                    $o_subtotal = $order_subtotal == PRICE_N_A ? 0 : $order_subtotal;

                    $subtotal_global_discount = $this->getOrderPrice("SubtotalGlobalDiscount", $main_store_currency);
                    $o_subtotal_global_discount = $subtotal_global_discount == PRICE_N_A ? 0 : $subtotal_global_discount;

                    $subtotal_promo_code_discount = $this->getOrderPrice("SubtotalPromoCodeDiscount", $main_store_currency);
                    $o_subtotal_promo_code_discount = $subtotal_promo_code_discount == PRICE_N_A ? 0 : $subtotal_promo_code_discount;

                    $quantity_discount = $this->getOrderPrice("QuantityDiscount", $main_store_currency);
                    $o_quantity_discount = $quantity_discount == PRICE_N_A ? 0 : $quantity_discount;

                    $value = $o_subtotal -
                             $o_subtotal_global_discount -
                             $o_subtotal_promo_code_discount -
                             $o_quantity_discount;

                    if($value < 0.0)
                    {
                        $value = 0.0;
                    }
                }
                break;
            }
            case "DiscountsSum":
            {
                //                                                  .                                     .
                //                                           :                         Subtotal,
                //                                     .
                $subtotal_global_discount = $this->getOrderPrice("SubtotalGlobalDiscount", $main_store_currency);
                $o_subtotal_global_discount = $subtotal_global_discount == PRICE_N_A ? 0 : $subtotal_global_discount;

                $subtotal_promo_code_discount = $this->getOrderPrice("SubtotalPromoCodeDiscount", $main_store_currency);
                $o_subtotal_promo_code_discount = $subtotal_promo_code_discount == PRICE_N_A ? 0 : $subtotal_promo_code_discount;

                $quantity_discount = $this->getOrderPrice("QuantityDiscount", $main_store_currency);
                $o_quantity_discount = $quantity_discount == PRICE_N_A ? 0 : $quantity_discount;

                $value = $o_subtotal_global_discount +
                         $o_subtotal_promo_code_discount +
                         $o_quantity_discount;

                if($value < 0.0)
                {
                    $value = 0.0;
                }
                break;
            }
            case "SubtotalGlobalDiscount":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["SubtotalGlobalDiscount"];
                }
                else
                {
                	//           -                   ,
                	// global_discount,                .
                    if(modApiFunc("PromoCodes", "isPromoCodeIdSet") === true)
                	{
                	    $promo_code_id = modApiFunc("PromoCodes", "getPromoCodeId");
                		$pc_info = modApiFunc("PromoCodes", "getPromoCodeInfo", $promo_code_id);
                        if($pc_info !== false)
                        {
                    		if($pc_info['b_ignore_other_discounts'] == 1 /* YES */)
                    		{
                    			$value = 0.0;
                    			break;
                    		}
                        }
                	}

                    #$order_subtotal = $this->getOrderPrice("SubtotalExcludingIncludedTax", $main_store_currency);
                    $order_subtotal = $this->getOrderPrice("Subtotal", $main_store_currency);
                    $o_subtotal = $order_subtotal == PRICE_N_A ? 0.0 : $order_subtotal;
                    $value = modApiFunc("Discounts", "getGlobalDiscount", $o_subtotal);
                    //     PRICE_N_A,
                    //           .
                    $value = $value == PRICE_N_A ? 0.0 : $value;
                }
                break;
            }
            case "QuantityDiscount":
            {
                $membership = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
                //           Quantity Discount                                                               ?
                $qtydsc_discard_options = modApiFunc('Settings', 'getParamValue', 'QUANTITY_DISCOUNT', "QUANTITY_DISCOUNT_BEHAVIOR");
                if ($qtydsc_discard_options == "NO")
                {
                    if(!empty($orderInfo))
                    {
                        $value = $orderInfo["Price"]["QuantityDiscount"];
                    }
                    else
                    {
                        $value = PRICE_N_A;
                        $products = modApiFunc("Cart", "getCartContentExt");
                        foreach($products as $info)
                        {
                            #$price = (isset($info['CartItemSalePriceExcludingTaxes'])) ? $info['CartItemSalePriceExcludingTaxes'] : $info['CartItemSalePrice'];
                            #$discount = modApiFunc("Quantity_Discounts", "getQuantityDiscount", $info["ID"], $info["Quantity_In_Cart"], $price, $membership);
                            $discount = modApiFunc("Quantity_Discounts", "getQuantityDiscount", $info["ID"], $info["Quantity_In_Cart"], $info['CartItemSalePrice'], $membership);
                            if($discount === 'FIXED_PRICE')
                            {
                                $discount = 0.00;
                            }
                            if($discount !== PRICE_N_A)
                            {
                                if($value === PRICE_N_A)
                                {
                                    $value = 0.00;
                                }
                                $value += $discount;
                            }
                        }

                        $value = ($value == PRICE_N_A) ? 0.0 : $value;
                        //                             (              ,      "          "       ,
                        //                 ),                       0.00;
                    }
                }
                else // if ($$qtydsc_discard_options == "YES")
                {
                    if(!empty($orderInfo))
                    {
                        $value = $orderInfo["Price"]["QuantityDiscount"];
                    }
                    else
                    {
                        $value = PRICE_N_A;
                        $products = modApiFunc("Cart", "getCartContentExt");
                        $_base_products = array();

                        foreach($products as $info)
                        {
                            if (isset($_base_products[$info["ID"]]))
                            {
                                $_base_products[$info["ID"]]["Quantity_In_Cart"] += $info["Quantity_In_Cart"];
                                $price = (isset($info["CartItemSalePriceExcludingTaxes"])) ? $info["CartItemSalePriceExcludingTaxes"] : $info["SalePrice"];
                                $_base_products[$info["ID"]]["OverallSum"] += $price*$info["Quantity_In_Cart"];
                            }
                            else
                            {
                                #12.01 $_base_products[$info["ID"]] = array ("Quantity_In_Cart" => $info["Quantity_In_Cart"], "SalePrice" => $info['CartItemSalePrice']);
                                $price = (isset($info["CartItemSalePriceExcludingTaxes"])) ? $info["CartItemSalePriceExcludingTaxes"] : $info["SalePrice"];
                                $_base_products[$info["ID"]] = array ("Quantity_In_Cart" => $info["Quantity_In_Cart"], "SalePrice" => $price, "OverallSum" => $price*$info["Quantity_In_Cart"]);
                            }
                        }

                        /*
                         * [1] => (array, 3) >>>
                         *      [Quantity_In_Cart] => (integer) 2
                         *      [SalePrice] => (float) 11
                         *      [OverallSum] => (float) 21
                         *
                         * SalePrice is the price of the first piece of product
                         * OverallSum is the overall group sum
                         */
                        foreach($_base_products as $prod_id => $info)
                        {
                            #12.01 $discount = modApiFunc("Quantity_Discounts", "getQuantityDiscount", $prod_id, $info["Quantity_In_Cart"], $info["SalePrice"]);
                            $discount = modApiFunc("Quantity_Discounts", "getQuantityDiscount", $prod_id, $info["Quantity_In_Cart"], $info["OverallSum"], $membership, true, QD_OVERALL_SUM);
                            if($discount === 'FIXED_PRICE')
                            {
                                $price = modApiFunc('Quantity_Discounts','getFixedPrice',$prod_id,$info["Quantity_In_Cart"],$info["SalePrice"],$membership);
                            }
                            elseif($discount !== PRICE_N_A)
                            {
                                if($value === PRICE_N_A)
                                {
                                    $value = 0.00;
                                }
                                $value += $discount;
                            }
                        }

                        $value = ($value == PRICE_N_A) ? 0.0 : $value;
                    }
                }
                break;
            }
            case "SubtotalPromoCodeDiscount":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["SubtotalPromoCodeDiscount"];
                }
                else
                {
                	$promo_code_id = modApiFunc("PromoCodes", "getPromoCodeId");
                    if(modApiFunc("PromoCodes", "isPromoCodeIdSet") === false)
                	//if($promo_code_id === NULL)
                	{
                	    $value = 0.0;
                	}
                	else
                	{
                	    // get product IDs and their categories' IDs
                	    $prod = array();
                	    $order_cart = modApiFunc("Cart","getCartContentExt");
                	    foreach ($order_cart as $product) {
                	    	$coupon_cart[] = array(
                	    	  'id' => $product["ID"],
                	    	  'cat' => $product['CategoryID'],
                	    	  'total' => $product['TotalExcludingTaxes']
                	    	);
                	    }

    	                $order_subtotal = $this->getOrderPrice("SubtotalExcludingIncludedTax", $main_store_currency);
    	                $o_subtotal = $order_subtotal == PRICE_N_A ? 0.0 : $order_subtotal;

    	                $value = modApiFunc("PromoCodes", "getPromoCodeDiscount", $o_subtotal, $promo_code_id, $coupon_cart);
    	                //     PRICE_N_A,
    	                //           .
    	                $value = $value == PRICE_N_A ? 0.0 : $value;
                	}
                }
                break;
            }
            case "ShippingMethodCost":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["ShippingMethodCost"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["ShippingMethodCost"];
                }
                break;
            }
            case "PerOrderPaymentModuleShippingFee":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["PerOrderPaymentModuleShippingFee"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["PerOrderPaymentModuleShippingFee"];
                }
                break;
            }
            case "PerItemShippingCostSum":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["PerItemShippingCostSum"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["PerItemShippingCostSum"];
                }
                break;
            }
            case "TotalShippingAndHandlingCost":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["TotalShippingAndHandlingCost"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["TotalShippingAndHandlingCost"];
                }
                break;
            }
            case "FreeHandlingForOrdersOver":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["FreeHandlingForOrdersOver"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["FreeHandlingForOrdersOver"];
                }
                break;
            }
            case "FreeShippingForOrdersOver":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["FreeShippingForOrdersOver"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["FreeShippingForOrdersOver"];
                }
                break;
            }
            case "MinimumShippingCost":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["MinimumShippingCost"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["MinimumShippingCost"];
                }
                break;
            }
            case "PerOrderShippingFee":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["PerOrderShippingFee"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["PerOrderShippingFee"];
                }
                break;
            }
            case "TotalShippingCharge":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["TotalShippingCharge"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["TotalShippingCharge"];
                }
                break;
            }
            case "PerItemHandlingCostSum":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["PerItemHandlingCostSum"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["PerItemHandlingCostSum"];
                }
                break;
            }
            case "PerOrderHandlingFee":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["PerOrderHandlingFee"];
                }
                else
                {
                    if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["PerOrderHandlingFee"];
                }
                break;
            }
            case "TotalHandlingCharge":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["TotalHandlingCharge"];
                }
                else
                {
                if ($_shipping_cost_array == null)
                        $value = PRICE_N_A;
                    else
                        $value = $_shipping_cost_array["shipping_cost"]["TotalHandlingCharge"];
                }
                break;
            }
            //          Exempt
            case "IncludedTax_NoExempts":
            {
                //             Subtotal       .
                //                 "                                subtotal",
                //              - 0.
                if(!empty($orderInfo))
                {
                    //:           ,
                    //                         (                             ,
                    //  TaxDisplayOptions)
                    //                               .
                    //                                                   !

                    //
                    //                                 ,
                    //  "          ".
                    //  TaxDisplayOption' ,                        -    .

                    //                                            ,
                    //          (2007.07.30)       -       Checkout::getOrderPrice()
                    //
                    //    $order_total = $o_discounted_subtotal +
                    //                   $o_shipping +
                    //                   $o_tax;
                    //    if($display_totals_including_taxes === true)
                    //    {
                    //        $value = $value - $o_included_tax;
                    //    }
                    $order_tax = $this->getOrderPrice("Tax", $main_store_currency);
                    $o_tax = $order_tax == PRICE_N_A ? 0 : $order_tax;

                    $order_not_included_tax = $this->getOrderPrice("NotIncludedTax", $main_store_currency);
                    $o_not_included_tax = $order_not_included_tax == PRICE_N_A ? 0 : $order_not_included_tax;

                    $value = $order_tax - $order_not_included_tax;
                    if($value < 0.0)
                    {
                        $value = 0.0;
                    }
                }
                else
                {
                    $display_totals_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");

                    if($display_totals_including_taxes == DB_TRUE)
                    {
                        $result = modApiFunc("Taxes", "getTax", true);
                        $value = isset($result['IncludedTaxTotalAmount']) ? $result['IncludedTaxTotalAmount'] : 0.0;
                    }
                    else
                    {
                        $value = 0.0;
                    }
                }
                break;
            }
            case "IncludedTax":
            {
                if(!empty($orderInfo))
                {
                    //                full tax exempt
                    $full_tax_exempt_status = (sizeof(modApiFunc("TaxExempts", "getOrderFullTaxExempts", $orderInfo['ID'])) == 1) ? DB_TRUE : DB_FALSE;
                }
                else
                {
                    $full_tax_exempt_status = modApiFunc("TaxExempts", "getFullTaxExemptStatus");
                }
                //                            Exempt -         0,       - 'IncludedTax_NoExempts'
                switch($full_tax_exempt_status)
                {
                    case DB_TRUE:
                        $value = 0.0;
                        break;
                    default:
                        $value = $this->getOrderPrice("IncludedTax_NoExempts", $main_store_currency, $shipping_method_cost);
                        break;
                }
                break;
            }

            //          Exempt
            case "NotIncludedTax_NoExempts":
            {
                //             Subtotal       .
                //                 "                                subtotal"
                //                                   .
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["OrderNotIncludedTaxTotal"];
                }
                else
                {
                    $order_tax = $this->getOrderPrice("Tax", $main_store_currency);
                    $o_tax = $order_tax == PRICE_N_A ? 0 : $order_tax;

                    $order_included_tax = $this->getOrderPrice("IncludedTax", $main_store_currency);
                    $o_included_tax = $order_included_tax == PRICE_N_A ? 0 : $order_included_tax;

                    $value = $o_tax - $o_included_tax;
//                                                         - GST:                                         .
//                    if($value < 0.0)
//                    {
//                        $value = 0.0;
//                    }
                }
                break;
            }
            case "NotIncludedTax":
            {
                if(!empty($orderInfo))
                {
                    //                full tax exempt
                    $full_tax_exempt_status = (sizeof(modApiFunc("TaxExempts", "getOrderFullTaxExempts", $orderInfo['ID'])) == 1) ? DB_TRUE : DB_FALSE;
                }
                else
                {
                    $full_tax_exempt_status = modApiFunc("TaxExempts", "getFullTaxExemptStatus");
                }
                //                            Exempt -         0,       - 'NotIncludedTax_NoExempts'
                switch($full_tax_exempt_status)
                {
                    case DB_TRUE:
                        $value = 0.0;
                        break;
                    default:
                        $value = $this->getOrderPrice("NotIncludedTax_NoExempts", $main_store_currency, $shipping_method_cost);
                        break;
                }
                break;
            }

            //          Exempt
            case "Tax_NoExempts":
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["OrderTaxTotal"];
                }
                else
                {
                    $result = modApiFunc("Taxes", "getTax");
                    $value = isset($result['TaxTotalAmount']) ? $result['TaxTotalAmount'] : 0.0;
                }
                $value = ($value == PRICE_N_A) ? 0.0 : $value;
                break;
            }
            case "Tax":
            {
                if(!empty($orderInfo))
                {
                    //                full tax exempt
                    $full_tax_exempt_status = (sizeof(modApiFunc("TaxExempts", "getOrderFullTaxExempts", $orderInfo['ID'])) == 1) ? DB_TRUE : DB_FALSE;
                }
                else
                {
                    $full_tax_exempt_status = modApiFunc("TaxExempts", "getFullTaxExemptStatus");
                }

                switch($full_tax_exempt_status)
                {
                    case DB_TRUE:
                        $value = 0.0;
                        break;
                    default:
                        $value = $this->getOrderPrice("Tax_NoExempts", $main_store_currency, $shipping_method_cost);
                        break;
                }
                break;
            }

            case "Payment":
            {
                $value = 0;
                break;
            }
            case "Total":
            {
                $from_code = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization','getLocalMainCurrency'));
                $to_code = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization','getLocalDisplayCurrency'));
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["OrderTotal"];
                }
                else
                {
                    //: Shipping may not be defined. Or tax may not be defined.
                    $order_discounted_subtotal = $this->getOrderPrice("DiscountedSubtotal", $main_store_currency);
                    $o_discounted_subtotal = $order_discounted_subtotal == PRICE_N_A ? 0 : $order_discounted_subtotal;

                    $order_shipping = $this->getOrderPrice("TotalShippingAndHandlingCost", $main_store_currency);
                    $o_shipping = $order_shipping == PRICE_N_A ? 0 : $order_shipping;

    /*                $order_tax = $this->getOrderPrice("Tax");
                    $o_tax = $order_tax == PRICE_N_A ? 0 : $order_tax;

                    $order_included_tax = $this->getOrderPrice("IncludedTax");
                    $o_included_tax = $order_included_tax == PRICE_N_A ? 0 : $order_included_tax; */

                    $order_not_included_tax = $this->getOrderPrice("NotIncludedTax", $main_store_currency);
                    $o_not_included_tax = $order_not_included_tax == PRICE_N_A ? 0 : $order_not_included_tax;

                    if($from_code != $to_code)
                    {
                        //$o_discounted_subtotal = number_format(modApiFunc("Currency_Converter", "convert", $o_discounted_subtotal, $from_code, $to_code), 2, '.', '');
                        $o_discounted_subtotal = modApiFunc("Currency_Converter", "convert", $o_discounted_subtotal, $from_code, $to_code);
                        $o_shipping= number_format(modApiFunc("Currency_Converter", "convert", $o_shipping, $from_code, $to_code), 2, '.', '');
                        //$order_not_included_tax = number_format(modApiFunc("Currency_Converter", "convert", $order_not_included_tax, $from_code, $to_code), 2, '.', '');
                        $order_not_included_tax = modApiFunc("Currency_Converter", "convert", $order_not_included_tax, $from_code, $to_code);
                        $value = modApiFunc("Currency_Converter", "convert",
                                         $o_discounted_subtotal + $o_shipping + $order_not_included_tax,
                                         $to_code, $from_code);
                    }
                    else
                    {
                        $value = $o_discounted_subtotal +
                                 $o_shipping +
                                 $order_not_included_tax;
                    }

    /*                //     included                      subtotal -                 ,
                    //       .
                    $display_totals_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");
                    if($display_totals_including_taxes == DB_TRUE)
                    {
                        $value = $value - $o_included_tax;
                    } */

                    if($value < 0.0)
                    {
                        $value = 0.0;
                    }
                }

                break;
            }
            case 'TotalToPay':
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["OrderTotalToPay"];
                }
                else
                {
                    $total = $this->getOrderPrice("Total", $main_store_currency);
                    $value = modApiFunc('GiftCertificateApi','getCurrentBalance', $total);

                    if($value < 0.0)
                    {
                        $value = 0.0;
                    }
                }
                break;
            }
            case 'TotalPrepaidByGC':
            {
                if(!empty($orderInfo))
                {
                    $value = $orderInfo["Price"]["OrderTotalPrepaidByGC"];
                }
                else
                {
                    $value = $this->getOrderPrice("Total", $main_store_currency) - $this->getOrderPrice("TotalToPay", $main_store_currency);
                }
                break;
            }
            default:
            {
                $err_params = array(
                                    "CODE"    => "CHECKOUT_005"
                                   );
                _fatal($err_params, $type);
                break;
            }
        }

        //                                                           -               .
        if(empty($orderInfo) &&
           $main_store_currency != $currency_id)
        {
        	$code_from = modApiFunc("Localization", "getCurrencyCodeById", $main_store_currency);
            $code_to = modApiFunc("Localization", "getCurrencyCodeById", $currency_id);
            $value = modApiFunc("Currency_Converter", "convert", $value, $code_from, $code_to);
        }

        //         :
        $value = modApiFunc("Localization", "currency_round", $value, $currency_id);

        $__cache__->write($__cache_key__, $value);

        return $value;
    }

    /**
     * Returns a total product weight.
     *
     * It is called by shipping modules.
     */
    function getOrderWeight($type)
    {
        $value = 0;
        switch($type)
        {
            case "netto":
                $value = modApiFunc("Cart", "getCartProductsWeightNetto");
                break;
            case "brutto":
                $value = $this->getOrderWeight("netto") +
                         $this->getOrderWeight("tare");
                break;
            case "tare":
                $value = 0.0;
                break;
            default:
                $err_params = array(
                                    "CODE"    => "CHECKOUT_006"
                                   );
                _fatal($err_params, $type);
                break;
        }
        return $value;
    }

    /**
     * Returns a product number in the order.
     * It is called by shipping modules.
     */
    function getOrderProductsNumber()
    {
        return modApiFunc("Cart", "getCartProductsQuantity");
    }

    /**
     * Checks if the order id is valid.
     *
     * @param integer $order_id
     * @return boolean
     * @ do the caching
     */
    function isCorrectOrderId($order_id)
    {
        $order_id += 0;
        // the id can't be a string or has a negative value.
        if (empty($order_id) || !is_int($order_id) || $order_id <= 0)
        {
            return false;
        }

        $result = execQuery('SELECT_NUMBER_OR_ORDERS_BY_ID',array('order_id'=>$order_id));
        // there should only one order that has such id
        if ($result[0]['count_id'] != 1)
        {
            return false;
        }

        return true;
    }

    /**
     * Returns a list of domain values for the order status.
     */
    function getOrderStatusList($s_id=NULL)
    {
        $result = execQuery('SELECT_ORDER_STATUS_LIST', array('s_id'=>$s_id));
        $array = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $array[$result[$i]['id']] = array(
                'id' => $result[$i]['id']
               ,'name' => getMsg('SYS', $result[$i]['name'])
               ,'descr' => getMsg('SYS', $result[$i]['descr'])
            );
        }
        return $array;
    }

    /**
     * Returns a list of domain values for the order payment status.
     */
    function getOrderPaymentStatusList($ps_id=NULL)
    {
        $result = execQuery('SELECT_ORDER_PAYMENT_STATUS_LIST', array('ps_id'=>$ps_id));
        $array = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $array[$result[$i]['id']] = array(
                'id' => $result[$i]['id']
               ,'name' => getMsg('SYS', $result[$i]['name'])
               ,'descr' => getMsg('SYS', $result[$i]['descr'])
            );
        }
        return $array;
    }

    function getBaseOrderInfo($order_id)
    {
        global $application;
        if (!$this->isCorrectOrderId($order_id))
        {
            return null;
        }
        $result  = execQuery('SELECT_BASE_ORDER_INFO', array('order_id'=>$order_id));

        $order = array();
        $order['ID'] = $this->outputOrderId($result[0]['id']);
        $order['Date'] = $result[0]['date'];
        $order['StatusId'] = $result[0]['status_id'];
        $order['Status'] = getMsg('SYS', $result[0]['status']);
        $order['PaymentStatusId'] = $result[0]['payment_status_id'];
        $order['PaymentStatus'] = getMsg('SYS', $result[0]['payment_status']);
        $order['PaymentMethod'] = $result[0]['payment_method'];
        $order['PaymentModuleId'] = $result[0]['payment_module_id'];
        $order['PaymentMethodDetail'] = $result[0]['payment_method_detail'];
        $order['PaymentProcessorOrderId'] = $result[0]['payment_processor_order_id'];
        $order['ShippingMethod'] = $result[0]['shipping_method'];
        $order['TrackId'] = $result[0]['track_id'];
        $order['AffiliateId'] = $result[0]['affiliate_id'];
        $order['PersonId'] = $result[0]['person_id'];
        $order["NewType"] = $result[0]["new_type"];
        $order["DisplayIncludedTax"] = $result[0]["included_tax"];

        return $order;
    }

    /**
     * Returns order info with the specified number.
     *      $currency_id == NULL,                                  currency,
     *            currency_type = CURRENCY_TYPE_MAIN_STORE_CURRENCY
     */
    function getOrderInfo($order_id, $currency_id /* MAIN_STORE_CURRENCY */, $b_encrypted = false)
    {
        global $application;
        if (!$this->isCorrectOrderId($order_id))
        {
            return null;
        }

        $currency_code = modApiFunc("Localization", "getCurrencyCodeById", $currency_id);
        $result  = execQuery('SELECT_BASE_ORDER_INFO', array('order_id'=>$order_id));
        $_prices = execQuery('SELECT_ORDER_PRICES', array('order_id'=>$order_id, 'currency_code'=>$currency_code));

        $prices = array();
        for ($i=0; $i<sizeof($_prices); $i++)
        {
            //Remove artificial attributes: ShippingCostCorrector Settings
            $prices['OrderTotalToPay']              = $_prices[$i]['order_total_to_pay'];
            $prices['OrderTotalPrepaidByGC']        = $_prices[$i]['order_total_paid_by_gc'];
            $prices['OrderTotal']                   = $_prices[$i]['order_total'];
            $prices['OrderSubtotal']                = $_prices[$i]['order_subtotal'];
            $prices['MinimumShippingCost']          = $_prices[$i]['minimum_shipping_cost'];
            $prices['FreeShippingForOrdersOver']    = $_prices[$i]['free_shipping_for_orders_over'];
            $prices['FreeHandlingForOrdersOver']    = $_prices[$i]['free_handling_for_orders_over'];
            $prices['PerItemShippingCostSum']       = $_prices[$i]['per_item_shipping_cost_sum'];
            $prices['PerOrderShippingFee']          = $_prices[$i]['per_order_shipping_fee'];
            $prices['ShippingMethodCost']           = $_prices[$i]['shipping_method_cost'];
            $prices['TotalShippingCharge']          = $_prices[$i]['total_shipping_charge'];
            $prices['PerItemHandlingCostSum']       = $_prices[$i]['per_item_handling_cost_sum'];
            $prices['PerOrderHandlingFee']          = $_prices[$i]['per_order_handling_fee'];
            $prices['TotalHandlingCharge']          = $_prices[$i]['total_handling_charge'];
            $prices['TotalShippingAndHandlingCost'] = $_prices[$i]['total_shipping_and_handling_cost'];
            $prices['OrderTaxTotal']                = $_prices[$i]['order_tax_total'];
            $prices['SubtotalGlobalDiscount']       = $_prices[$i]['subtotal_global_discount'];
            $prices['SubtotalPromoCodeDiscount']    = $_prices[$i]['subtotal_promo_code_discount'];
            $prices['QuantityDiscount']             = $_prices[$i]['quantity_discount'];
            $prices['DiscountedSubtotal']           = $_prices[$i]['discounted_subtotal'];
            $prices['OrderNotIncludedTaxTotal']     = $_prices[$i]['order_not_included_tax_total'];
//            $prices["isOrderEditable"]              = $result["new_type"];
        }

        // get order taxes
        $_taxes = execQuery('SELECT_ORDER_TAXES', array('order_id'=>$order_id, 'currency_code'=>$currency_code));

        $prices["taxes"] = array();
        for ($i=0; $i<sizeof($_taxes); $i++)
        {
            $prices["Taxes['" . $_taxes[$i]['type'] . "']"] = $_taxes[$i]['value'];
            $prices["taxes"][$_taxes[$i]["id"]]["name"] = $_taxes[$i]['type'];
            $prices["taxes"][$_taxes[$i]["id"]]["value"] = $_taxes[$i]['value'];
            $prices["taxes"][$_taxes[$i]["id"]]["is_included"] = $_taxes[$i]["is_included"];
        }

        //  get order tax display options
        $_tax_dops = execQuery('SELECT_ORDER_TAX_DISPLAY_OPTIONS', array('order_id'=>$order_id, 'currency_code'=>$currency_code));

        $prices["tax_dops"] = array();
        for ($i=0; $i<sizeof($_tax_dops); $i++)
        {
            $prices["tax_dops"][$_tax_dops[$i]["id"]]["name"] = $_tax_dops[$i]['name'];
            $prices["tax_dops"][$_tax_dops[$i]["id"]]["value"] = $_tax_dops[$i]['value'];
            $prices["tax_dops"][$_tax_dops[$i]["id"]]["formula"] = $_tax_dops[$i]["formula"];
        }

        $_person_data = execQuery('SELECT_ORDER_BILLING_SHIPPING_INFO', array('order_id'=>$order_id, 'b_encrypted'=>$b_encrypted));
        $person_data = array();
        for ($i=0; $i<sizeof($_person_data); $i++)
        {
            if (!isset($person_data[$_person_data[$i]['pit_tag']]))
            {
                $person_data[$_person_data[$i]['pit_tag']] = array(
                    'id'    => $_person_data[$i]['pit_id']
                   ,'person_info_variant_id' => $_person_data[$i]['person_info_variant_id']
                   ,'tag'   => $_person_data[$i]['pit_tag']
                   ,'name'  => $_person_data[$i]['pit_name']
                   ,'descr' => $_person_data[$i]['pit_descr']
                   ,'attr'  => array()
                );
            }
            $info_tag = $_person_data[$i]['pit_tag'];
            $attr_tag = $_person_data[$i]['pa_tag'];
            $person_data[$info_tag]['attr'][$attr_tag] = array (
                'id'                   => $_person_data[$i]['id']
               ,'person_attribute_id'  => $_person_data[$i]['person_attribute_id']
               ,'tag'                  => $attr_tag
               ,'name'                 => $_person_data[$i]['name']
               ,'value'                => $_person_data[$i]['value']
               ,'descr'                => $_person_data[$i]['descr']
               ,'b_encrypted'          => $_person_data[$i]['b_encrypted']
               ,'encrypted_secret_key' => $_person_data[$i]['encrypted_secret_key']
               ,'rsa_public_key_asc_format' =>
                                          $_person_data[$i]['rsa_public_key_asc_format']
               ,'input_type_id'        => $_person_data[$i]['pa_input_type_id']
                );
        }

        $_products = execQuery('SELECT_ORDER_PRODUCTS', array('order_id'=>$order_id));
        $products = array();

        for ($i=0; $i<sizeof($_products); $i++)
        {
            $product = array(
                'id' => $_products[$i]['id']
               ,'storeProductID' => $_products[$i]['store_id']
               ,'inventory_id' => $_products[$i]['inventory_id']
               ,'name' => $_products[$i]['name']
               ,'type' => $_products[$i]['type']
               ,'qty' => $_products[$i]['qty']
               ,'attr' => array()
            );

            $_attr = execQuery('SELECT_ORDER_PRODUCT_ATTRIBUTES', array('order_product_id'=>$_products[$i]['id'], 'currency_code'=>$currency_code));
            for ($j=0; $j<sizeof($_attr); $j++)
            {
                $a_tag = $_attr[$j]['a_tag'];
                $product['attr'][$a_tag] = array(
                    'name'  => $_attr[$j]['a_name']
                   ,'value' => $_attr[$j]['value']
                );
                $product[$a_tag] = $_attr[$j]['value'];
            }

            $product['options'] = execQuery('SELECT_ORDER_PRODUCT_OPTIONS', array('order_product_id'=>$_products[$i]['id'], 'currency_code'=>$currency_code));
            $product['custom_attributes'] = execQuery('SELECT_ORDER_PRODUCT_CUSTOM_ATTRIBUTES', array('order_product_id'=>$_products[$i]['id']));
            $products[] = $product;
        }

        $_notes = execQuery('SELECT_ORDER_NOTES', array('order_id'=>$order_id));
        $comments = array();
        $history = array();
        for ($i=0; $i<sizeof($_notes); $i++)
        {
            if ($_notes[$i]['type'] == 'comment')
            {
                $_notes[$i]['content'] = str_replace("\n", '<br/>', $_notes[$i]['content']);
                $comments[] = $_notes[$i];
            }
            elseif ($_notes[$i]['type'] == 'history')
            {
                $_notes[$i]['content'] = str_replace("\n", '<br/>', $_notes[$i]['content']);
                $history[] = $_notes[$i];
            }
        }

        $order = array();
        $order['ID'] = $this->outputOrderId($result[0]['id']);
        $order['Date'] = $result[0]['date'];
        $order['StatusId'] = $result[0]['status_id'];
        $order['Status'] = getMsg('SYS', $result[0]['status']);
        $order['PaymentStatusId'] = $result[0]['payment_status_id'];
        $order['PaymentStatus'] = getMsg('SYS', $result[0]['payment_status']);
        $order['PaymentMethod'] = $result[0]['payment_method'];
        $order['PaymentModuleId'] = $result[0]['payment_module_id'];
        $order['PaymentMethodDetail'] = $result[0]['payment_method_detail'];
        $order['PaymentProcessorOrderId'] = $result[0]['payment_processor_order_id'];
        $order['ShippingMethod'] = $result[0]['shipping_method'];
        $order['TrackId'] = $result[0]['track_id'];
        $order['AffiliateId'] = $result[0]['affiliate_id'];
        $order['Products'] = $products;
        $order['Subtotal'] = $prices['OrderSubtotal'];
        $order['Total'] = $prices['OrderTotal'];
        $order['TotalToPay'] = $prices['OrderTotalToPay'];
        $order['Price'] = $prices;
        $order['Comments'] = $comments;
        $order['History'] = $history;
        $order['Billing'] = array_key_exists('billingInfo', $person_data) ? $person_data['billingInfo'] : array('attr' => array());
        $order['Shipping'] = array_key_exists('shippingInfo', $person_data) ? $person_data['shippingInfo'] : array('attr' => array());
        $order['CreditCard'] = array_key_exists('creditCardInfo', $person_data) ? $person_data['creditCardInfo'] : array('attr' => array());
        $order['BankAccount'] = array_key_exists('bankAccountInfo', $person_data) ? $person_data['bankAccountInfo'] : array('attr' => array());
        $order['PersonId'] = $result[0]['person_id'];
        $order["NewType"] = $result[0]["new_type"];
        $order["DisplayIncludedTax"] = $result[0]["included_tax"];

        return $order;
    }

    function outputOrderId($id)
    {
        return sprintf("%05d", $id);
    }
    /**
     * Returns the id, chosen for pament module.
     * CZ.
     * WARNING: the customer should have chosen the module by that moment!
     */
    function getChosenPaymentModuleIdCZ()
    {
        $PrerequisiteValidationResults = $this->getPrerequisiteValidationResults('paymentModule');
        $module_id = empty($PrerequisiteValidationResults['validatedData']['method_code']['value']) ? NULL : $PrerequisiteValidationResults['validatedData']['method_code']['value'];
        if(NULL == $module_id)
        {
            //Output an error message: Payment Module (payment method) is not chosen.
            //: TEST IT
            //Returns NULL, and for example, not the id of the AllInactive module, because
            //  nobody will know what is wrong: no methods were activated,
            //  but PaymentMethodInput were in the templates, or prerequisite PaymentModule
            //  was removed from checkout-config.ini, or it was left
            //  PaymentMethodOutput, but PaymentMethodInput was removed.
            //So each object that calls getChosenPaymentModuleIdCZ can processes
            //   NULL, the way it likes:
            //  for example:
            //  outputs null or AllInactive data, or an error, or something else.
            return NULL;

            ///$err_params = array(
            /// "CODE"    => "CHECKOUT_001"
            ///);
            ///_fatal($err_params);
        }
        else
        {
            return $module_id;
        }
    }

    function getChosenShippingModuleIdCZ()
    {
        $PrerequisiteValidationResults = $this->getPrerequisiteValidationResults('shippingModuleAndMethod');
        if($PrerequisiteValidationResults["isMet"] == false)
        {
            return NULL;
        }

        $module_and_method_ids = empty($PrerequisiteValidationResults['validatedData']['method_code']['value']) ? NULL : $PrerequisiteValidationResults['validatedData']['method_code']['value'];
        if(NULL == $module_and_method_ids)
        {
            return NULL;

            /// Output an error message: Shipping Module (shipping method) is not chosen.
            ////: TEST IT
            //$err_params = array(
            //      "CODE"    => "CHECKOUT_002"
            //);
            //_fatal($err_params);
        }
        else
        {
            //// Module and Method
            $shipping_module_and_method_pattern = "/^([0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12})_(\d+)$/";

            $matches = array();
            if(preg_match($shipping_module_and_method_pattern, $module_and_method_ids, $matches))
            {
                return $matches[1];
            }
            else
            {

                /////Output an error message: shipping module/method data format is not valid.
                /////: TEST IT
                ///$err_params = array(
                /////Must NOT be.
                ///     "CODE"    => "CHECKOUT_003"
                ///);
                ///_fatal($err_params);
                return NULL;
            }
        }
    }

    function getChosenShippingMethodIdCZ()
    {
        $PrerequisiteValidationResults = $this->getPrerequisiteValidationResults('shippingModuleAndMethod');
        $module_and_method_ids = empty($PrerequisiteValidationResults['validatedData']['method_code']['value']) ? NULL : $PrerequisiteValidationResults['validatedData']['method_code']['value'];
        if(NULL == $module_and_method_ids)
        {
            //  Output an error message: Shipping Module (shipping method) is not chosen.
            //: TEST IT
            return NULL;

            ///$err_params = array(
            ///     "CODE"    => "CHECKOUT_002"
            ///);
            ///_fatal($err_params);
        }
        else
        {
            //// Module and Method
            $shipping_module_and_method_pattern = "/^([0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12})_(\d+)$/";

            $matches = array();
            if(preg_match($shipping_module_and_method_pattern, $module_and_method_ids, $matches))
            {
                return $matches[2];
            }
            else
            {
                //Output an error message: shipping module/method data format is not valid.
                //: TEST IT
                //Must not be
                ///$err_params = array(
                ///     "CODE"    => "CHECKOUT_003"
                ///);
                ///_fatal($err_params);
                return NULL;
            }
        }
    }

    /* static */ function getModululeNameByUUID($module_type, $module_id)
    {
        //
        //  1.              UUID
        //  2.            API                               moduleInfo

        //                             1.:
        //
        //               . Checkout
        //          (                            ,
        //                         ),   ModuleManager           UUID.

        //                                Checkout -
        //                                  ,                  -
        //                                       ModuleManager.
        global $application;
        $PmSmName = NULL;

        $selected_modules = Checkout::getSelectedModules($module_type);
        if(array_key_exists($module_id, $selected_modules))
        {
            $PmSmName = $selected_modules[$module_id]["module_class_name"];
        }
        else
        {
            $pm_sm_list = Checkout::getInstalledModulesListData($module_type);
            $items = array();
            foreach ($pm_sm_list as $pm_sm_item)
            {
                // create/use some mm function to convert class names.
                $name = _ml_strtolower($pm_sm_item->name);

                $pm_sm_item_uuid = modApiStaticFunc($name, "getUid");
                if($pm_sm_item_uuid == $module_id)
                {
                    $PmSmName = $name;
                    break;
                }
            }
        }
        return $PmSmName;
    }

    /* static */ function getPaymentModuleInfo($module_id = null)
    {
        global $application;
        $Name = Checkout::getModululeNameByUUID("payment", $module_id);
        if($Name === NULL)
        {
        	return NULL;
        }
        else
        {
            $Info = modApiFunc($Name, "getInfo");
            return $Info;
        }
     }

    /* static */ function getShippingModuleInfo($module_id = null)
    {
        global $application;

        if($module_id == $this->getNotNeedShippingModuleID())
        {
            return array(
                'GlobalUniqueShippingModuleID' => $module_id
               ,'APIClassName' => 'Shipping_Not_Needed'
            );
        }

        $Name = Checkout::getModululeNameByUUID("shipping", $module_id);
        if($Name === NULL)
        {
            return NULL;
        }
        else
        {
            $Info = modApiFunc($Name, "getInfo");
            return $Info;
        }
    }

    function getShippingMethodInfo($module_id = null, $method_id = null)
    {
        if($module_id == $this->getNotNeedShippingModuleID() and $method_id == $this->getNotNeedShippingMethodID())
        {
            return array(
                'id' => $method_id
               ,'method_name' => $this->getNotNeedShippingMethodName()
               ,'method_code' => 'NNS'
               ,'destination' => 'L'
               ,'available' => 'Y'
               ,'cost' => 0.0
            );
        }

        global $application;

        $sm_list = Checkout::getInstalledModulesListData("shipping");

        $items = array();
        foreach ($sm_list as $sm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($sm_item->name);

            $smInfo = modApiFunc($name, "getInfo");

            if($smInfo['GlobalUniqueShippingModuleID'] == $module_id)
            {
                $method_info = modApiFunc($name, "getShippingMethodInfo", $method_id);
                return $method_info;
            }
            //// : check if function exists
            //call_user_func($smInfo['CZInputViewClassName']));
        }
        return null;
    }

    /**
     * Gives a list of all payment modules exist in the system.
     * I.e. loaded AVACTIS modules with     * 'groups', containing PaymentModule.
     */
    /* static */ function getInstalledModulesListData($ModuleClass, $groups = NULL, $b_with_uuid = false)
    {
        global $application;
        if($groups == null)
        {
            $groups = array();
        }
        if($ModuleClass == "payment")
        {
            $groups[] = "PaymentModule";
        }
        else if($ModuleClass == "shipping")
        {
            $groups[] = "ShippingModule";
        }
        else
        {
            _fatal(__FILE__ . " : " . __LINE__);
        }
        $m_list = modApiFunc("Modules_Manager", "getActiveModules", $groups);

        //Return also UUID for each module. It's slow, but sometimes necessary.
        if($b_with_uuid === true)
        {
            foreach($m_list as $key => $m_info)
            {
                $m_info =& $m_list[$key];

                // omitted for heavy memory load
                if ($ModuleClass == "payment")
                {
		    include($application->getAppIni("PATH_ASC_ROOT").$m_info->directory."/includes/uid.php");
                    $m_info->UUID = $uid;
                }
                else
                {
                    $m_info->UUID = modApiStaticFunc($m_info->name, "getUid");
                }
                unset($m_info);
            }
        }
        return $m_list;
    }

    /**
     * Gives a list of all payment/... modules exist in the system.
     * I.e. loaded AVACTIS modules with     * 'groups', containing PaymentModule/....
     * and activated by administrator or automatically.
     */
    /*static*/ function getInstalledAndActiveModulesListData($ModuleClass, $groups = NULL)
    {
        global $application;
        if($groups == null)
        {
            $groups = array();
        }
        if($ModuleClass == "payment")
        {
            $groups[] = "PaymentModule";
        }
        else if($ModuleClass == "shipping")
        {
            $groups[] = "ShippingModule";
        }
        else
        {
            _fatal(__FILE__ . " : " . __LINE__);
        }

        $m_list = modApiFunc("Modules_Manager", "getActiveModules", $groups);
        $active_modules = Checkout::getActiveModules($ModuleClass);
        //Make a name array for
        $active_modules_class_names = array();
        foreach($active_modules as $uuid => $m_info)
        {
            $active_modules_class_names[$m_info["module_class_name"]] = $m_info;
        }
        //the end of making the name list.

        foreach($m_list as $key => $module_info)
        {
            if(!array_key_exists($module_info->name, $active_modules_class_names))
            {
                unset($m_list[$key]);
            }
        }
        return $m_list;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    function getCartDefaultCurrencyCode()
    {
        $default_currency = modApiFunc("Localization", "getMainStoreCurrency");
        $default_currency_code = modApiFunc("Localization", "getCurrencyCodeById", $default_currency);
        return $default_currency_code;
    }

    function getCartCurrencies()
    {
        $default_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
        $default_currency_code = modApiFunc("Localization", "getCurrencyCodeById", $default_currency_id);
        $cart_currencies = array();
        $cart_currencies[] = array
        (
            "code" => $default_currency_code
           ,"type" => CURRENCY_TYPE_MAIN_STORE_CURRENCY
        );

        //                                 ,                        -
        $customer_currency_id = modApiFunc("Localization", "getSessionDisplayCurrency");
        $customer_currency_code = modApiFunc("Localization", "getCurrencyCodeById", $customer_currency_id);
        if($customer_currency_id != $default_currency_id)
        {
            $cart_currencies[] = array
            (
                "code" => $customer_currency_code
               ,"type" => CURRENCY_TYPE_CUSTOMER_SELECTED
            );
        }

        //                                                                ,               main store
        //  currency      customer selected currency,                            .
        $pm_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
        if($pm_id !== NULL)
        {
        	$pm_currency_id = modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", ORDER_NOT_CREATED_YET, $pm_id);
            $pm_currency_code = modApiFunc("Localization", "getCurrencyCodeById", $pm_currency_id);
            if($pm_currency_id != $default_currency_id &&
               $pm_currency_id != $customer_currency_id)
	        {
	            $cart_currencies[] = array
	            (
	                "code" => $pm_currency_code
	               ,"type" => CURRENCY_TYPE_PAYMENT_GATEWAY
	            );
	        }
        }

        return $cart_currencies;
    }

    /**
     *                 ,                        .
     *                                ,
     *                       .
     */
    function getProductAttributesOfPriceType()
    {
    	return array
    	(
            SALE_PRICE_PRODUCT_ATTRIBUTE_ID => ""
           ,LIST_PRICE_PRODUCT_ATTRIBUTE_ID => ""
           ,PER_ITEM_SHIPPING_COST_PRODUCT_ATTRIBUTE_ID => ""
           ,PER_ITEM_HANDLING_COST_PRODUCT_ATTRIBUTE_ID => ""
    	);
    }




    /**
     * Adds product info.
     */
    function addOrderProducts($order_id, $CartProductsInfo)
    {
        global $application;

		$cart_currencies = modApiFunc("Checkout", "getCartCurrencies");
        $default_currency_code = modApiFunc("Checkout", "getCartDefaultCurrencyCode");
        $product_attributes_of_price_type = modApiFunc("Checkout", "getProductAttributesOfPriceType");
        $manufacturer_attr_id = modApiFunc('Catalog', 'getManufacturerAttrId');
        $event_data = array();

        foreach($CartProductsInfo as $CartProductInfo)
        {
            $ProductInfoRaw = modApiFunc("Catalog", "getProductInfoRaw", $CartProductInfo['ID']);

            $params = array(
                                'order_id'          => $order_id,
                                'Quantity_In_Cart'  => $CartProductInfo['Quantity_In_Cart'],
                                'ProductName'       => $ProductInfoRaw['attributes']['Name']['value'],
                                'ProductTypeID'     => $ProductInfoRaw['attributes']['TypeID']['value'],
                                'ProductID'         => $ProductInfoRaw['attributes']['ID']['value'],
                                'inventory_id'      => $CartProductInfo['InventoryID']
								,'colorname'         => $CartProductInfo['Colorname']
                           );
            execQuery('INSERT_ORDER_PRODUCTS', $params);
            $inserted_order_product_id = $application->db->DB_Insert_Id();

            unset($ProductInfoRaw['attributes']['ID']);
            unset($ProductInfoRaw['attributes']['Name']);
            unset($ProductInfoRaw['attributes']['TypeID']);
            unset($ProductInfoRaw['ID']);
            unset($ProductInfoRaw['Name']);
            unset($ProductInfoRaw['TypeID']);

            loadCoreFile('db_multiple_insert.php');
            $query = new DB_Multiple_Insert('order_product_to_attributes');
            $tables = $this->getTables();
            $opta = $tables['order_product_to_attributes']['columns'];
            $params = array(
                                $opta['currency_code'],
                                $opta['currency_type'],
                                $opta['attribute_id'],
                                $opta['value'],
                                $opta['product_id'],
                           );
            $query->setInsertFields($params);

            foreach($ProductInfoRaw['attributes'] as $attr_info)
            {
            	if($attr_info['attr_type'] != "standard")
            	{
            	    continue;
            	}

                //Sale price -          .
                if($attr_info['id'] == SALE_PRICE_PRODUCT_ATTRIBUTE_ID)
                {
                    $display_totals_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");
                    if($display_totals_including_taxes == DB_TRUE)
                    {
                        $attr_info['value'] = $CartProductInfo['CartItemSalePriceIncludingTaxes'];
                    }
                    else
                    {
                        $attr_info['value'] = $CartProductInfo['CartItemSalePriceExcludingTaxes'];
                    }
                }

                //Manufacturer -             ,      id.
                if($attr_info['id'] == $manufacturer_attr_id)
                {
                	$info = modApiFunc("Manufacturers", "getManufacturerInfo", $attr_info['value']);
                	$attr_info['value'] = $info['manufacturer_name'];
                }

                reset($cart_currencies);
                foreach($cart_currencies as $currency)
                {
                    $currency_code = $currency["code"];
                    $currency_type = $currency["type"];

                    if($default_currency_code != $currency_code &&
                       array_key_exists($attr_info['id'], $product_attributes_of_price_type))
                    {
                        $value = modApiFunc("Currency_Converter", "convert", $attr_info['value'], $default_currency_code, $currency_code);
                    }
                    else
                    {
                        $value = $attr_info['value'];
                    }

					$params = array(
										$opta['currency_code'] => $currency_code,
										$opta['currency_type'] => $currency_type,
										$opta['attribute_id'] => $attr_info['id'],
										$opta['value'] => $value,
										$opta['product_id'] => $inserted_order_product_id,
								   );
					$query->addInsertValuesArray($params);
                }
            }
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();

            //Add custom attributes
            reset($ProductInfoRaw);
            foreach($ProductInfoRaw['attributes'] as $attr_info)
            {
                if($attr_info['attr_type'] != "custom")
                {
                    continue;
                }

                $params = array(
                                    'inserted_order_product_id' => $inserted_order_product_id,
                                    'attr_view_tag'     => $attr_info['view_tag'],
                                    'attr_view_vame'    => $attr_info['name'],
                                    'attr_view_value'   => $attr_info['value']
                               );
                execQuery('INSERT_ORDER_PRODUCT_CUSTOM_ATTRIBUTES', $params);
            }

            //Add options
            if(isset($CartProductInfo['Options']) and !empty($CartProductInfo['Options']))
            {
                foreach($CartProductInfo['Options'] as $option_id => $option_data)
                {
					list($oname, $oval, $modifiers) = modApiFunc("Product_Options","prepareDataForPlaceOrder",$option_id, $option_data);
					if($oname!="" and $oval!="")
					{
						$params = array(
								'inserted_order_product_id' => $inserted_order_product_id,
								'oname' => $oname,
								'oval' => $oval,
								'option_data' => $option_data,
						   );
						execQuery('INSERT_ORDER_PRODUCT_OPTIONS',$params);
					}
                }
            }

            //Update attributes, that have been modified by options

			if(isset($CartProductInfo['Colorname']) and !empty($CartProductInfo['Colorname']))
			{
				$params = array(
							 'inserted_order_product_id' => $inserted_order_product_id,
							 'oname' => 'Color Name',
							 'oval' => $CartProductInfo['Colorname'],
							);
							execQuery('INSERT_ORDER_PRODUCT_OPTIONS',$params);
			}

            if(isset($CartProductInfo['OptionsModifiers']) and !empty($CartProductInfo['OptionsModifiers']))
            {
                $mods_to_attr=modApiFunc("Product_Options","getModsMap");

                //update SKU and Inventory when it is needed
                if($CartProductInfo['InventoryID']!=null)
                {
                    $inv_inf = modApiFunc("Product_Options","getInventoryInfo",$CartProductInfo['InventoryID']);

                    if($inv_inf["sku"]!="")
                    {
                        $attr_info=modApiFunc("Catalog","getAttributeInfo",$CartProductInfo['TypeID'],'SKU');

                        $params = array(
                                            'inserted_order_product_id' => $inserted_order_product_id,
                                            'inv_inf_sku' => $inv_inf['sku'],
                                            'attr_info' => $attr_info
                                       );
                        execQuery('UPDATE_ORDER_PRODUCT_ATTRIBUTES_BY_MODIFIERS', $params);

                    }
                }
            }

            modApiFunc('Product_Files','genHotlinks',$CartProductInfo['ID'],$inserted_order_product_id);

			$event_data[] = array(
					'CART_ITEM_SALE_PRICE_INCLUDING_TAXES' => $CartProductInfo['CartItemSalePriceIncludingTaxes'],
					'PRODUCT_ID' => $CartProductInfo['ID'],
					'PRODUCT_QUANTITY' => $CartProductInfo['Quantity_In_Cart'],
				);
            //modApiFunc('EventsManager','throwEvent','ProductWasSold',$CartProductInfo['ID'],$CartProductInfo['Quantity_In_Cart']);
        }
        modApiFunc('EventsManager','throwEvent','ProductsWasSold',$event_data);
    }

    /**
     * Returns a list of Person Info types.
     *
     * @return array the array of variant ids and their visible prices
     */
    /* static */ function getPersonInfoTypeList()
    {
        $result = execQuery('SELECT_PERSON_INFO_TYPE_LIST', array());

        $array = array();
        for ($i = 0; $i < sizeof($result); $i++)
        {
            $array[$result[$i]['id']] = array(
                'type_id'    => $result[$i]['id']
               ,'active'  => $result[$i]['active']
               ,'tag' => $result[$i]['tag']
            );
        }

        return $array;
    }

    /**#@+
     * @access private
     */
    function updateOrderPaymentProcessorOrderID($order_id, $new_payment_processor_order_id)
    {
        $params = array('order_id' => $order_id,
                        'new_payment_processor_order_id' => $new_payment_processor_order_id);
        execQuery('UPDATE_ORDER_PAYMENT_PROCESSOR_ORDER_ID', $params);
    }

    /**#@+
     * @access private
     */
    function addOrder($data)
    {
        global $application;
        execQuery('INSERT_ORDER_DATA', $data);
        return $application->db->DB_Insert_Id();
    }

    function addOrderPromoCodeHistory($order_id, $promo_code_id)
    {
    	global $application;
        $messageResources = &$application->getInstance('MessageResources');
        $pc_info = modApiFunc("PromoCodes", "getPromoCodeInfo", $promo_code_id);
        $campaign_name = $pc_info['campaign_name'];
        $promo_code = $pc_info['promo_code'];
        switch($pc_info["discount_cost_type_id"])
        {
            case 1 /* FLAT RATE */:
            {
                $discount_text = modApiFunc("Localization", "currency_format", $pc_info["discount_cost"]);
                break;
            }
            case 2 /* PERCENT */:
            {
                $discount_text = modApiFunc("Localization", "num_format", $pc_info["discount_cost"]) . "%";
                break;
            }
            default:
            {
            	//: report error.
            	$discount_text = "";
            }
        }
        $history_text = $messageResources->getMessage
        (
            new ActionMessage
            (
                array('ORDERS_HISTORY_PROMO_CODE_APPLIED', $campaign_name, $promo_code, $discount_text)
            )
        );
        $this->addOrderHistory($order_id, $history_text);
    }

    function addOrderGCHistory($order_id, $gc_list)
    {
        global $application;
        loadClass('GiftCertificate');
        $messageResources = &$application->getInstance('MessageResources');

        foreach ($gc_list as $code=>$gc_obj)
        {
            $amount = modApiFunc("Localization", "currency_format", $gc_obj->amount);
            $remainder = modApiFunc("Localization", "currency_format", $gc_obj->remainder);

            $history_text = $messageResources->getMessage
            (
                new ActionMessage
                (
                    array('ORDERS_HISTORY_GIFT_CERTIFICATE_APPLIED', $code, $amount, $remainder)
                )
            );
            $this->addOrderHistory($order_id, $history_text);
        }
    }

    function addOrderFullTaxExemptHistory($order_id, $customer_input)
    {
        global $application;
        $messageResources = &$application->getInstance('MessageResources');
        $history_text = $messageResources->getMessage
        (
            new ActionMessage
            (
                array('ORDERS_HISTORY_FULL_TAX_EXEMPT_APPLIED', $customer_input)
            )
        );
        $this->addOrderHistory($order_id, $history_text);
    }

    /**#@+
     * @access private
     */
    function addOrderPrices($order_id, $old_format_order_prices, $tax_array)
    {
        global $application;
		//                         -                       TimesUsed
		$promo_code_id = modApiFunc("PromoCodes", "getPromoCodeId");

        if(modApiFunc("PromoCodes", "isPromoCodeIdSet") === true)
        {
            modApiFunc("PromoCodes", "updatePromoCodeTimesUsed", $promo_code_id);
            $this->addOrderPromoCodeHistory($order_id, $promo_code_id);
            modApiFunc("PromoCodes", "insertOrderCoupon", $order_id, $promo_code_id);
        }

        $gc_list = modApiFunc("GiftCertificateApi", "getCurrentGiftCertificateListFull");
        if(count($gc_list) > 0)
        {
            $this->addOrderGCHistory($order_id, $gc_list);
            modApiFunc("GiftCertificateApi", "insertOrderGC", $order_id, $gc_list);
        }

        $exempt_status = modApiFunc("TaxExempts", "getFullTaxExemptStatus");
        $customer_input = modApiFunc("TaxExempts", "getFullTaxExemptCustomerInput");
        modApiFunc("TaxExempts", "insertOrderFullTaxExempt", $order_id, $exempt_status, $customer_input);
        //                                                               -
        switch($exempt_status)
        {
            case DB_TRUE:
                $this->addOrderFullTaxExemptHistory($order_id, $customer_input);
                break;
            default:
                break;
        }

        $order_prices = array();
        foreach($old_format_order_prices as $order_price)
        {
            $order_prices[$order_price['type']] = $order_price['value'];
        }

        //Withdraw Gift certificate balance if it was used in this order
        modApiFunc('GiftCertificateApi','applyCurrentBalance', $order_prices['OrderTotal'], $order_id);

        $cart_currencies = modApiFunc("Checkout", "getCartCurrencies");
        $default_currency_code = modApiFunc("Checkout", "getCartDefaultCurrencyCode");
        foreach($cart_currencies as $currency)
        {
            $currency_code = $currency["code"];
            $currency_type = $currency["type"];

            if($currency_code == $default_currency_code)
            {
				$params = array(
									'order_id' => $order_id,
									'order_prices' => $order_prices,
									'currency_code' => $currency_code,
									'currency_type' => $currency_type
							   );
            }
            else
            {
	            $converted_order_prices = array();
	            foreach($order_prices as $key => $value)
	            {
                    $converted_order_prices[$key] = modApiFunc("Currency_Converter", "convert", $value, $default_currency_code, $currency_code);
	            }
				$params = array(
									'order_id' => $order_id,
									'order_prices' => $converted_order_prices,
									'currency_code' => $currency_code,
									'currency_type' => $currency_type
							   );
            }
			execQuery('INSERT_ORDER_PRICES', $params);
        }

        // add real order taxes
        foreach($tax_array["real_tax"] as $key => $tax)
        {
            reset($cart_currencies);
            foreach($cart_currencies as $currency)
            {
                $currency_code = $currency["code"];
                $currency_type = $currency["type"];

                if($default_currency_code == $currency_code)
                {
                    $value = $tax['value'];
                }
                else
                {
                    $value = modApiFunc("Currency_Converter", "convert", $tax['value'], $default_currency_code, $currency_code);
                }

                $params = array(
                                    'order_id' => $order_id,
                                    'taxes_type' => $tax["name"],
                                    'taxes_value' => $value,
                                    'currency_code' => $currency_code,
                                    'currency_type' => $currency_type,
                                    "is_included" => ($tax["is_included"] == "true") ? 1 : 0
                               );

                execQuery('INSERT_ORDER_TAXES', $params);
                $tax_array["real_tax"][$key]["new_id"][$currency_code] = $application->db->DB_Insert_Id();
            }
        }

        // add order tax display options
        foreach ($tax_array["tax_dops"] as $key => $tdo)
        {
            reset($cart_currencies);
            foreach($cart_currencies as $currency)
            {
                $currency_code = $currency["code"];
                $currency_type = $currency["type"];

                if($default_currency_code == $currency_code)
                {
                    $value = $tdo['value'];
                }
                else
                {
                    $value = modApiFunc("Currency_Converter", "convert", $tdo['value'], $default_currency_code, $currency_code);
                }

                $new_formula = '';
                $formula = explode(",", $tdo["formula"]);
                foreach($formula as $sign)
                {
                    $new_formula[] = $tax_array["real_tax"][$sign]["new_id"][$currency_code];
                }

                $order_tdo_formula = implode(',', $new_formula);

                $params = array(
                                    'order_id' => $order_id,
                                    'name' => $tdo["view"],
                                    'value' => $value,
                                    'currency_code' => $currency_code,
                                    'currency_type' => $currency_type,
                                    "formula" => $order_tdo_formula
                               );

                execQuery('INSERT_ORDER_TAX_DISPLAY_OPTIONS', $params);
            }
        }
    }


    function getPersonInfoVariantId($person_info_type_tag, $person_info_variant_tag)
    {
        global $application;
        loadCoreFile('UUIDUtils.php');

        $person_info_type_tag = UUIDUtils::cut_uuid_suffix($person_info_type_tag, "js");

        $params = array('person_info_variant_tag' => $person_info_variant_tag,
                        'person_info_type_tag' => $person_info_type_tag);
        $result = execQuery('SELECT_PERSON_INFO_VARIANT_ID', $params);

        if(sizeof($result) != 1)
        {
            //report error
            _fatal(__FILE__ . " : " . __LINE__ . '<br>sizeof($result) MUST BE equals to 1<br>$result = '.print_r($result, true).'<br>$person_info_type_tag = '.$person_info_type_tag.'<br>$person_info_variant_tag = '.$person_info_variant_tag.'<br>QUERY: SELECT_PERSON_INFO_VARIANT_ID');
        }
        else
        {
            return $result[0]['id'];
        }
    }

    /**
     * Returns a list of ids for the Person Info variant, sorted in order
     * fields sort.
     *
     * @author Oleg Vlasenko
     * @param integer $variant_id - the Id of the attribute variant.
     * @param bool $custom - if true only custom attributes will be returned
     * @return array the array of attribute ids
     */
    function getPersonInfoAttributeIdList($variant_id, $custom=STANDARD_ATTRIBUTES_ONLY)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];
        $a = $tables['person_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($s['attribute_id'], 'attribute_id');
        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        if ($custom == CUSTOM_ATTRIBUTES_ONLY)
        {
            $query->WhereAND();
            $query->WhereValue($a['is_custom'], DB_EQ, 1);
            $query->WhereAND();
            $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        }
        else if ($custom == STANDARD_ATTRIBUTES_ONLY)
        {
            $query->WhereAND();
            $query->WhereValue($a['is_custom'], DB_EQ, 0);
            $query->WhereAND();
            $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        }
        else if ($custom == ALL_ATTRIBUTES)
        {

        }
        $query->SelectOrder($s['sort']);

        $result = $application->db->getDB_Result($query);

        $ids = array();
        for ($i = 0; $i < sizeof($result); $i++)
        {
            $ids[] = $result[$i]['attribute_id'];
        }

        return $ids;
    }

    function getPersonInfoAttributeList($variant_id)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];
        $a = $tables['person_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($a['tag'], 'tag');
        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        $query->WhereAND();
        $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        $query->WhereAND();
        $query->WhereValue($a['is_custom'], DB_EQ, 0);
        $query->SelectOrder($s['sort']);
        $result = $application->db->getDB_Result($query);

        return $result;
    }

    function getPersonInfoCustomAttributeList($variant_id)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];
        $a = $tables['person_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($a['tag'], 'tag');
        $query->addSelectField($s['name'], 'name');
        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        $query->WhereAND();
        $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        $query->WhereAND();
        $query->WhereValue($a['is_custom'], DB_EQ, 1);
        $query->SelectOrder($s['sort']);
        $result = $application->db->getDB_Result($query);

        return $result;
    }

    function getPersonInfoTagList($person_type, $custom=false)
    {
        $prerequisiteValidationResults = $this->getPrerequisiteValidationResults($person_type);
        if ($custom == true)
        {
            $variant_tag_list = $this->getPersonInfoCustomAttributeList($prerequisiteValidationResults['id']);
        }
        else
        {
            $variant_tag_list = $this->getPersonInfoAttributeList($prerequisiteValidationResults['id']);
        }
        $direct_tags = array();
        foreach ($variant_tag_list as $tag_info)
        {
            $direct_tags['Local_'.$tag_info['tag']] = isset($prerequisiteValidationResults['validatedData'][$tag_info['tag']]) ? $prerequisiteValidationResults['validatedData'][$tag_info['tag']]['value'] : '';
            if ($tag_info['tag'] == 'Country')
            {
                $country_id = $direct_tags['Local_'.$tag_info['tag']];
                $direct_tags['Local_'.$tag_info['tag']] = modApiFunc("Location", "getCountry", $country_id);
            }
            if ($tag_info['tag'] == 'State')
            {
                if (isset($prerequisiteValidationResults['validatedData']['Statemenu']))
                    $state_id = $prerequisiteValidationResults['validatedData']['Statemenu']['value'];

                if(!empty($state_id))
                {
                    $direct_tags['Local_'.$tag_info['tag']] = modApiFunc("Location", "getState", $state_id);
                }
                else if (isset($prerequisiteValidationResults['validatedData']['Statetext']))
                {

                    $direct_tags['Local_'.$tag_info['tag']] = $prerequisiteValidationResults['validatedData']['Statetext']['value'];
                }
            }
        }
        return $direct_tags;
    }

    function getPersonInfoCustomTagList($person_type)
    {
        $prerequisiteValidationResults = $this->getPrerequisiteValidationResults($person_type);
        $variant_tag_list = $this->getPersonInfoCustomAttributeList($prerequisiteValidationResults['id']);
        $direct_tags = array();
        foreach ($variant_tag_list as $tag_info)
        {
            $tmp = array();
            $tmp['tag'] = $tag_info['tag'];
            $tmp['name'] = $tag_info['name'];
            $tmp['value'] = isset($prerequisiteValidationResults['validatedData'][$tag_info['tag']]) ? $prerequisiteValidationResults['validatedData'][$tag_info['tag']]['value'] : '';
            $direct_tags[] = $tmp;
        }
        return $direct_tags;
    }

    /**
     * Returns a list of fields for the Person Info attribute.
     *
     * @author Oleg Vlasenko
     * @param integer $variant_id - the Id of the attribute variant.
     * @param integer $attribute_id - the attribute Id .
     * @return array the array of values of attribute fields.
     */
    function getPersonInfoFieldsList($variant_id, $attribute_id)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($s['variant_id'], 'variant_id');
        $query->addSelectField($s['attribute_id'], 'attribute_id');

        $query->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $s['name'], $s['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_name'), 'name');

        $query->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $s['descr'], $s['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_descr'), 'descr');

        $query->addSelectField($s['unremovable'], 'unremovable');
        $query->addSelectField($s['visible'], 'visible');
        $query->addSelectField($s['required'], 'required');

        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        $query->WhereAnd();
        $query->WhereValue($s['attribute_id'], DB_EQ, $attribute_id);

        $result = $application->db->getDB_Result($query);

        if ($result != null)
        {

            $array = array(
                    'variant_id'    => $result[0]['variant_id']
                   ,'attribute_id'  => $result[0]['attribute_id']
                   ,'name'          => $result[0]['name']
                   ,'descr'         => $result[0]['descr']
                   ,'unremovable'   => $result[0]['unremovable']
                   ,'visible'       => $result[0]['visible']
                   ,'required'      => $result[0]['required']
                );

            return $array;
        }
        else
        {
            return null;
        }
    }

    /*static*/ function getAdditionalPrerequisiteName($prerequisite_type, $payment_module_id)
    {
        loadCoreFile('UUIDUtils.php');
        return $prerequisite_type . UUIDUtils::convert("minuses_and_capitals", "js", $payment_module_id);
    }

    /**
       2.1 Mask account numbers when displayed (the first six and last four
       digits are the maximum number of digits to be displayed).

       Visa U.S.A. Cardholder Information Security Program (CISP)
       Payment Application Best Practices
    */
    function ccnum2public($cc_num)
    {
        $len = _ml_strlen($cc_num);
        for($i = 0; $i< ($len - 4); $i++)
        {
            $cc_num[$i] = "*";
        }
        return $cc_num;
    }

    /**
       2.1 Mask account numbers when displayed (the first six and last four
       digits are the maximum number of digits to be displayed).
               CVV                      .

       Visa U.S.A. Cardholder Information Security Program (CISP)
       Payment Application Best Practices
    */
    function cvv2public($cvv)
    {
        $len = _ml_strlen($cvv);
        for($i = 0; $i< $len; $i++)
        {
            $cvv[$i] = "*";
        }
        return $cvv;
    }

    function get_public_view_of_secured_data($value, $person_attribute_id)
    {
        switch($person_attribute_id)
        {
            case 13 /* CreditCardNumber */:
                $value = $this->ccnum2public($value);
                break;

            case 14 /* CreditCardVerificationNumber */:
                $value = $this->cvv2public($value);
                break;

            default:
                break;
        }
        return $value;
    }

    function encryptOrderPersonAttribute($value, $key)
    {
        //: implement proper symmetric encryption.
        return modApiFunc("Crypto", "blowfish_encrypt", $value, $key);
    }

    function addOrderPersonAttribute($order_id,
                                     $person_info_variant_id,
                                     $attribute_id,
                                     $attribute_visible_name,
                                     $attribute_value,
                                     $attribute_description,
                                     $b_encrypted,
                                     $encrypted_secret_key,
                                     $rsa_public_key_asc_format)
    {
        $params = array(
                        'order_id'                  => $order_id,
                        'person_info_variant_id'    => $person_info_variant_id,
                        'attribute_id'              => $attribute_id,
                        'attribute_visible_name'    => $attribute_visible_name,
                        'attribute_value'           => $attribute_value,
                        'attribute_description'     => $attribute_description,
                        'b_encrypted'               => $b_encrypted,
                        'encrypted_secret_key'      => $encrypted_secret_key,
                        'rsa_public_key_asc_format' => $rsa_public_key_asc_format
                       );
        execQuery('INSERT_ORDER_PERSON_ATTRIBUTE',$params);
    }

    function appendCheckoutCZGETParameters($request)
    {
        $request->setKey   ( 'CHECKOUT_CZ_BLOWFISH_KEY', modApiFunc("Checkout", "getPerRequestVariable", "CHECKOUT_CZ_BLOWFISH_KEY"));
        return $request;
    }

    function getDecryptedCreditCardInfoPrerequisiteData($payment_module_id)
    {
         $prerequisite_name = Checkout::getAdditionalPrerequisiteName("creditCardInfo", $payment_module_id);
         if(isset($this->PrerequisitesValidationResults[$prerequisite_name]))
         {
            //Decrypt data in the session
            $this->decrypt_prerequisite_with_checkout_cz_blowfish_key($prerequisite_name);
            $decrypted_data = $this->getPrerequisitesValidationResults();
            $info = $decrypted_data[$prerequisite_name];
            //Encrypt data in the session
            $this->encrypt_prerequisite_with_checkout_cz_blowfish_key($prerequisite_name);

            //$person_info_variant_id = $this->getPersonInfoVariantId($prerequisite_name, $info['variant_tag']);

            return $info["validatedData"];
         }
         else
         {
             return false;
         }
    }

    /**
     * Adds a Custoner (List Person) to this order.
     * It is used, for example, to save unregistered customers.
     * A separate record is not created in the table persons.
     *
     * @
     * @param
     * @return
     */
    function addOrderPerson($order_id)
    {
        global $application;
        $tables =  $this->getTables();

        $ptiv = $tables["person_to_info_variants"]['columns'];
        $opd = $tables["order_person_data"]['columns'];

        $data = $this->getPrerequisitesValidationResults();
        $payment_module_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
        $required_cc_info_prerequisite_name = Checkout::getAdditionalPrerequisiteName("creditCardInfo", $payment_module_id);
        $required_bank_account_info_prerequisite_name = Checkout::getAdditionalPrerequisiteName("bankAccountInfo", $payment_module_id);


        loadCoreFile('db_multiple_insert.php');
        $query = new DB_Multiple_Insert('order_person_data');
        $query->setInsertFields(array('order_id', 'person_info_variant_id', 'person_attribute_id', 'order_person_data_name', 'order_person_data_value', 'order_person_data_description', 'order_person_data_b_encrypted', 'order_person_data_encrypted_secret_key', 'order_person_data_rsa_public_key_asc_format'));

        foreach ($data as $prerequisite_key => $info)
        {
            if (_ml_strpos($prerequisite_key, "Module") || $prerequisite_key == 'subscriptionTopics')
            {
                //"shippingModuleAndMethod","paymentModule"
            }
            else if(_ml_strpos($prerequisite_key, "creditCardInfo") !== FALSE)
            {
                /**
                 * Define, if this creditCardInfo instance matches
                 * the selected payment module.
                 * If it does, then write it to the DB. Make two instances:
                 * 1. not encrypted obfuscaed one
                 * 2. not obfuscated encrypted one.
                 */

                 if($prerequisite_key == $required_cc_info_prerequisite_name)
                 {
                    /*
                       Ask the payment module, if it has to store Credit Card Info
                       in the database, or it won't be used after creating the order.
                     */
                    $mInfo = Checkout::getPaymentModuleInfo($payment_module_id);
                    $mmObj = &$application->getInstance('Modules_Manager');
                    $mmObj->includeAPIFileOnce($mInfo["APIClassName"]);
                    /* This condition can be checked only after loading */
                    if(is_callable(array($mInfo["APIClassName"],"storeCreditCardInfoInDB")))
                    {
                        $b_storeCreditCardInfoInDB = call_user_func(array($mInfo["APIClassName"], 'storeCreditCardInfoInDB'));
                        if($b_storeCreditCardInfoInDB === true)
                        {
                        	$symmetric_secret_key = modApiFunc("Crypto", "blowfish_gen_blowfish_key");
                        	$rsa_public_key = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInCryptRSAFormat");
                        	$rsa_public_key_asc_format = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInASCFormat");

                        	$rsa_obj = new Crypt_RSA;
                        	$encrypted_symmetric_secret_key = $rsa_obj->encrypt($symmetric_secret_key, $rsa_public_key);

                            //Decrypt data in the session
                            $this->decrypt_prerequisite_with_checkout_cz_blowfish_key($prerequisite_key);
                            $decrypted_data = $this->getPrerequisitesValidationResults();
                            $info = $decrypted_data[$prerequisite_key];
                            //Encrypt data in the session
                            $this->encrypt_prerequisite_with_checkout_cz_blowfish_key($prerequisite_key);

                            $person_info_variant_id = $this->getPersonInfoVariantId($prerequisite_key, $info['variant_tag']);
                            //Encrypt data in the session
                            foreach ($info["validatedData"] as $attribute_key => $validatedData)
                            {
                                $attribute_id = $validatedData["id"];
                                $attribute_visible_name = $validatedData["attribute_visible_name"];
                                if($attribute_key == "CreditCardType")
                                {
                                    $cc_type_names = modApiFunc("Configuration", "getCreditCardSettings");
                                    $attribute_value = $cc_type_names[$validatedData["value"]]["name"];
                                }
                                else
                                {
                                    $attribute_value = $validatedData["value"];
                                }
                                $attribute_description = $validatedData["attribute_description"];
                                // add the not encrypted obfuscated value
                                $b_encrypted = "0";
                                $i_arr = array(
                                    'order_id'                                    => $order_id,
                                    'person_info_variant_id'                      => $person_info_variant_id,
                                    'person_attribute_id'                         => $attribute_id,
                                    'order_person_data_name'                      => $attribute_visible_name,
                                    'order_person_data_value'                     => $this->get_public_view_of_secured_data($attribute_value, $attribute_id),
                                    'order_person_data_description'               => $attribute_description,
                                    'order_person_data_b_encrypted'               => $b_encrypted,
                                    'order_person_data_encrypted_secret_key'      => $encrypted_symmetric_secret_key,
                                    'order_person_data_rsa_public_key_asc_format' => $rsa_public_key_asc_format
                                );
                                $query->addInsertValuesArray($i_arr);

                                // add the not obfuscated encrypted value
                                $i_arr['order_person_data_b_encrypted'] = "1";
                                $i_arr['order_person_data_value'] = base64_encode($this->encryptOrderPersonAttribute($attribute_value, $symmetric_secret_key));
                                $query->addInsertValuesArray($i_arr);
                            }
                        }
                    }
                }
            }
            else
            {
                //                    ,                                                 ,
                //                ,                                  ,
                //                              .
                if(_ml_strpos($prerequisite_key, "bankAccountInfo") !== FALSE && $required_bank_account_info_prerequisite_name != $prerequisite_key)
                {
                    //BankAccountInfo,
                }
                else
                {
                    $person_info_variant_id = $this->getPersonInfoVariantId($prerequisite_key, $info['variant_tag']);
                    // add to the table order_person_data
                    foreach ($info["validatedData"] as $attribute_key => $validatedData)
                    {
                        if($attribute_key=="Statemenu" || $attribute_key=="Statetext")
                        {
                            //An attribute "state" from the DB matches two attributes
                            // state_menu and state_text in the session. They are mutually exclussive in meaning:
                            // state_menu is the ID of the record about the state in the DB, i.e.
                            // a number. sate_text is a state name, inputted manually by a customer.
                            // It is inputted only if the customer selected a country, which has no
                            // defined states in the DB. As for now (Dec 2005) in the DB
                            // in the field "state" is saved only one of the values, which is not empty.
                            // Either sate_menu, or state_text.
                            if($attribute_key == "Statetext")
                            {
                                continue;
                            }
                            if($attribute_key == "Statemenu")
                            {
                                $state_menu_value = $info["validatedData"]["Statemenu"]["value"];
                                $state_text_value = $info["validatedData"]["Statetext"]["value"];
                                //FIMXE: check if both values are empty.
                                $value = empty($state_menu_value) ? $state_text_value : $state_menu_value;

                                //: analyze the conversion "1 atribute" <=> "2 attributes" for
                                // "state". As for now(Dec 2005) data on DB-attribute "state"
                                // is saved to the session-attribute "Statemenu"

                                //Write a state name, but not the id
                                //: depends on another attribute value: Country
                                if(is_numeric($value))
                                {
                                    //: can validatedData contain a nested
                                    //  structure with the same name validatedData?
                                    $states = modApiFunc("Location", "getStates", $info["validatedData"]["Country"]["value"]);
                                    $value = $states[$value];
                                };
                                // add to the table order_person_data
                                $i_arr = array(
                                    'order_id'                                    => $order_id,
                                    'person_info_variant_id'                      => $person_info_variant_id,
                                    'person_attribute_id'                         => $validatedData["id"],
                                    'order_person_data_name'                      => $validatedData["attribute_visible_name"],
                                    'order_person_data_value'                     => $value,
                                    'order_person_data_description'               => $validatedData["attribute_description"],
                                    'order_person_data_b_encrypted'               => "0",
                                    'order_person_data_encrypted_secret_key'      => "",
                                    'order_person_data_rsa_public_key_asc_format' => ""
                                );
                                $query->addInsertValuesArray($i_arr);
                            }
                        }
                        else
                        {
                            //Write a name for the country rather than the id
                            if($attribute_key == "Country")
                            {
                                $countries = modApiFunc("Location", "getCountries");
                                $value = empty($validatedData["value"]) ? "" : $countries[$validatedData["value"]];
                            }
                            else
                            {
                                $value = $validatedData["value"];
                            }
                            $i_arr = array(
                                'order_id'                                    => $order_id,
// what is the id (becomes a type_id in the DB)? Into the prerequisite
                                'person_info_variant_id'                      => $person_info_variant_id,
                                'person_attribute_id'                         => $validatedData["id"],
                                'order_person_data_name'                      => $validatedData["attribute_visible_name"],
                                'order_person_data_value'                     => $value,
                                'order_person_data_description'               => $validatedData["attribute_description"],
                                'order_person_data_b_encrypted'               => "0",
                                'order_person_data_encrypted_secret_key'      => "",
                                'order_person_data_rsa_public_key_asc_format' => ""
                            );
                            $query->addInsertValuesArray($i_arr);
                        }
                    }
                }
            }
        }
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    /**
     * Adds a record to the change backlog.
     *
     * @param $history - the array of ActionMessage objects
     */
    function addOrderHistory($order_id, $history_text)
    {
        global $application;

//        if (!is_array($history) || count($history) == 0)
//        {
//            return;
//        }
        $tables = $this->getTables();

        //$text = "";
        //foreach ($history as $record)
        //{
        //    $text .= $messageResources->getMessage($record) . "\n";
        //}

        $on = $tables['order_notes']['columns'];
        $query = new DB_Insert('order_notes');
        $query->addInsertValue($order_id, $on['order_id']);
        $query->addInsertValue(date('Y-m-d H:i:s', time()/*getServerTime()*/), $on['date']);
        $query->addInsertValue($application->get_microtime(), $on['microtime']);
        $query->addInsertValue($history_text, $on['content']);
        $query->addInsertValue('history', $on['type']);
        $application->db->getDB_Result($query);
     }

    /**
     * Sends a report to each of the recipient.
     */
    function asc_send($subj, $body, $to_address)
    {
        loadCoreFile('ascHtmlMimeMail.php');
        $mail = new ascHtmlMimeMail();
        $mail->setText($body);
        $mail->setSubject($subj);
        $mail->setFrom($to_address);
        $mail->send(array($to_address));
    }

    /**
     * Creates an order in the database.
     *
     * @author Alexander Girin
     * @ finish the function
     * @param
     * @return
     */
    function createOrderInDB()
    {
        global $application;

        $payment_module_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
        if($payment_module_id === NULL)
        {
            //Use "AllInactive" Payment module.
            $payment_module_id = Checkout::getAllInactiveModuleId("payment");
        }
        $payment_module_info = Checkout::getPaymentModuleInfo($payment_module_id);
        $payment_method_name = $payment_module_info["Name"];

        $shipping_module_id = modApiFunc("Checkout", "getChosenShippingModuleIdCZ");
        if($shipping_module_id === NULL)
        {
            //Use "AllInactive" Shipping module.
            $shipping_module_id = Checkout::getAllInactiveModuleId("shipping");
            $shipping_module_info = Checkout::getShippingModuleInfo($shipping_module_id);
            $shipping_method_name = $shipping_module_info['Name'];
        }
        else
        {
            $shipping_method_id = modApiFunc("Checkout", "getChosenShippingMethodIdCZ");
            $shipping_method_info = modApiFunc("Checkout", "getShippingMethodInfo", $shipping_module_id, $shipping_method_id);
            $shipping_method_name = $shipping_method_info['method_name'];
        }

        $main_store_currency = modApiFunc("Localization", "getMainStoreCurrency");
        $order_subtotal = modApiFunc("Checkout", "getOrderPrice", "Subtotal", $main_store_currency);

        $subtotal_global_discount = modApiFunc("Checkout", "getOrderPrice", "SubtotalGlobalDiscount", $main_store_currency);

        $subtotal_promo_code_discount = modApiFunc("Checkout", "getOrderPrice", "SubtotalPromoCodeDiscount", $main_store_currency);

        $quantity_discount = modApiFunc("Checkout", "getOrderPrice", "QuantityDiscount", $main_store_currency);

        //: Total, Shipping and Tax MUST be set. If "Total" and "Tax" depend
        // not only on shipping parameters (checked in
        //  the beginning of addOrder(), then those additional dependencies should be
        //  checked too.
        $order_total = modApiFunc("Checkout", "getOrderPrice", "Total", $main_store_currency);

        $order_total_to_pay = modApiFunc("Checkout", "getOrderPrice", "TotalToPay", $main_store_currency);

        $order_shipping_method_cost = modApiFunc("Checkout", "getOrderPrice", "ShippingMethodCost", $main_store_currency);

        $order_shipping_cost = modApiFunc("Checkout", "getOrderPrice", "TotalShippingAndHandlingCost", $main_store_currency);

        $order_order_tax_total_cost = modApiFunc("Checkout", "getOrderPrice", "Tax_NoExempts", $main_store_currency);

        $order_not_included_tax_total = modApiFunc("Checkout", "getOrderPrice", "NotIncludedTax_NoExempts", $main_store_currency);

        $order_discounted_subtotal_cost = modApiFunc("Checkout", "getOrderPrice", "DiscountedSubtotal", $main_store_currency);

        $payment_processor_order_id ="";
        $display_prices_with_included_taxes = (modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES") == DB_TRUE) ? 1 : 0;

        if (modApiFunc("Session","is_set","AffiliateID")) //Affiliate id found in session
        {
            $aff_id = modApiFunc("Session","get","AffiliateID");

            if (modApiFunc("Settings","getParamValue","AFFILIATE_SETTINGS","FIRST_ORDER_ONLY") == "YES") // Only first affiliate order will be tracked
            {
                modApiFunc("Session","un_set","AffiliateID");
            }
        }
        else
            $aff_id = "";

        $oid = modApiFunc("Checkout", "addOrder", array('status_id' => 1, // New Order
                                                        'payment_status_id' => 1, // Waiting
                                                        'payment_method' => $payment_method_name,
                                                        'payment_module_id' => $payment_module_id,
                                                        'payment_method_detail' => "NULL",
                                                        'payment_processor_order_id' => $payment_processor_order_id,//"NULL", //$payment_module_id,
                                                        'shipping_method' => $shipping_method_name,
                                                        'track_id' => "",//"NULL", //?
                                                        'person_id' => '0',//"NULL"//Checkout
                                                        'affiliate_id' => $aff_id, // Affiliate id
                                                        //without registration, so:
                                                        // NO "registered person" id.
                                                        "included_tax" => $display_prices_with_included_taxes
                                                       )
                         );

        $order_prices = array(
                array('type' => "OrderTotalToPay",
                      'value' => $order_total_to_pay),
                array('type' => "OrderTotalPrepaidByGC",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "TotalPrepaidByGC", $main_store_currency)),
                array('type' => "OrderTotal",
                      'value' => $order_total),
                array('type' => "OrderSubtotal",
                      'value' => $order_subtotal),
                // General Shipping Settings
                array('type' => "MinimumShippingCost",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "MinimumShippingCost", $main_store_currency)),
                array('type' => "FreeShippingForOrdersOver",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "FreeShippingForOrdersOver", $main_store_currency)),
                array('type' => "FreeHandlingForOrdersOver",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "FreeHandlingForOrdersOver", $main_store_currency)),
                // Shipping prices
                array('type' => "PerItemShippingCostSum",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "PerItemShippingCostSum", $main_store_currency)),
                array('type' => "PerOrderShippingFee",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "PerOrderShippingFee", $main_store_currency)),
                array('type' => "ShippingMethodCost",
                      'value' => $order_shipping_method_cost),
                array('type' => "TotalShippingCharge",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "TotalShippingCharge", $main_store_currency)),
                array('type' => "PerOrderPaymentModuleShippingFee",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "PerOrderPaymentModuleShippingFee", $main_store_currency)),
                // handling prices
                array('type' => "PerItemHandlingCostSum",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "PerItemHandlingCostSum", $main_store_currency)),
                array('type' => "PerOrderHandlingFee",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "PerOrderHandlingFee", $main_store_currency)),
                array('type' => "TotalHandlingCharge",
                      'value' => modApiFunc("Checkout", "getOrderPrice", "TotalHandlingCharge", $main_store_currency)),
                // total shipping and handling
                array('type' => "TotalShippingAndHandlingCost",
                      'value' => $order_shipping_cost),
                // total tax
                array('type' => "OrderTaxTotal",
                      'value' => $order_order_tax_total_cost),
                array('type' => "SubtotalGlobalDiscount",
                      'value' => $subtotal_global_discount),
                array('type' => "SubtotalPromoCodeDiscount",
                      'value' => $subtotal_promo_code_discount),
                array('type' => "QuantityDiscount",
                      'value' => $quantity_discount),
                array('type' => "DiscountedSubtotal",
                      'value' => $order_discounted_subtotal_cost),
                array('type' => "OrderNotIncludedTaxTotal",
                      'value' => $order_not_included_tax_total)
                );

        $tax_real_array = array();
        $tax_dops_array = array();
        $tax_array = modApiFunc("Taxes", "getTax");
        $tax_names = execQuery("SELECT_TAX_LIST", NULL);
        $tax_dops = execQuery("SELECT_TAX_DISPLAY_OPTIONS_LIST", NULL);

        foreach ($tax_array['TaxSubtotalAmount'] as $key => $value)
        {
            foreach ($tax_names as $tn)
            {
                if ($key == $tn["id"])
                {
                    $tax_real_array[$key]["id"] = $tn["id"];
                    $tax_real_array[$key]["value"] = $value;
                    $tax_real_array[$key]["name"] = $tn["name"];
                    $tax_real_array[$key]["is_included"] = $tn["included_into_price"];
                }
            }
        }
        foreach ($tax_array['TaxSubtotalAmountView'] as $taxView)
        {
            $order_prices[] = array(
                                    'type' => "Taxes['".$taxView['view']."']",
                                    'value' => $taxView['value'],
                                    "is_included" => ($taxView["is_included"] == "true") ? 1 : 0
                                    );
            foreach ($tax_dops as $tdo)
            {
                if ($taxView["id"] == $tdo["Id"])
                {
                    $tdo["Formula"] = preg_replace("/[{}]/", '', $tdo["Formula"]);
                    $tdo["Formula"] = str_replace("+", ',', $tdo["Formula"]);
                    $tax_dops_array[$tdo["Id"]] = $taxView;
                    $tax_dops_array[$tdo["Id"]]["formula"] = $tdo["Formula"];
                }
            }
        }

        $tax_array = array(
                 "real_tax" => $tax_real_array
                ,"tax_dops" => $tax_dops_array
            );

        modApiFunc("Checkout", "addOrderPrices", $oid, $order_prices, $tax_array);
        //add product info
        modApiFunc("Checkout", "addOrderProducts", $oid, modApiFunc("Cart", "getCartContent"));

        //If the customer is not registered
        if(true)
        {
            modApiFunc("Checkout", "addOrderPerson", $oid);
        }
        else
        {
        }

        modApiFunc("Checkout", "setLastPlacedOrderID", $oid);
        modApiFunc("Checkout", "saveState");

       /*
          Clear all encrypted blowfish-data       , and prerequisitesValidationResults as well. At the current moment
          it can be stored in the session and in the object this. The function, that deletes such data is performed
          in the session. It was used to delete data, when the blowfish key is lost. The key is passed in to the Checkout CZ
          as a GET/POST parameter.
          NOTE! The call "saveState" is required! As it is executed (2007 03 14) a string above, it is commented here.
        */
        modApiFunc("Checkout", "saveState");
        Checkout::clear_all_checkout_cz_blowfish_encoded_data();
        //modApiFunc("Checkout", "loadState");

        if (isset($_SERVER['REMOTE_ADDR']))
        {
            $h = str_replace('{IP}', $_SERVER['REMOTE_ADDR'], getMsg('CHCKT','ORDER_CREATED_FROM_IP'));
            modApiFunc('Checkout','addOrderHistory',$oid, $h);
        }

        // throw event
        modApiFunc('EventsManager','throwEvent','OrderCreated',$oid);

        // timeline
        modApiFunc('Checkout', 'addOrderCreationToTimeline', $oid);

        modApiFunc("GiftCertificateApi","createGC", $oid);

        // if order_total_to_pay == 0 change order status to Fully Paid
        if ($order_total_to_pay == 0)
        {
            modApiFunc("Checkout", "UpdatePaymentStatusInDB", $oid, 2, '');
        }
        return $oid;
    }

    function addOrderCreationToTimeline($oid)
    {
        if (modApiFunc('Settings','getParamValue','TIMELINE','LOG_ORDER_CREATION') === 'NO')
        {
            return;
        }

        $tl_header = getMsg('CHCKT', 'TL_ORDER_CREATED_HEADER');
        $tl_header = str_replace('{OID}', $oid, $tl_header);
        $tl_type = getMsg('CHCKT', 'TL_ORDER_CREATED_TYPE');
        modApiFunc('Timeline', 'addLog', $tl_type, $tl_header, '');
    }

    /*
     *                     ,                                     ,                .
     *            order_id.
     */
    function createOrderInDBAndUpdateEnvironment()
    {
        //Create order
        $order_id = modApiFunc("Checkout", "createOrderInDB");
        //                .
        //
        modApiFunc("Cart", "removeAllFromCart");
        return $order_id;
    }

     function UpdatePaymentStatusInDB($order_id, $payment_status_id, $comment)
     {
        global $application;
        $statusChanged = array("payment_status" => array());

        $tables = $this->getTables();

        $o = $tables['orders']['columns'];
        $on = $tables['order_notes']['columns'];


        #  get current order info
        $order = $this->getOrderInfo($order_id, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id));
        #     check if it is necessary to update status
        $update_payment_status_id = $payment_status_id != $order['PaymentStatusId'];

        if($update_payment_status_id)
        {
            $statusChanged["payment_status"][$order_id] = array("old_status" => $order['PaymentStatusId'], "new_status" => $payment_status_id);
            $statusChanged["order_status"][$order_id] = null;
        }

        $db_update = new DB_Update('orders');
        $db_update->addUpdateValue($o['payment_status_id'], $payment_status_id);
        $db_update->WhereValue($o['id'], DB_EQ, $order_id);
        $application->db->PrepareSQL($db_update);
        $application->db->DB_Exec();

        #    add comment, if it is specified
        if (!empty($comment))
        {
            $this->addOrderHistory($order_id, $comment);
        }

        if($update_payment_status_id)
        {
            modApiFunc('EventsManager','throwEvent','OrdersWereUpdated',$statusChanged);
        }

        //send email notifications if needed
        //SQL QUERY: INSERT INTO events_manager VALUES (164,'OrderStatusUpdated','Notifications','OnOrderStatusUpdated',NULL,100);
        modApiFunc('EventsManager','throwEvent','OrderStatusUpdated',$statusChanged);

        return $statusChanged;
     }

    /**
     * Returns a list of Checkout-related store blocks, which were viewed on
     * the privious View, and data which is expected to be in GET/POST.
     * Each block can appear by outputting some hidden field to the View.
     * For example, SubmitedCheckoutStoreBlocksList['shipping-info'].
     */
    function getLastViewSubmitedCheckoutStoreBlocksList()
    {
        global $application;
        $request = $application->getInstance('Request');
        if($request->getValueByKey('SubmitedCheckoutStoreBlocksList') != NULL)
        {
            return $request->getValueByKey('SubmitedCheckoutStoreBlocksList');
        }
        else
        {
            return array();
        }
    }

    /**
     * Returns prerequisite contents (with all field values) for the current
     * step.
     *
     * @param $step_id - a number of the current step.
     *
     * @author Alexandr Girin
     * @
     * @param
     * @return
     */
    function getPrerequisitesValidationResultsForGivenStep($step_id)
    {
        $prerequisites = modApiFunc('Checkout', 'getPrerequisitesListForStep', $step_id);
        $PrerequisitesValidationResultsForCurrentStep = array();
        foreach($prerequisites as $prerequisite)
        {
            $PrerequisiteValidationResults = modApiFunc('Checkout', 'getPrerequisiteValidationResults', $prerequisite);
            $PrerequisitesValidationResultsForCurrentStep[$prerequisite] = $PrerequisiteValidationResults;
        }
        return $PrerequisitesValidationResultsForCurrentStep;
    }


    function redirectBackBecauseOfErrors()
    {
    }

    function getStepIDtoRedirectToAfterPrerequisitesValidationErrors($target_step_id)
    {
        $PrerequisiteErrorToStepIDTable = modApiFunc('Checkout', 'getPrerequisiteErrorToStepIDTable');
        $PrerequisitesValidationResults = $this->getPrerequisitesValidationResultsForGivenStep($target_step_id);

        //                               ,                                                step_id,
        //                                      .                   - CreditCardInfo             .
        //                                (                           Checkout),
        //                                                                  .
        //  (getStepIDtoRedirectToAfterPrerequisitesValidationErrors) -          ,
        //                       -                                         .           -
        //                  .

        //                            .          ,                         ("isMet" = false)
        //            ,                                      checkout                      .
        $min_step_id_to_redirect_to = $target_step_id;
        foreach($PrerequisitesValidationResults as $prerequisite_name => $validation_results_array)
        {
            //                            CheckoutFormEditor' ,                              .
            //         ,     PersonInfoTypeTag             PrerequisiteName.
            if($validation_results_array['isMet'] == false &&
               Checkout::arePersonInfoTypesActive(array($prerequisite_name)) === true)
            {
                if($PrerequisiteErrorToStepIDTable[$prerequisite_name] < $min_step_id_to_redirect_to)
                {
                    $min_step_id_to_redirect_to = $PrerequisiteErrorToStepIDTable[$prerequisite_name];
                }
            }
        }

        return $min_step_id_to_redirect_to;
    }

    /**
    * Common sub algo for two checkout actions:
    * setCurrentStepID() and
    * updateOrderStatusID()
    *
    * Perform checks: is it possible to set new checkout step id.
    * If possible (prerequisites are met) - set it.
    */
    function ProcessNewStepID($step_id)
    {
        if ($step_id != NULL)
        {
            //check if prerequisites are executed

            // get a list of prerequisites
            $prerequisites = modApiFunc('Checkout', 'getPrerequisitesListForStep', $step_id);

            //Overview the list of prerequisites and do the following for each of them:
            //    If it hasn't been executed yet, then see the compatability table
            //         "prerequisite => store block",
            //         check if data came from this store block.
            //             If it did, call validation function
            //             Otherwise, the prerequisite is not executed.

            foreach($prerequisites as $prerequisite)
            {
                $PrerequisitePreviousValidationResults = modApiFunc('Checkout', 'getPrerequisiteValidationResults', $prerequisite);
                //: Perhaps there is no need to double check data that just came
                //  (if, for example, this data once were checked successfully).

                //Get a name of store block matching the given prerequisite.
                $PrerequisiteStoreBlock = modApiFunc('Checkout', 'getPrerequisiteStoreBlock', $prerequisite);
                //check, if some GET/POST data came from this block, i.e. if it was outputted
                // on the previous View.
                $LastViewSubmitedCheckoutStoreBlocksList = $this->getLastViewSubmitedCheckoutStoreBlocksList();

                if(($PrerequisitePreviousValidationResults['isMet'] == true) && !isset($LastViewSubmitedCheckoutStoreBlocksList[$PrerequisiteStoreBlock]))
                {
                }
                else
                {
                    if(isset($LastViewSubmitedCheckoutStoreBlocksList[$PrerequisiteStoreBlock]))
                    {
                        //Validate data, that came from Store block, and if it is
                        // done successfully, specify the prerequisite as a executed
                        // condition.
                        ////$validationFunctionName = modApiFunc('Checkout', 'getPrerequisiteValidationFunction', $prerequisite);

                        /**
                         * Call the validation function.
                         * Validation results are saved in the session.
                         */

                        modApiFunc('Checkout', 'validateInputForPrerequisite', $prerequisite);
                    }
                    else
                    {
                        //Example: it specified in the configuration file that
                        // customer info was on the previous step, but in the POST data
                        // it doesn't exist. The case, when a block label exists
                        // but data doesn't (e.g. there are available shipping methods,
                        // but the customer didn't choose any), is described in
                        // validateInputForPrerequisite().

                        //The data didn't come (or Store block didn't specify, that its form data
                        // was filled out and sent). Prerequisite wasn't executed.
                        // Specify it as not executed. After checking other prerequisites
                        // redirect the customer to the previous (or any other,
                        // specified in the list of the pages, to be redirected if an error occurs)
                        // page.
                        // output a detailed error message: POST/GET data of such-and-such
                        // store block.
                        //: use Checkout API function to get "visibleName" and set Error data.

                        $this->PrerequisitesValidationResults[$prerequisite]["isMet"] = false;

                        $this->PrerequisitesValidationResults[$prerequisite]["error_code"] = 'CHECKOUT_ERR_004_NO_POST_DATA_FOR_PREREQUISITE';
                        $this->PrerequisitesValidationResults[$prerequisite]["error_message_parameters"] = array($this->PrerequisitesValidationResults[$prerequisite]["visibleName"], $prerequisite);
                        //break;
                    }
                }
            }
            //End check if prerequisites are executed

            //Perform required additional actions
            //END Perform required additional actions

            //If not all prerequisites are met, redirect to the page,
            //specified as an error for this step.
            $all_prerequisites_are_met = true;
            $all_active_in_checkout_editor_prerequisites_are_met = true;
            foreach($prerequisites as $prerequisite)
            {
                $PrerequisiteValidationResults = modApiFunc('Checkout', 'getPrerequisiteValidationResults', $prerequisite);
                if($PrerequisiteValidationResults['isMet'] == false)
                {
                    $all_prerequisites_are_met = false;
                    //                               (turned off)   Checkout Form Editor'
                    //
                    //        . isMet                   false!
                    if(Checkout::arePersonInfoTypesActive(array($prerequisite)) === true)
                    {
                        //:                                               .
                        //                        ,    -   CreditCardInfo   BankAccountInfo
                        //                             - UIN'                     .
                        //                                   ,                ,
                        //                     .
                        //                 if'                           :
                        //     _             = person_info_type_tag
                        $all_active_in_checkout_editor_prerequisites_are_met = false;
                    }
                }
            }

            if($all_active_in_checkout_editor_prerequisites_are_met === true)
            {
                //Define a number of the step, where can be the checkout process.
                $retval = modApiFunc('Checkout', 'setCurrentStepID', $step_id);
                //END Define a number of the step, where can be the checkout process.
            }
            else
            {
                //Redirect to the page, specified as an error for this step.

                // : fill the error array
                //Fill the error array. Only the block list, where an error occurred
                // and an error message, which is returned by the validation function.

                //$this->redirectBackBecauseOfErrors();
                $step_id_to_redirect_to = modApiFunc('Checkout', 'getStepIDtoRedirectToAfterPrerequisitesValidationErrors', $step_id);

                $retval = modApiFunc('Checkout', 'setCurrentStepID', $step_id_to_redirect_to);
            }
        }
    }

    function isShippingModulesListEmpty($groups = null)
    {
        /*
        global $application;

        $SelectedShippingModules = $this->getSelectedModules("shipping");

        $sm_list = Checkout::getInstalledAndActiveModulesListData("shipping", $groups);

        $items = array();

        foreach ($sm_list as $sm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($sm_item->name);

            $smInfo = modApiFunc($name, "getInfo");

            // : check if function exists
            $module_uid = $smInfo['GlobalUniqueShippingModuleID'];

            if (array_key_exists($module_uid, $SelectedShippingModules) == true)
            {
               /* Check, if for the current address even one method works.
                  Otherwise output a special template "No shipping method
                  available for the inputted address".
                  What should be outputted, if the address hasn't been inputted yet?
                */
                /*
                $methods_info_list = modApiFunc($smInfo['APIClassName'], "getShippingMethods", "AVAILABLE", true);
                if(!empty($methods_info_list))
                {
                    return false;
                }
            }
        }
        */

        $formatted_cart = modApiFunc("Shipping_Cost_Calculator","formatCart",modApiFunc("Cart","getCartContent"));
        modApiFunc("Shipping_Cost_Calculator","setShippingInfo",modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo"));
        modApiFunc("Shipping_Cost_Calculator","setCart",$formatted_cart);
        $_modules_and_methods = modApiFunc("Shipping_Cost_Calculator","calculateShippingCost");

        if(!empty($_modules_and_methods) and !isset($_modules_and_methods[modApiFunc("Checkout","getAllInactiveModuleClassAPIName","shipping")]))
            return false;

        return true;
    }

    function isPaymentModulesListEmpty($groups = null)
    {
        global $application;

        $SelectedPaymentModules = $this->getSelectedModules("payment");

        $pm_list = Checkout::getInstalledAndActiveModulesListData("payment", $groups);

        $items = array();
        $new_selected_module_sort_order = 0;
        foreach ($pm_list as $pm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($pm_item->name);

            $pmInfo = modApiFunc($name, "getInfo");

            // : check if function exists
            $module_uid = $pmInfo['GlobalUniquePaymentModuleID'];
            if (array_key_exists($module_uid, $SelectedPaymentModules) == true)
            {
                return false;
            }
        }
        return true;
    }

    function getNotNeedShippingModuleID()
    {
        return "5BED3FCA-82F4-4A5D-A578-D6022994BC2F";
    }

    function getNotNeedShippingMethodID()
    {
        return 1;
    }

    function getNotNeedShippingMethodName()
    {
        return "Shipping is not needed"; //: move to resources?
    }

    //                                "OrderCreated"
    function getPaymentMethodText($pm_uid)
    {
        global $application;
        $pm_info = modApiFunc("Checkout", "getPaymentModuleInfo", $pm_uid);
        $mmObj = &$application->getInstance('Modules_Manager');
        /* Load required file */
        $mmObj->includeAPIFileOnce($pm_info['APIClassName']);
        /* This condition can be checked only after loading */
        if(is_callable(array($pm_info['APIClassName'],"getPaymentMethodText")))
        {
            $text = modApiFunc($pm_info['APIClassName'], "getPaymentMethodText");
        }
        else
        {
            $text = "";
        }
        return $text;
    }

    /**
     *                             .         , ShippingInfo['State'].                 ,
     *                                               .         ,                                . .
     *          $options                                                        .         , "TEXT",
     * "ID"          .
     */
    function getPrAttr($variant_tag, $attribute_tag, $options = array())
    {
    	$pvr = &$this->PrerequisitesValidationResults;
    	if(!isset($pvr[$variant_tag]))
    	{
    		return NULL;
    	}
    	else
    	{
    		if(!isset($pvr[$variant_tag]['validatedData'][$attribute_tag]))
    		{
    			return NULL;
    		}
    		else
    		{
    			return $pvr[$variant_tag]['validatedData'][$attribute_tag]['value'];
    		}
    	}
    }

    /**
     * If prerequisites has not been completed, it returns true.
     * Otherwise it outputes checked, if the id of module prerequisites is equal
     * to this module id. Otherwise it returns false.
     *
     * @return string
     */
    function isPaymentModuleChecked($pm_uuid)
    {
        $value = false;
        $paymentPrerquisites = modApiFunc("Checkout", "getPrerequisiteValidationResults", "paymentModule");
        if ($paymentPrerquisites["isMet"] == true)
        {
            $preselectedModuleId = $paymentPrerquisites["validatedData"]["method_code"]["value"];
            if (!empty($preselectedModuleId))
            {
                if ($preselectedModuleId == $pm_uuid)
                {
                    $value = true;
                }
            }
        }
        else
        {
            /**
             * If the method has not been chosen yet and this method is the
             * first in the list, tick it off.
             */
            $checkedPaymentMethod = modApiFunc("Checkout", "getPerRequestVariable", "checkedPaymentMethod", "");
            if(empty($checkedPaymentMethod))
            {
                modApiFunc("Checkout", "setPerRequestVariable", "checkedPaymentMethod", "defined");
                $value = true;
            }
            else
            {
                $value = false;
            }
        }
        return $value;
    }

    function update_pm_sm_currency_settings($module_id, $module_settings)
    {
        global $application;
        loadCoreFile('db_multiple_insert.php');
        //The list of modules,which should become selected.

        $tables = Checkout::getTables();

        //                                                     pm_sm_accepted_currencies
        //    pm_sm_currency_acceptance_rules.
        $cpsac = $tables['checkout_pm_sm_accepted_currencies']['columns'];
        $query = new DB_Delete('checkout_pm_sm_accepted_currencies');
        $query->WhereValue($cpsac['module_id'], DB_EQ, $module_id);
        $application->db->getDB_Result($query);

        $cpscar = $tables['checkout_pm_sm_currency_acceptance_rules']['columns'];
        $query = new DB_Delete('checkout_pm_sm_currency_acceptance_rules');
        $query->WhereValue($cpscar['module_id'], DB_EQ, $module_id);
        $application->db->getDB_Result($query);

        //                      (      )                                   pm_sm_accepted_currencies
        //    pm_sm_currency_acceptance_rules.
        if(sizeof($module_settings["accepted_currencies"]) > 0)
        {
	        $query = new DB_Multiple_Insert('checkout_pm_sm_accepted_currencies');
	        $query->setInsertFields(array($cpsac['module_id'],$cpsac['currency_code'],$cpsac['currency_status']));
	        foreach($module_settings["accepted_currencies"] as $info)
	        {
	            $row = array(
	                $cpsac['module_id']       => $module_id
	               ,$cpsac['currency_code']   => $info['currency_code']
	               ,$cpsac['currency_status'] => $info['currency_status']
	            );
	            $query->addInsertValuesArray($row);
	        }
	        $application->db->PrepareSQL($query);
	        $application->db->DB_Exec();
        }

        if(sizeof($module_settings["currency_acceptance_rules"]) > 0)
        {
	        $query = new DB_Multiple_Insert('checkout_pm_sm_currency_acceptance_rules');
	        $query->setInsertFields(array($cpscar['module_id'],$cpscar['rule_name'],$cpscar['rule_selected']));
	        foreach($module_settings["currency_acceptance_rules"] as $info)
	        {
	            $row = array(
	                $cpscar['module_id']       => $module_id
	               ,$cpscar['rule_name']       => $info['rule_name']
	               ,$cpscar['rule_selected']   => $info['rule_selected']
	            );
	            $query->addInsertValuesArray($row);
	        }
	        $application->db->PrepareSQL($query);
	        $application->db->DB_Exec();
        }
    }

    /**
     * Sets up "is_selected" in false, and also "is_active" in false in all
     * modules, except the passed in ones. It sets "is_selected" in true in all
     * passed modules,that exist in the database. It adds the modules, that don't
     * exist in the table. So that it should be specified in the inputted data
     * which data should be added as "active", and which of them shouldn't.
     *
     * Parameters have a complicated structure, because words Selected and Active
     * for modules depend: if a module is not Selected, it can't be Active.
     * And also because the status Selected/non-Selected is usually changed for
     * several modules at once. Administrator choses, which modules should be
     * Selected, which of them are non-Selected and then presses the button:
     * save the changes.
     */
    function setSelectedModules($modules, $modulesType, $b_settings_table_is_empty = false)
    {
        global $application;
        //The list of modules,which should become selected.
        $selected_modules_ids = array();
        foreach($modules as $module_id => $module_settings)
        {
            $selected_modules_ids[] = "'" . $module_id . "'";
        }

        $tables = Checkout::getTables();
        $columns = $tables['checkout_pm_sm_settings']['columns'];
        $YES = "1";
        $NO  = "2";

        //Sets up "is_selected" in false, and also "is_active" in false in all modules, except the passed in ones.
        if (count($selected_modules_ids) != 0)
        {
            $query = new DB_Update('checkout_pm_sm_settings');
            $query->addUpdateExpression($columns['status_selected_value_id'], $NO);
            $query->addUpdateExpression($columns['status_active_value_id']  , $NO);
            $query->WhereField($columns['module_id'], DB_NIN, "(" . implode(",", $selected_modules_ids) . ")");
            $query->WhereAnd();
            $query->WhereValue($columns['module_group'], DB_EQ, $modulesType);
            $application->db->getDB_Result($query);
        }
        //Set "is_selected" in true, and also sort_order in all passed modules that exist in the database.
        //It adds the modules, that don't exist in the table. So that it should be specified in the inputted data
        //which data should be added as "active", and which of them shouldn't.
        //First get the list of modules, entered to the database:
        //_print($selected_modules_ids);die("full stop");
        if($b_settings_table_is_empty === true)
        {
            $modules_already_in_db = array();
        }
        else
        {
            $modules_already_in_db = $this->getSelectedModules($modulesType);
        }

        foreach($modules as $module_id => $module_settings)
        {
            if(!array_key_exists($module_id, $modules_already_in_db))
            {
                //The module doesn't exist in the database.
                //Add module settings to the database.

                //        Checkout::getPaymentModuleInfo()       ,
                //                                   ,           isActive()
                //                      ,                               checkout_pm_sm_settings.
                $mm_info = Checkout::getInstalledModulesListData($modulesType, NULL, true);
                $m_name = "";
                foreach($mm_info as $key => $info)
                {
               	    if($info->UUID == $module_settings['module_id'])
               	    {
               	        $m_name = $info->name;
               	        break;
               	    }
                }
                $db_insert = new DB_Insert('checkout_pm_sm_settings');
                $db_insert->addInsertValue($module_settings['module_id'], $columns['module_id']);
                $db_insert->addInsertValue($m_name, $columns['module_class_name']);
                $db_insert->addInsertValue($module_settings['module_group'], $columns['module_group']);
                $db_insert->addInsertValue(//$module_settings['b_is_active'] === true ? $YES :
                                           $NO, $columns['status_active_value_id']);
                $db_insert->addInsertValue($module_settings['b_is_selected'] === true ? $YES : $NO, $columns['status_selected_value_id']);
                $db_insert->addInsertValue($module_settings['sort_order'], $columns['sort_order']);
                $application->db->PrepareSQL($db_insert);
                //_print($modules);_print($module_settings);_print($application->db->QueryString);die;
                $application->db->DB_Exec();

                Checkout::update_pm_sm_currency_settings($module_settings['module_id'], modApiStaticFunc($m_name, "getInitialCurrencySettings"));
            }
            else
            {
                //The module exists in the database. Update sort_order and is_selected.
                $query = new DB_Update('checkout_pm_sm_settings');
                $query->addUpdateExpression($columns['status_selected_value_id'], $YES);
                $query->addUpdateExpression($columns['sort_order'], $module_settings['sort_order']);
                $query->WhereValue($columns['module_id'], DB_EQ, $module_id);
                $application->db->getDB_Result($query);
            }
        }
    }


    /*static*/ function setModuleActive($module_id, $b_active)
    {
        //The condition, where a module doesn't exist in the Checkout table
        // settings, and setModuleActive is called for it, should
        //not exist. So you can get to the place only from the page of
        // module settings, or by automatic operatins into the system
        // (for example, by adding all_inactive to the table at the begining,
        //  when the table is empty).
        //In the first case to open the page of settings, the module should
        // be Selected, and has been added to the setting table before.
        //In the second case hte function setModuleActive is not called. The flag
        // Active is passed in within data for the function setSelectedModules.


        //Delete operations with the falg Active from the function setSelectedModules,
        // it may take much time. For all modules, which become non-Selected,
        // should be set the status non-Active.
        //But to simplify the code, they can be separated.

        global $application;
        $tables = Checkout::getTables();
        $columns = $tables['checkout_pm_sm_settings']['columns'];

        $YES = "1";
        $NO  = "2";
        $query = new DB_Update('checkout_pm_sm_settings');
        $query->addUpdateExpression($columns['status_active_value_id'], $b_active === true ? $YES : $NO);
        $query->WhereValue($columns['module_id'], DB_EQ, $module_id);
        $application->db->getDB_Result($query);
        //: add the check to update only one string
        // If zero strings are updated, then an error occurred: the module
        // doesn't exist  in the table.

        if ($b_active === true)
        {
            Checkout::setPM_SM_RequiredCurrencieslist();
        }
    }


    /**
     * function returns the list of currencies,
     * required by active PM and SM
     */
    /* static */ function getPM_SM_RequiredCurrenciesList()
    {
        $rlt = execQuery('SELECT_PM_SM_REQUIRED_CURRENCIES', array());

        $return = array();
        foreach ($rlt as $record)
        {
            if (!isset($return[$record['currency_code']]))
                $return[$record['currency_code']] = array();

            $return[$record['currency_code']][$record['module_class_name']] = $record;
        }

        return $return;
    }

    /**
     * function sets the pm/sm-required currencies to AC
     *
     */
    function setPM_SM_RequiredCurrencieslist()
    {
        $ac = modApiFunc("Localization", 'getActiveCurrenciesList', RETURN_AS_CODE_OBJECT_LIST);
        $req = modApiFunc('Checkout', 'getPM_SM_RequiredCurrenciesList');

        foreach ($req as $code => $currency)
        {
        	if (isset($ac[$code])) continue;

        	$cid = modApiFunc("Localization", "getCurrencyIdByCode", $code);
        	modApiFunc("Localization", "addNewAdditionalCurrency", $cid, 2, null, false, true);
        }
    }

    /**
     *            true,                     PersonInfoType'         .
     * false -      .
     */
    /* static */ function arePersonInfoTypesActive($types)
    {
        $type_list = Checkout::getPersonInfoTypeList();
        //                ,                       .
        $type_list_indexed_by_tags = array();
        foreach($type_list as $type_id => $info)
        {
            $type_list_indexed_by_tags[$info['tag']] = $info;
        }

        //
        foreach($types as $tag)
        {
            if(!array_key_exists($tag, $type_list_indexed_by_tags) ||
                $type_list_indexed_by_tags[$tag]['active'] == DB_FALSE)
            {
                return false;
            }
        }
        return true;
    }

    function getCustomerAttributeValue($order_id, $person_info_type_tag, $person_info_variant_tag, $person_attribute)
    {
        $params = array(
                            'order_id'=>$order_id,
                            'person_attribute'=>$person_attribute,
                            'person_info_type_tag'=>$person_info_type_tag,
                            'person_info_variant_tag'=>$person_info_variant_tag
                       );
        $result = execQuery('SELECT_CUSTOMER_ATTRIBUTE_VALUE', $params);
        return isset($result[0]["value"])? $result[0]["value"]:"";
    }

    //                    Auto_Increment,                           ID
    //                  ,                   .
    function getNextOrderId()
    {
        global $application;

        $query = "SHOW TABLE STATUS LIKE '" . $application->db->table_prefix . "orders'";
        $result = $application->db->DB_Query($query);
        if(mysqli_num_rows($result) == 1)
        {
            $result = mysqli_fetch_assoc($result);
            if(isset($result['Auto_increment']))
            {
                return $result['Auto_increment'];
            }
            else
            {
                //: report error
                return false;
            }
        }
        else
        {
            //: report error
            return false;
        }
    }

    /**
     *                        ,                                    .
     *                                                                         ,
     *                 -                                     ,
     *                      checkout        .
     *
     * @param unknown_type $id
     */
    function getOrderCurrencyList($id, $db_data = NULL)
    {
        if($db_data === NULL)
        {
            $result = execQuery('SELECT_ORDER_CURRENCY_LIST_BY_ORDER_ID', array('order_id'=>$id));
        }
        else
        {
            $result = $db_data;
        }


        $res = array();
        foreach($result as $info)
        {
        	$res[$info['currency_type']] = $info;
        }

        if(!isset($res[CURRENCY_TYPE_CUSTOMER_SELECTED]))
        {
        	$res[CURRENCY_TYPE_CUSTOMER_SELECTED] = $res[CURRENCY_TYPE_MAIN_STORE_CURRENCY];
        }
        if(!isset($res[CURRENCY_TYPE_PAYMENT_GATEWAY]))
        {
        	$res[CURRENCY_TYPE_PAYMENT_GATEWAY]   = $res[CURRENCY_TYPE_MAIN_STORE_CURRENCY];
        }
        return $res;
    }

    function getOrderProductsIDs($order_id)
    {
        global $application;
        $tables = $this->getTables();
        $op_table = $tables['order_product']['columns'];

        $query = new DB_Select();
        $query->addSelectField($op_table['id'],'id');
        $query->WhereValue($op_table['order_id'], DB_EQ, $order_id);

        $res = $application->db->getDB_Result($query);

        $opids = array();
        for($i=0;$i<count($res);$i++)
            $opids[] = $res[$i]['id'];

        return $opids;
    }


    /* static */ function getOrderStatusesWhichDecreaseInventory()
    {
    	$value =  array
    	(
    	    ORDER_STATUS_NEW
    	   ,ORDER_STATUS_IN_PROGRESS
    	   ,ORDER_STATUS_READY_TO_SHIP
    	   ,ORDER_STATUS_SHIPPED
    	   ,ORDER_STATUS_COMPLETED
    	);
    	return $value;
    }

    /* static */ function getOrderStatusesWhichIncreaseInventory()
    {
        $value =  array(ORDER_STATUS_NOT_CREATED_YET);

        if(modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED))
        {
            $value[] = ORDER_STATUS_CANCELLED;
            $value[] = ORDER_STATUS_DECLINED;
        }

        if(modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED))
        {
            $value[] = ORDER_STATUS_DELETED;
        }

        return $value;
    }

    function updateInventoryByOrderStatus($order_id, $old_status, $new_status)
    {
    	$dec = Checkout::getOrderStatusesWhichDecreaseInventory();
        $inc = Checkout::getOrderStatusesWhichIncreaseInventory();

        //
    	if(in_array($old_status,$inc) && in_array($new_status, $dec))
    	{
    		//
    		$mult = -1;
    	}
    	//
    	else if(in_array($old_status,$dec) && in_array($new_status, $inc))
    	{
    		//
            $mult = +1;
    	}
    	//                                      -
    	else
    	{
    		return;
    	}
    	$order_info = modApiFunc("Checkout", "getOrderInfo", $order_id, modApiFunc("Localization", "getCurrencyIdByCode", modApiFunc("Localization", "getOrderMainCurrency", $order_id)));
    	//                        Inventory (        +      ).
    	$products_without_inventory = array();
        foreach($order_info['Products'] as $product)
        {
        	if(!empty($product['inventory_id']) && $product['inventory_id'] !== NULL)
        	{
                modApiFunc("Product_Options","updateInventoryQuantity",$product['inventory_id'],($product['qty']*$mult));
        	}
        	else
        	{
        		 $products_without_inventory[] = $product;
        	}
        }

        if(!empty($products_without_inventory))
        {
            modApiFunc("Catalog", "updateProductsQuantity", $products_without_inventory, $mult);
        }
    }
    /**
     *                     .                                 "             "    "               "
     *             ,                                                           .
     *
     *               3        :
     * - OrdersWereUpdated
     * - OrderCreated
     * - OrdersWillBeDeleted
     *
     */
    function OnOrderStatusCreatedOrUpdatedOrDeleted($params)
    {
    	if(is_array($params) && !empty($params) && isset($params["order_status"]) && !empty($params["order_status"]))
    	{
    		// - OrdersWereUpdated
    		foreach($params["order_status"] as $order_id => $info)
    		{
                modApiFunc("Checkout", "updateInventoryByOrderStatus", $order_id, $info["old_status"], $info["new_status"]);
    		}
    	}
    	else if(is_array($params) && !empty($params) && !isset($params["payment_status"]))
    	{
    		// - OrdersWillBeDeleted
    		$orders_ids = $params;
            for($i=0;$i<count($orders_ids);$i++)
            {
            	$order_id = $orders_ids[$i];
                $order_info = modApiFunc("Checkout", "getOrderInfo", $order_id, modApiFunc("Localization", "getCurrencyIdByCode", modApiFunc("Localization", "getOrderMainCurrency", $order_id)));
                modApiFunc("Checkout", "updateInventoryByOrderStatus", $order_id, $order_info['StatusId'], ORDER_STATUS_DELETED);
            }
    	}
    	else if(!is_array($params))
    	{
    		// - OrderCreated
    		$order_id = $params;
    		$order_info = modApiFunc("Checkout", "getOrderInfo", $order_id, modApiFunc("Localization", "getCurrencyIdByCode", modApiFunc("Localization", "getOrderMainCurrency", $order_id)));
    	    modApiFunc("Checkout", "updateInventoryByOrderStatus", $order_id, ORDER_STATUS_NOT_CREATED_YET, $order_info['StatusId']);
    	}
    }

    function getOrderProductInfo($order_product_id)
    {
        global $application;
        $tables = $this->getTables();
        $op_table = $tables['order_product']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('order_product');
        $query->addSelectField('*');
        $query->WhereValue($op_table['id'], DB_EQ, $order_product_id);

        $res = $application->db->getDB_Result($query);

        if(count($res)!=1)
            return null;
        else
            return array_shift($res);
    }

     /**
     * Returnts the full person custom attribute record (person_attributes and person_info_variants_to_attributes)
     *
     * @author Andrei V. Zhuravlev
     * @param $variantId integer variant id
     * @param $attributeId integer attribute id
     */
    function getPersonCustomAttributeData($attributeId)
    {
        global $application;
        $tables = $this->getTables();
        $piva = $tables['person_info_variants_to_attributes']['columns'];
        $pa = $tables['person_attributes']['columns'];

        $query = new DB_Select();

        foreach($piva as $k => $v)
            if ($k != 'name' && $k != 'descr')
                $query->addSelectField($v);

        $query->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $piva['name'], $piva['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_name'), 'person_attribute_visible_name');

        $query->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $piva['descr'], $piva['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_descr'), 'person_attribute_description');

        foreach($pa as $v)
            $query->addSelectField($v);

        $query->WhereField($piva['attribute_id'], DB_EQ, $pa['id']);
        $query->WhereAnd();
        $query->WhereValue($piva['attribute_id'], DB_EQ, $attributeId);
        $query->WhereAnd();
        $query->WhereValue($pa['is_custom'], DB_EQ, "1");

        $result = $application->db->getDB_Result($query);
        return $result;
    }

    /**
     * This function calculates hash from checkout form fields array.
     * @author Andrei V. Zhuravlev
     *
     */
    function updateCheckoutFormHash()
    {
        global $application;

        $tables = $this->getTables();
        $pa = $tables['person_attributes']['columns'];
        $piva = $tables['person_info_variants_to_attributes']['columns'];

        $s = new DB_Select();
        $s->addSelectTable('person_attributes');
        $s->addSelectTable('person_info_variants_to_attributes');
        $s->WhereField($piva['attribute_id'], DB_EQ, $pa['id']);

        $checkout_data = $application->db->getDB_Result($s); //query fields

        $hash = md5(serialize($checkout_data));

        /*$tables = Configuration::getTables();
        $ss = $tables['store_settings']['columns'];

        $u = new DB_Update('store_settings');
        $u->addUpdateValue('variable_value',$hash);
        $u->WhereValue('variable_name', DB_EQ, SYSCONFIG_CHECKOUT_FORM_HASH);
        $application->db->getDB_Result($u);*/

        $cache = CCacheFactory::getCache('hash');
        $cache->write(SYSCONFIG_CHECKOUT_FORM_HASH, $hash);


        return $hash;
    }

    function onCartChanged()
    {
        global $zone;

        //Start checkout from the first step
        $this -> changeCurrentPersonInfoVariant('customerInfo', 'default');
        $this -> changeCurrentPersonInfoVariant('billingInfo', 'default');
        $this -> changeCurrentPersonInfoVariant('shippingInfo', 'default');

        $this -> ProcessNewStepID(1);
        $this -> setLastPlacedOrderID(NULL);

        $this -> clearAllNotMetPrerequisitesValidationResultsData();

        $this -> clearCacheObj();
    }

    /**
     * If for the selected Shipping address only one shipping method
     * (and one shipping module) is available it returns its module id and
     * method id. It returns false otherwise.
     */
    function getTheOnlyAvailableAndComputableShippingMethodId()
    {
        global $application;
        $groups = NULL;

        $SelectedModules = $this->getSelectedModules("shipping");
        $sm_list = $this->getInstalledAndActiveModulesListData("shipping", $groups);

        $there_is_a_shipping_module_with_only_one_available_method = false;
        $res = false;

        foreach ($sm_list as $sm_item)
        {
            // create/use some mm function to convert class names.
            $name = _ml_strtolower($sm_item->name);

            $smInfo = modApiFunc($name, "getInfo");
            // : check if function exists
            $module_uid = $smInfo['GlobalUniqueShippingModuleID'];

            if (array_key_exists($module_uid, $SelectedModules) == true)
            {
               /* Check, if for the current address even one method works.
                  Otherwise output a special template "No shipping method
                  available for the inputted address".
                  What should be outputted, if the address hasn't been inputted yet?
                */

                //$methods_info_list = modApiFunc($smInfo['APIClassName'], "getShippingMethods", "AVAILABLE", true);

                # Added by egor

                $formatted_cart=modApiFunc("Shipping_Cost_Calculator","formatCart",modApiFunc("Cart","getCartContent"));
                modApiFunc("Shipping_Cost_Calculator","setShippingInfo",$this->getPrerequisiteValidationResults("shippingInfo"));
                modApiFunc("Shipping_Cost_Calculator","setCart",$formatted_cart);

                $calculation_result=modApiFunc("Shipping_Cost_Calculator","calculateShippingCost");

                if(!empty($calculation_result))
                {
                    $first_api_result=array_shift($calculation_result);
                    if(isset($first_api_result['methods']) and is_array($first_api_result['methods']) and !empty($first_api_result['methods']))
                        $methods_info_list=$first_api_result['methods'];
                    else
                        $methods_info_list=array();
                }
                else
                    $methods_info_list=array();

                # end

                if(!empty($methods_info_list))
                {
                    if(count($methods_info_list) == 1)
                    {
                        if($there_is_a_shipping_module_with_only_one_available_method == true)
                        {
                            //This is the SECOND module, which has available methods.
                            return false;
                        }
                        else
                        {
                            $there_is_a_shipping_module_with_only_one_available_method = true;

                            $res = array("module_id" => $module_uid
                                        ,"method_id" => $methods_info_list[0]["id"]);
                        }
                    }
                    else
                    if(count($methods_info_list) > 1)
                    {
                        //Which method has more than one available method.
                        return false;
                    }
                }
            }
        }

        return $res;
    }

    /**
     * The list of Checkout views.
     */
    var $ViewsList;

    /**
     * Settings from DB.
     */
    var $Settings = NULL;

    /**
     * Current Checkout Step ID
     */
    var $currentStepID = NULL;

    /**
     * Current Selected Order.
     */
    var $currentOrderID = NULL;

    /**
     * Last Placed Order
     */
    var $lastPlacedOrderID = NULL;

    /**
     * Current Selected Customer.
     */
    var $currentCustomerID = NULL;

    /**
     * List of action handlers.
     */
    var $ActionHandlersList;

    /**
     * A table of compatability of prerequisite and store block.
     */
    var $PrerequisiteToStoreBlockTable = array
        (
        //'customerInfo'     => 'customer-info-input',
        'shippingInfo' => 'shipping-info-input',
        'shippingModuleAndMethod'  => 'shipping-method-list-input',
        'billingInfo'  => 'billing-info-input',
        'paymentModule'   => 'payment-method-list-input',
        'subscriptionTopics'  => 'subscription-topics-input',
        );

    /**
     * The table, that specifies the functions to check prerequisites.
     * Data form blocks is coming through GET or POST. Every block contains its
     * name. As mentioned above, each block matches the prerequisite. Define the
     * prerequisite by the block name and call matching it validation function.
     */
    /* prerequisite => validation-function */
/*    var $PrerequisitesValidationFunctionsTable = array
        (
        'customerInfo'     => 'validateInputForStep',
        'shippingInfo' => 'validateInputForStep',
        'shippingModuleAndMethod'  => 'validateInputForStep', //Calls the validation function matching a shipping module. It returns true/false and a formatted array with shipping data. See a long paper.
        'billingInfo'  => 'validateInputForStep',
        'paymentMethod'   => 'validateInputForStep'
        );
*/

    /**
     *                                                       prerequisite'  .
     *             ,                        ,    'isMet' = false,       =true.
     */
    var $PrerequisitesValidationResults; /* = array
        (
        'customer-info'     => array('isMet' => false,
                                     'error_code' => 'CHECKOUT_ERR_004',
                                     'error_message_parameters' => array($this->PrerequisitesValidationResults[$prerequisite]["visibleName"], $prerequisite)
                                     'validatedData' => array( array(                 'view_tag'=>'',
                                                                                      'value'=>'',
                                                                                      'pattern_id' => '',
                                                                                      'error_code_short'    =>'',
                                                                                      'error_code_full'    =>'',
                                                                                       ... ),
                                                                                array('view_tag'=>'',
                                                                                      'value'=>'',
                                                                                      'pattern_id' => '',
                                                                                      'error_code_short'    =>'',
                                                                                      'error_code_full'    =>'',
                                                                                       ... )
                                                                              ),
        'shipping-info' => array('isMet' => false, 'validatedData' => array()),
        'shipping-method'  => array('isMet' => false, 'validatedData' => array()),
        'billing-info'  => array('isMet' => false, 'validatedData' => array()),
        'payment-method'   => array('isMet' => false, 'validatedData' => array())
        );
        */

    var $order_search_filter = array();

    var $customer_search_filter = array();

    /**
     * View, which is responsible for editing the current selected
     * Payment module.
     */
    var $CurrentPaymentModuleSettingsViewName = NULL;

    var $CurrentPaymentShippingModuleSettingsUID = NULL;


    /**
     * Some HTML code, to be viewed, that came as answer form the payment
     * gateway.
     */
    var $CustomPaymentGatewayPageContents = NULL;

    /**
     * View, which is responsible for editting the current selected
     * Shipping module.
     */
    var $CurrentShippingModuleSettingsViewName = NULL;

    var $MessageResources;

    var $DeleteOrdersFlag;
    /**#@-*/

}

global $zone;
if($zone == 'AdminZone')
{
    loadModuleFile('checkout/includes/checkout_api_az.php');
    eval("class Checkout extends Checkout_AZ{};");
}
else
{
    eval("class Checkout extends CheckoutBase{};");
}

?>