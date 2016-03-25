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

define("PAYPAL_URL_TEST", "https://www.sandbox.paypal.com/cgi-bin/webscr");
define("PAYPAL_SOCKET_TEST", "www.sandbox.paypal.com");

define("PAYPAL_URL", "https://www.paypal.com/cgi-bin/webscr");
define("PAYPAL_SOCKET", "www.paypal.com");


/**
 *
 * @package PaymentModulePaypalCC
 * @author Vadim Lyalikov
 */
class Payment_Module_Paypal_CC extends pm_sm_api
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /* static */ function getInitialCurrencySettings()
    {
        $res = array
        (
            'currency_acceptance_rules' => array
            (
                array
                (
                    'rule_name'     =>  ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER
                   ,'rule_selected' =>  DB_FALSE
                )
               ,array
                (
                    'rule_name'     =>  THE_ONLY_ACCEPTED
                   ,'rule_selected' =>  DB_TRUE
                )
               ,array
                (
                    'rule_name'     =>  MAIN_STORE_CURRENCY
                   ,'rule_selected' =>  DB_FALSE
                )
            )

            /**
             * Website Payments Standard Integration Guide, Last updated: March 2008.
             * TABLE 2.6 Currencies Allowed for Transactions and Balances, Currency ISO-4217 Code
             */
           ,'accepted_currencies' => array
            (
                array
                (
                    'currency_code'   => 'AUD'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'CAD'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'CHF'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'CZK'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'DKK'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'EUR'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'GBP'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'HKD'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'HUF'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'JPY'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'NOK'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'NZD'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'PLN'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'SEK'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'SGD'
                   ,'currency_status' => 'NOT_ACCEPTED'
                )
               ,array
                (
                    'currency_code'   => 'USD'
                   ,'currency_status' => 'THE_ONE_ONLY_ACCEPTED'
                )
            )
        );
        return $res;
    }


    /**
     * PaymentModulePaypal  constructor.
     */
    function Payment_Module_Paypal_CC()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");

        $info = $this->getInfo();

        $this->getSettings();

        $this->OrderHistoryMessageTag = $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MODULE_ID') . $info["GlobalUniquePaymentModuleID"] . "\n" .
///        $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MODULE_NAME') .
        $this->Settings["MODULE_NAME"];// .
//        $obj->getMessage('MODULE_NAME');
    }


    function getmicrotime()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");

        list($usec, $sec) = explode(" ",microtime());

        return $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_TIME') . (float)($usec + $sec) . " " . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_SECONDS');
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Paypal_CC::getTables() instead of $this->getTables().
     */
    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");

        $tables = Payment_Module_Paypal_CC::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pm_paypal_settings';            #
        $columns = $tables[$table]['columns'];  #

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue("MODULE_NAME", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_NAME')).':"'.$obj->getMessage('MODULE_NAME').'";', $columns['value']);
        $application->db->getDB_Result($query);
/*
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue("MODULE_DESCR", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_DESCR')).':"'.$obj->getMessage('MODULE_DESCR').'";', $columns['value']);
        $application->db->getDB_Result($query);
*/
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue("MODULE_EMAIL", $columns['key']);
        $query->addInsertValue('s:20:"you@yourbusiness.com";', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue("MODULE_MODE", $columns['key']);
        $query->addInsertValue('s:1:"1";', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue("MODULE_CART", $columns['key']);
        $query->addInsertValue('s:1:"1";', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue("MODULE_BILLING_INFO", $columns['key']);
        $query->addInsertValue('s:1:"1";', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue("MODULE_ADDRESS_OVERRIDE", $columns['key']);
        $query->addInsertValue('s:1:"1";', $columns['value']);
        $application->db->getDB_Result($query);

        //                                RemovePersonInfoType
        //                   ,          -     CheckoutEditor
        //             PersonInfoType,         , BillingInfo, ShippingInfo,
        //  CustomerInfo, CreditCardInfo, BankAccountInfo, PaymentMethodInfo (
        //             ), ShippingMethodInfo (              ).
        //       PaymentModule'                                                 ,
        //                                     .
        //                    ,                 PersonInfoType          .
        //
        //  PersonInfoType.
        modApiFunc('EventsManager',
               'addEventHandler',
               'RemovePersonInfoTypeEvent',
               'Payment_Module_Paypal_CC',
               'OnRemovePersonInfoType');
    }

    /**
     *                    RemovePersonInfoType.
     *                  ,          -     CheckoutEditor
     *            PersonInfoType,         , BillingInfo, ShippingInfo,
     * CustomerInfo, CreditCardInfo, BankAccountInfo, PaymentMethodInfo (
     *            ), ShippingMethodInfo (              ).
     *      PaymentModule'                                                 ,
     *                                    .
     *                   ,                 PersonInfoType          .
     *
     * PersonInfoType.
     *
     *        Payment_Module_Paypal_CC                    2 PersonInfoType' :
     * billingInfo
     * shippingInfo
     */
    function OnRemovePersonInfoType($person_info_type_tag)
    {
        global $application;

        if($this->isActive() === false)
        {
            //                        -                                .
            $value =  NULL;
        }
        else
        {
            $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");
            $msg = $this->MessageResources->getMessage('MODULE_PAYMENT_PAYPAL_API_MSG_REMOVE_PERSON_INFO_TYPE');

            $value = NULL;
            switch($person_info_type_tag)
            {
                case "billingInfo":
                {
                    if ($this->Settings['MODULE_BILLING_INFO'] == 1)
                    {
                        $value = $msg;
                    }
                    break;
                }
                case "shippingInfo":
                {
                    if ($this->Settings['MODULE_ADDRESS_OVERRIDE'] == 1)
                    {
                        $value = $msg;
                    }
                    break;
                }
                default:
                {
                    break;
                }
            }
        }
        return $value;
    }



    /**
     * This method is invoked by Checkout module in CZ (Customer Zone). It is
     * also invoked in AZ (Admin Zone).
     * returns - Boolean - Show this module during the process of Checkout in
     * Customer Zone or not?
     */
    function isActive()
    {
        global $application;
        $active_modules = modApiFunc("Checkout","getActiveModules","payment");
        return array_key_exists($this->getUid(), $active_modules);
    }

    function getUid()
    {
//        return "F45499B9-9850-FD62-9890-323E996B6B4B";
        include(dirname(__FILE__)."/includes/uid.php");
        return $uid;
    }

    function getInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");

        $this->getSettings();

        return array("GlobalUniquePaymentModuleID" => $this->getUid(),
                     "Name"        => $this->Settings["MODULE_NAME"],
//                     "Description" => $this->Settings["MODULE_DESCR"],
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>",
                     //"Undefined", //NOT for active/inactive.
                     // there is special config in Checkout module to handle/store
                     // active/inactive flags.
                     "PreferencesAZViewClassName" => "paypal_cc_input_az",
                     "CZInputViewClassName" => "CheckoutPaymentModulePaypalCCInput",
                     "CZOutputViewClassName" => "CheckoutPaymentModulePaypalCCOutput",
                     "APIClassName" => __CLASS__
                    );
    }
    /**
     * Clears the Settings table.
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('pm_paypal_settings');
        $application->db->getDB_Result($query);
    }

    /**
     * Gets current module settings from Settings.
     *
     * @return array - module settings array
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'pm_paypal_settings'));
        $this->Settings = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $this->Settings[$result[$i]['set_key']] = unserialize($result[$i]['set_value']);
        }
        return $this->Settings;
    }

    /**
     * Sets up module attributes and logs it to the database.
     *
     * @param array $Settings - module settings array.
     */
    function setSettings($Settings)
    {
        global $application;
        $this->clearSettingsInDB();
        $tables = $this->getTables();
        $columns = $tables['pm_paypal_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('pm_paypal_settings');
            $query->addInsertValue($key, $columns['key']);
            $query->addInsertValue(serialize($value), $columns['value']);
            $application->db->getDB_Result($query);

            $inserted_id = $application->db->DB_Insert_Id();
        }
    }

    /**
     * Sets up module attributes and logs it to the database.
     *
     * @param array $Settings -  module settings array. The undefined parameter values
     * remain unchanged.
     */
    function updateSettings($Settings)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['pm_paypal_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('pm_paypal_settings');
            $query->addUpdateValue($columns['value'], serialize($value));
            $query->WhereValue($columns['key'], DB_EQ, $key);
            $application->db->getDB_Result($query);
        }
    }

    /**
     * It is called from Checkout. It returns the cost of selected delivery
     * method. The cost should be 0 for the majority of payment methods.
     */
    function getPaymentCost($method_id)
    {
        return 0;
    }

    /**
     * Converts the value of the monetary sum to be used _out_ ASC.
     * If the price equals PRICE_N_A, then it is changed to 0.0.
     */
    function export_PRICE_N_A($price)
    {
        return ($price == PRICE_N_A) ? 0.00 : number_format($price, 2, '.', '');
    }

    /**
     * Prepares and returns necessary data, passed to the payment gateway.
     *
     * @ not all data is defined
     */
    function getConfirmationData($orderId)
    {
        global $application;
        loadCoreFile('aal.class.php');

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction("UpdatePaymentStatus");
        $request->setKey("asc_oid", $orderId);
        $self_link = $request->getURL("", true);

        $currency_id = modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $orderId, $this->getUid());
        $currency = modApiFunc("Localization", "getCurrencyCodeById", $currency_id);

        $ocntr = modApiFunc("Location","getCountryCode",modApiFunc("Configuration","getValue",SYSCONFIG_STORE_OWNER_COUNTRY));
        $bn_code = "PentasoftCorp_Cart_WPS_" . $ocntr;

        $orderInfo = modApiFunc("Checkout", "getOrderInfo", $orderId, $currency_id);

        $discount = $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "DiscountsSum", $currency_id));
        $gc = $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "TotalPrepaidByGC", $currency_id));
        $amount = $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "Subtotal", $currency_id) - $discount - $gc);
        if($amount < 0) $amount = 0;

        $this->getSettings();
        $confirmationData = array
            (
                //"FormAction" => PAYPAL_URL
                "FormMethod" => "POST"
               ,"DataFields" => array
                    (
                        "cmd"           => "_xclick"
                       ,"rm"            => "2"
                       ,"business"      => $this->Settings["MODULE_EMAIL"]
                       ,"item_name"     => modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_NAME)
                       ,"amount"        => number_format($amount, 2, '.', '')
                       ,"shipping"      => number_format($this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "TotalShippingAndHandlingCost", $currency_id)), 2, '.', '')
                       ,"tax"           => number_format($this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "NotIncludedTax", $currency_id)), 2, '.', '')
                       ,"currency_code" => $currency
                       ,"bn"            => $bn_code
                       ,"return"        => $self_link ."&status=return"
                       ,"notify_url"    => $self_link ."&status=notify"
                       ,"cancel_return" => $self_link ."&status=cancel"
                    )
            );

        if ($this->Settings['MODULE_CART'] == 1 && $discount == 0)
        {
            $items = array();
            $n = 1;
            foreach ($orderInfo["Products"] as $productInfo)
            {
                $items["item_name_".$n] = prepareHTMLDisplay($productInfo["name"]);

                $items["amount_".$n] = number_format($productInfo["SalePrice"], 2);
                $items["quantity_".$n] = $productInfo["qty"];
                foreach ($productInfo["options"] as $i => $option)
                {
                    if ($i > 1)
                    {
                        $items["os1_".$n].= ", ".$option["option_name"].": ".$option["option_value"];
                        if (_ml_strlen($items["os1_".$n]) >= 200)
                        {
                            $items["os1_".$n] = _ml_substr($items["os1_".$n], 0, 197)."...";
                        }
                    }
                    else
                    {
                        $items["on".$i."_".$n] = $option["option_name"];
                        $items["os".$i."_".$n] = $option["option_value"];
                    }
                }
                $n++;
            }
            $confirmationData["DataFields"] = array_merge($confirmationData["DataFields"], $items);
            $confirmationData["DataFields"]["cmd"] = "_cart";
            $confirmationData["DataFields"]["upload"] = "1";
            $confirmationData["DataFields"]["handling_cart"] = $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "TotalShippingAndHandlingCost", $currency_id));
            $confirmationData["DataFields"]["tax_cart"] = $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "NotIncludedTax", $currency_id));
            unset($confirmationData["DataFields"]["shipping"]);
            unset($confirmationData["DataFields"]["tax"]);
        }

        if ($this->Settings['MODULE_BILLING_INFO'] == 1)
        {
            $billingInfo = new ArrayAccessLayer($orderInfo);
            $billingInfo->setAccessMask ("Billing",  "attr", AAL_CUSTOM_PARAM, "value");

            if ($confirmationData["DataFields"]["cmd"] == "_xclick")
            {
                $confirmationData["DataFields"]["cmd"] = "_ext-enter";
                $confirmationData["DataFields"]["redirect_cmd"] = "_xclick";
            }
            $confirmationData["DataFields"]["first_name"] = $billingInfo->getByMask('Firstname');
            $confirmationData["DataFields"]["last_name"] = $billingInfo->getByMask('Lastname');
            $confirmationData["DataFields"]["address1"] = $billingInfo->getByMask('Streetline1');
            $confirmationData["DataFields"]["address2"] = $billingInfo->getByMask('Streetline2');
            $confirmationData["DataFields"]["city"] = $billingInfo->getByMask('City');
            $confirmationData["DataFields"]["state"] = modApiFunc("Location", "getStateCodeByStateName", $billingInfo->getByMask('State'));
            if (!$confirmationData["DataFields"]["state"])
            {
                $confirmationData["DataFields"]["state"] = $billingInfo->getByMask('State');
            }
            $confirmationData["DataFields"]["zip"] = $billingInfo->getByMask('Postcode');
            $confirmationData["DataFields"]["country"] = modApiFunc("Location", "getCountryCodeByCountryName", $billingInfo->getByMask('Country'));
            $confirmationData["DataFields"]["email"] = $billingInfo->getByMask('Email');

            if ($billingInfo->getByMask('Phone'))
            {
                $phone = $billingInfo->getByMask('Phone');
                $phone = preg_replace("/[^0-9]/", "", $phone);
                if (isset($confirmationData["DataFields"]["country"]) && $confirmationData["DataFields"]["country"] == "US")
                {
                    if (_ml_strlen($phone) == 10)
                    {
                        $confirmationData["DataFields"]["night_phone_c"] = _ml_substr($phone, -4);
                        $phone = _ml_substr($phone, 0, -4);
                        $confirmationData["DataFields"]["night_phone_b"] = _ml_substr($phone, -3);
                        $phone = _ml_substr($phone, 0, -3);
                        $confirmationData["DataFields"]["night_phone_a"] = _ml_substr($phone, -3);
                    }
                }
            }
        }

        if ($this->Settings['MODULE_ADDRESS_OVERRIDE'] == 1)
        {
            $shippingInfo = new ArrayAccessLayer($orderInfo);
            $shippingInfo->setAccessMask ("Shipping",  "attr", AAL_CUSTOM_PARAM, "value");

            $confirmationData["DataFields"]["address_override"] = "1";
            $confirmationData["DataFields"]["first_name"] = $shippingInfo->getByMask('Firstname');
            $confirmationData["DataFields"]["last_name"] = $shippingInfo->getByMask('Lastname');
            $confirmationData["DataFields"]["address1"] = $shippingInfo->getByMask('Streetline1');
            $confirmationData["DataFields"]["address2"] = $shippingInfo->getByMask('Streetline2');
            $confirmationData["DataFields"]["city"] = $shippingInfo->getByMask('City');
            $confirmationData["DataFields"]["state"] = modApiFunc("Location", "getStateCodeByStateName", $shippingInfo->getByMask('State'));
            if (!$confirmationData["DataFields"]["state"])
            {
                $confirmationData["DataFields"]["state"] = $shippingInfo->getByMask('State');
            }
            $confirmationData["DataFields"]["zip"] = $shippingInfo->getByMask('Postcode');
            $confirmationData["DataFields"]["country"] = modApiFunc("Location", "getCountryCodeByCountryName", $shippingInfo->getByMask('Country'));
            $confirmationData["DataFields"]["email"] = $shippingInfo->getByMask('Email');

            if ($shippingInfo->getByMask('Phone'))
            {
                $phone = $shippingInfo->getByMask('Phone');
                $phone = preg_replace("/[^0-9]/", "", $phone);
                if (isset($confirmationData["DataFields"]["country"]) && $confirmationData["DataFields"]["country"] == "US")
                {
                    if (_ml_strlen($phone) == 10)
                    {
                        $confirmationData["DataFields"]["night_phone_c"] = _ml_substr($phone, -4);
                        $phone = _ml_substr($phone, 0, -4);
                        $confirmationData["DataFields"]["night_phone_b"] = _ml_substr($phone, -3);
                        $phone = _ml_substr($phone, 0, -3);
                        $confirmationData["DataFields"]["night_phone_a"] = _ml_substr($phone, -3);
                    }
                }
            }
        }

        $confirmationData['FormAction'] = $this->Settings['MODULE_MODE'] == 1 ? PAYPAL_URL_TEST : PAYPAL_URL;

    //=========================== logging request ========================

        $msgObj = $application->getInstance("MessageResources", "payment-module-paypal-messages", "AdminZone");
        $title = $msgObj->getMessage("MODULE_PAYMENT_TIMELINE_HEADER_CONFIRMATION_DATA");
        $title = str_replace('{ORDER_ID}', $orderId, $title);
        $this->addRequestLog("LOG_PM_INPUT", "Payment Module Logs", $title, prepareArrayDisplay($confirmationData));

    //=========================== logging request ========================

        return $confirmationData;
    }

    function processIPNnotify($post_data, $order_id)
    {
    	global $application;
    	$obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");
    	// read the post from PayPal system and add 'cmd'
    	$req = 'cmd=_notify-validate';
    	$raw_post_data = file_get_contents('php://input');

    	$raw_post_array = explode('&', $raw_post_data);
    	$myPost = array();
    	foreach ($raw_post_array as $keyval) {
    		$keyval = explode ('=', $keyval);
    		if (count($keyval) == 2)
    			$myPost[$keyval[0]] = urldecode($keyval[1]);
    	}

    	if(function_exists('get_magic_quotes_gpc')) {
    		$get_magic_quotes_exists = true;
    	}
    	foreach ($myPost as $key => $value) {
    		if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
    			$value = urlencode(stripslashes($value));
    		} else {
    			$value = urlencode($value);
    		}
    		$req .= "&$key=$value";
    	}
    	$settings = $this->getSettings();
    	$socketUrl = $settings['MODULE_MODE'] == 1 ? PAYPAL_URL_TEST : PAYPAL_URL;

    	$ch = curl_init($socketUrl);
    	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    	// In wamp like environments that do not come bundled with root authority certificates,
    	// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
    	// of the certificate as shown below.
    	// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
    	if( !($res = curl_exec($ch)) ) {
    		error_log("Got " . curl_error($ch) . " when processing IPN data");
    		curl_close($ch);

    		return array("payment_status" => array());
    	}

    	curl_close($ch);

    	/*        foreach ($post_data as $key => $value)
    	 {
    	$value = urlencode(stripslashes($value));
    	$req .= "&$key=$value";
    	}
    	*/

    	// assign posted variables to local variables
    	$item_name        = isset($post_data['item_name'])? $post_data['item_name']:"";
    	$item_number      = isset($post_data['item_number'])? $post_data['item_number']:"";
    	$payment_status   = $post_data['payment_status'];
    	$payment_amount   = $post_data['mc_gross'];
    	$payment_currency = $post_data['mc_currency'];
    	$txn_id           = $post_data['txn_id'];
    	$receiver_email   = $post_data['receiver_email'];
    	$payer_email      = $post_data['payer_email'];
    	/* Update the order: write txn_id */
    	modApiFunc("Checkout", "updateOrderPaymentProcessorOrderID", $order_id, $txn_id);


    	modApiFunc("Checkout", "addOrderHistory", $order_id, $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_001'));

    	$historyMessage = $this->OrderHistoryMessageTag;
    	$paymentStatusId = "";

    	if (strcmp (strtolower($res), "verified") == 0)
    	{
    		$historyMessage .= "\n" . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_002') . "\n";

    		switch ($payment_status)
    		{
    			case "Pending":
    				$paymentStatusId = 1;
    				break;
    			case "Completed":
    			case "Canceled_Reversal":
    				$paymentStatusId = 2;
    				break;
    			default:
    				$paymentStatusId = 3;
    				break;
    		}

    		$po = $this->getPresentOrderTotalAndCurrency($order_id);
    		if (round($po['total'],2) != round(floatval($payment_amount),2) || $po['curr'] != $payment_currency)
    		{
    			$paymentStatusId = 4;
    			$historyMessage .= "\n" . $po['msg'];
    		}
    		$historyMessage .=  $this->getmicrotime() . "\n" . $obj->getMessage('PAYMENT_STATUS_'._ml_strtoupper($payment_status).'_MSG');

    		if ($payment_status == "Pending")
    		{
    			$historyMessage .=  "\n" . $obj->getMessage('PAYMENT_STATUS_PENDING_'._ml_strtoupper($post_data["pending_reason"]).'_MSG');
    		}
    		if ($payment_status == "Reversed" || $payment_status == "Refunded")
    		{
    			$historyMessage .=  "\n" . $obj->getMessage('PAYMENT_STATUS_REVERSED_'._ml_strtoupper($post_data["reason_code"]).'_MSG');
    		}
    	}
    	elseif (strcmp ($res, "INVALID") == 0)
    	{
    		modApiFunc("Checkout", "addOrderHistory", $order_id, $this->OrderHistoryMessageTag);
    		// log for manual investigation
    		$paymentStatusId = 3;
    		$historyMessage .=  $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_003');
    		break;
    	}
    	else
    	{
    		//HTTP Header lines.
    	} //strcmp of res


    	return modApiFunc("Checkout", "UpdatePaymentStatusInDB", $order_id, $paymentStatusId, $historyMessage);
    }



    /**
     * Processes data on updating the order status, come from the payment gateway.
     * The flag &$bStop is set by the payment module. If it is true on return,
     * then Checkout module stops the data process, come from the payment gateway.
     */
    function processData($data, $order_id /*, &$DBPaymentStatus, &$bStop, &$bPaymentFinished, &$EventType */)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");
        //the first step of data processing from the Payment Gateway
        //the payment status update.
        //the next step.
        //do not call the Payment Gateway .
        $DBPaymentStatus = array();
        $status = $data["_GET"]["status"];

    //=========================== logging request ========================

        $msgObj = $application->getInstance("MessageResources", "payment-module-paypal-messages", "AdminZone");
        $title = $msgObj->getMessage("MODULE_PAYMENT_TIMELINE_HEADER_PROCESS_DATA");
        $title = str_replace('{ORDER_ID}', $order_id, $title);
        $this->addRequestLog("LOG_PM_INPUT", "Payment Module Logs", $title, prepareArrayDisplay($data));

    //=========================== logging request ========================

        switch ($status)
        {
            case "notify":
                $EventType = "BackgroundEvent";

                $result = modApiFunc("Checkout", "addOrderHistory", $order_id,
                           $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_004') . $data["_POST"]["payment_status"] . "\n " . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_005') . " \n " . print_r($data["_POST"], true));

                //next 2 steps : processing IPN "notify"
                $result = $this->processIPNnotify($data["_POST"], $order_id);
                break;

            case "return":
                $EventType = "ConfirmationSuccess";
                $result = modApiFunc("Checkout", "addOrderHistory", $order_id,
                           $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_006'));
                break;

            case "cancel":
                $EventType = "ConfirmationFailure";
                $result = modApiFunc("Checkout", "UpdatePaymentStatusInDB", $order_id, 3,
                           $this->OrderHistoryMessageTag . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_LOG_MESSAGE_AZ_007'));
                break;
            default:
                //: Report Error: e.g. write to order history
                $result = array("payment_status" => array());
                break;
        }

        return array("EventType" => $EventType, "statusChanged" => $result);
    }

    /**
     * Uninstalls the module.
     * It deletes all module tables.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Paypal_CC::getTables() instead of $this->getTables().
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Payment_Module_Paypal_CC::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of meta description of the table:
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       # several key fields may be used, e.g. - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      # several fields can be used in one index, e.g. - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -  the meta description of module tables
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $settings = 'pm_paypal_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.pm_paypal_setting_id'
               ,'key'               => $settings.'.pm_paypal_setting_key'
               ,'value'             => $settings.'.pm_paypal_setting_value'
            );
        $tables[$settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR255
            );
        $tables[$settings]['primary'] = array
            (
                'id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A flag, which is stored in the database. It indicates, if the specified
     * module is mapped in CZ Checkout.
     */
    var $bActive;

    var $OrderHistoryMessageTag;
    /**#@-*/

}
?>