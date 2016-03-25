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
 *
 * @package PaymentModuleOfflineCC
 * @author Alexander Girin
 */

/*           "                ".         . */
define("PAYMENT_OFFLINE_CC_URL", "checkout.php");

class Payment_Module_Offline_CC extends pm_sm_api
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * PaymentModuleOfflineCC  constructor.
     */
    function Payment_Module_Offline_CC()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
        //: gen real UID
        $info = $this->getInfo();

        $this->getSettings();

        $this->OrderHistoryMessageTag = $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_LOG_MODULE_ID')
                                        . $info["GlobalUniquePaymentModuleID"] . "\n"
                                        . $this->Settings["MODULE_NAME"];
    }

    /**
     * Replaces Rsa key pair. The first step: prepare temporary data on the server:
     * a temporary table in the DB to reencrypt data etc.
     *
     * : close the shop?
     *
     * It removes the temporary table with the reencrypted data order_person_info,
     * if it exists. It creates the same one, by copying order info.
     * It initializes and saves required ancillary variables to the module
     * settings Offline CC.
     *
     */
    function ReplaceRSAKeyPairStep1PrepareServerTmpData()
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $table_suffix = modApiFunc("Payment_Module_Offline_CC", "getTmpTableSuffix");
        $table_name = "order_person_data";
        $table_name_with_prefix_and_suffix = $table_prefix . $table_name . $table_suffix;
        $table_name_with_suffix =                            $table_name . $table_suffix;
        if (DB_MySQL::DB_isTableExists($table_name_with_prefix_and_suffix))
        {
            /**
             * Remove old temporary data. It can left if, for example,
             * the previous data reencryption wasn't completed.
             */
            $db_table_delete = new DB_Table_Delete($table_name_with_suffix);
            $application->db->PrepareSQL($db_table_delete);
            $application->db->DB_Exec();
        }

        //Prepare new temporary data for reencrypting.
        new DB_Table_Clone("Checkout", $table_name, $table_name . $table_suffix);
        /**
         * Prepare new optional data storing reencription status.
         *
         * The field order_person_data_id has now an attribute auto_increment now,
         * so just remember that not decrypted, where we stopped last time.
         * All records with great ID will be considered as not reencrypted.
         *
         */
        $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId = 1;
        modApiFunc("Payment_Module_Offline_CC", "saveState");
    }

    /**
     * Reencrypts temporary data on the server. The step of replacing RSA keys.
     * It selects encrypted data by chunks from the database. It reencrypts it and
     * saves back to the temporary table. If all data are reencrypted, returns
     * b_finished =true in the returned array, false otherwise.
     *
     * @param string $rsa_private_key_cryptrsa_format old RSA private key, which
     * was used to encrypt data, stored in the DB
     * @param string $new_rsa_public_key_asc_format new RSA public key, which is
     * used to encrypt data, decrypted by the old RSA private key.
     */
    function ReplaceRSAKeyPairStep2ReencryptTmpData($rsa_private_key_cryptrsa_format, $new_rsa_public_key_asc_format)
    {
        global $application;
        $new_rsa_public_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $new_rsa_public_key_asc_format);
        /**
         * Read out from the temporary table 500 records at a time (empirical
         * value).
         *
         * Reencrypt by chunks, that have the same Blowfish key, it is about
         * 10 database records. The decryption of one blowfish key (RSA), if no
         * mathematical libraries exist, can take 10 sec.
         * Check the timeout after each chunk - 2 sec.
         * If no records are left and the timeout is over, exit.
         *
         * Write what has been reencrypted to the database.
         */
        $tmp_table_name = "order_person_data" . $this->getTmpTableSuffix();
        // TableInfo only, but not data. Refer to the table using AVACTIS.
        $opd_tmp_info = clone_db_table_info("Checkout", "order_person_data", $tmp_table_name);
        $opd_tmp = $opd_tmp_info['columns'];

        # get Person Info data. Total record number.
        $query = new DB_Select();
        $query->addSelectField($query->fCount('*'), 'count');
        $query->Where($opd_tmp['b_encrypted'], DB_EQ, "1");
        $result = $application->db->getDB_Result($query);
        $n_total = $result[0]['count'];

        # get Person Info data.
        $query = new DB_Select();
        $query->addSelectField($opd_tmp['id'], 'id');
        $query->addSelectField($opd_tmp['value'], 'value');
        $query->addSelectField($opd_tmp['encrypted_secret_key'], 'encrypted_secret_key');
        $query->addSelectField($opd_tmp['rsa_public_key_asc_format'], 'rsa_public_key_asc_format');
        $query->Where($opd_tmp['b_encrypted'], DB_EQ, "1");
        $query->WhereAnd();
        $query->Where($opd_tmp['id'], DB_GTE, $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId);
        $query->SelectOrder($opd_tmp['id']);
        $query->SelectLimit(0, 500);

        $_person_data = $application->db->getDB_Result($query);

        if(sizeof($_person_data) == 0)
        {
            //No unreencrypted data is left. The reencryption is completed.
            return array("error_msg" => "", "b_finished" => true, "progress_position" => 1.0);
        }
        else
        {
            $i = 0; // a number of record from order_person_data
            $start_time = time();
            while ( (time() - $start_time < 2) ) //timeout 2  .
            {
                //Process one block with the same blowfish key.
                $rsa_encrypted_blowfish_key = $_person_data[$i]['encrypted_secret_key'];
               /*
                 If the loaded Private key doesn't match the Public key storing in the database  -
                 output an error message. Don't rewrite anything in the database.
                 */
                $old_rsa_public_key_asc_format = $_person_data[$i]['rsa_public_key_asc_format'];
                $old_rsa_public_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $old_rsa_public_key_asc_format);

                if(modApiFunc("Crypto","rsa_do_public_key_match_private_key", $old_rsa_public_key_cryptrsa_format, $rsa_private_key_cryptrsa_format) === true)
                {
                    //BEGIN decrypt blowfish key.

                    $rsa_obj = new Crypt_RSA;
                    $blowfish_key = $rsa_obj->decrypt($rsa_encrypted_blowfish_key, $rsa_private_key_cryptrsa_format);
                    $new_blowfish_key = modApiFunc("Crypto", "blowfish_gen_blowfish_key");
                    $new_encrypted_blowfish_key = $rsa_obj->encrypt($new_blowfish_key, $new_rsa_public_key_cryptrsa_format);
                    //END decrypt blowfish key.

                    //Bulk INSERT will increase the rate greatly!
                    for(;$i < sizeof($_person_data) &&
                         $_person_data[$i]['encrypted_secret_key'] == $rsa_encrypted_blowfish_key; $i++)
                    {
                        $decrypted_value = modApiFunc("Crypto", "blowfish_decrypt", base64_decode($_person_data[$i]['value']), $blowfish_key);
                        //Store decrypted data:
                        $query = new DB_Update($tmp_table_name);
                        $query->addUpdateValue($opd_tmp['value'], base64_encode(modApiFunc("Crypto", "blowfish_encrypt", $decrypted_value, $new_blowfish_key)));
                        $query->addUpdateValue($opd_tmp['encrypted_secret_key'], $new_encrypted_blowfish_key);
                        $query->addUpdateValue($opd_tmp['rsa_public_key_asc_format'], $new_rsa_public_key_asc_format);
                        $query->WhereValue($opd_tmp['id'], DB_EQ, $_person_data[$i]['id']);
                        $application->db->getDB_Result($query);

                        $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId = $_person_data[$i]['id'] + 1;
                        $this->saveState(); //Don't lose reencrypted data and save correct number
                        //of the last processed record. Otherwise the timeout can occur during the
                        //SQL query and data in the session will be incorrect.
                    }

                    if($i >= sizeof($_person_data))
                    {
                        break;
                    }
                }
                else
                {
                    //Report an error: keys don't match.
                    $MessageResources = &$application->getInstance('MessageResources');
                    $msg = $MessageResources->getMessage('CRYPTO_RSA_PUBLIC_PRIVATE_KEYS_MISMATCH_DECRYPT_ERROR');
                    return array("error_msg" => $msg, "b_finished" => false, "progress_position" => 0.0);
                }

            }

            # get Person Info data. Total record count.
            $query = new DB_Select();
            $query->addSelectField($query->fCount('*'), 'count');
            $query->Where($opd_tmp['b_encrypted'], DB_EQ, "1");
            $query->WhereAnd();
            $query->Where($opd_tmp['id'], DB_LT, $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId);
            $result = $application->db->getDB_Result($query);
            $n_done = $result[0]['count'];

            return array("error_msg" => "", "b_finished" => false, "progress_position" => (1.0 * $n_done)/$n_total);
        }
    }

    /**
     * Replaces the old encrypted data with new data reencrypted with the new key.
     * The step of replacing the pair of RSA keys.
     */
    function ReplaceRSAKeyPairStep5SwapCurrentDataWithTmpData()
    {
        $table_name = "order_person_data";
        $tmp_table_name = "order_person_data" . $this->getTmpTableSuffix();
        new DB_Table_Move($tmp_table_name, $table_name);
        //: return a return code.
        return true;
    }

    /**
     * The step of replacing keys. It stores the id of the current reencrypted
     * record from order_person_data.
     */
    function setReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId($id)
    {
        $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId = $id;
    }

    /**
     * Restores the module state.
     */
    function loadState()
    {
        global $zone;
        if ($zone == "AdminZone")
        {
            if(modApiFunc('Session', 'is_Set', 'ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId'))
            {
                $this->setReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId(modApiFunc('Session', 'get', 'ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId'));
            }
            else
            {
                $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId = 0;
            }
        }
    }

    /**
     * Saves the module state.
     */
    function saveState()
    {
        global $zone;
        if ($zone == "AdminZone")
        {
            modApiFunc('Session', 'set', 'ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId', $this->ReplaceRSAKeyPairStep2ReencryptTmpDataOrderPersonDataId);
        }
    }

    /**
     * Creates copies of database tables when reencrypting data.
     * Their names differ from initial ones in the suffix, specified for this
     * module.
     */
    function getTmpTableSuffix()
    {
        return "_pm_offline_cc_tmp";
    }

    /**
     * It is called by the Checkout module to know which CCInfo variant to
     * output to the CZ. If the function is not defined, then the additional
     * prerequisite CCInfo for the payment module won't be created while
     * cz checkout.
     *
     * If the payment gateway needs a specific CreditCardInfo variant, it can
     * create it itself, add it to the database, as PaypalPro does it and
     * return a tag matching this new variant within this function.
     */
    function getAdditionalPersonInfoVariantTag($prerequisite_type)
    {
        //If necessary, do actions with PersonInfo Variants - add/change...
        if($prerequisite_type == "creditCardInfo")
        {
            return "default";
        }
        else
        {
            return false;
        }
    }

    /**
     * Does it store Credit Card Info in the database or it won't be used after
     * creating the order.
     */
    function storeCreditCardInfoInDB()
    {
        return true;
    }

    //text format
    function getRSAPublicKeyInASCFormat()
    {
        $this->getSettings();
        $key = $this->Settings["RSA_PUBLIC_KEY_ASC_FORMAT"];
        return $key;
    }

    // CryptRSA library format
    function getRSAPublicKeyInCryptRSAFormat()
    {
        $this->getSettings();
        $key = $this->Settings["RSA_PUBLIC_KEY_ASC_FORMAT"];
        $key_in_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $key);
        return $key_in_cryptrsa_format;
    }

    /**
     * Prepares and returns necessary data, passed to the payment gateway.
     */
    function getConfirmationData($orderId)
    {
        global $application;

        $this->getSettings();
        $confirmationData = array
            (
                "FormAction" => PAYMENT_OFFLINE_CC_URL
               ,"FormMethod" => "GET"
               ,"DataFields" => array
                    (
                        "asc_action" => "UpdatePaymentStatus"
                       ,"asc_oid" => $orderId
                       ,"status" => "return"
                    )
            );
        return $confirmationData;
    }

    /**
     */
    function processData($data, $order_id)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
        //the first step of data processing from the Payment Gateway
        //the payment status update.
        //the next step.
        //do not call the Payment Gateway.
        $DBPaymentStatus = array();
        $status = $data["_GET"]["status"];

        switch ($status)
        {
            case "return":
                $paymentStatusId = 1; //"Waiting"
                $EventType = "ConfirmationSuccess";
                $result = modApiFunc("Checkout", "UpdatePaymentStatusInDB", $order_id, $paymentStatusId,
                           $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_OFFLINE_CC_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_OFFLINE_CC_LOG_MESSAGE_AZ_006'));
                break;

            default:
                //: Report Error: e.g. write to order history
                $result = array("payment_status" => array());
                break;
        }

        return array("EventType" => $EventType, "statusChanged" => $result);
    }

    function gettime()
    {
        return $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_LOG_EVENT_TIME')
                                                   . modApiFunc("Localization", "timestamp_date_format", time())
                                                   ." "
                                                   . modApiFunc("Localization", "timestamp_time_format", time());
    }

    function getmicrotime()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");

        list($usec, $sec) = explode(" ",microtime());

        return $obj->getMessage('MODULE_PAYMENT_OFFLINE_CC_LOG_EVENT_TIME')
                                . (float)($usec + $sec) . " "
                                . $obj->getMessage('MODULE_PAYMENT_OFFLINE_CC_SECONDS');
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Offline_CC::getTables() instead of $this->getTables()
     */
    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");

        $tables = Payment_Module_Offline_CC::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pm_offline_cc_settings';            #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue("MODULE_NAME", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_NAME')).':"'.$obj->getMessage('MODULE_NAME').'";', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue("RSA_PUBLIC_KEY_ASC_FORMAT", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen("").':"'."".'";', $columns['value']);
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
               'Payment_Module_Offline_CC',
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
     *        OfflineCC                    1 PersonInfoType:
     * creditCardInfo
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
            $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");
            $msg = $this->MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_API_MSG_REMOVE_PERSON_INFO_TYPE');

            $value = NULL;
            switch($person_info_type_tag)
            {
                case "creditCardInfo":
                {
                    $value = $msg;
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

    /*static*/ function getUid()
    {
//        return "B5D83D96-73BE-4409-4388-EF8B82AA1BE0";
        include(dirname(__FILE__)."/includes/uid.php");
        return $uid;
    }

    function getInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");

        $this->getSettings();

        global $zone;
        $name = $this->Settings["MODULE_NAME"];

        return array("GlobalUniquePaymentModuleID" => $this->getUid(),
                     "Name"        => $name,
//                     "Description" => $this->Settings["MODULE_DESCR"],
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>",
                     //"Undefined", //NOT for active/inactive.
                     // there is special config in Checkout module to handle/store
                     // active/inactive flags.
                     "PreferencesAZViewClassName" => "offline_cc_input_az",
                     "CZInputViewClassName" => "CheckoutPaymentModuleOfflineCCInput",
                     "CZOutputViewClassName" => "CheckoutPaymentModuleOfflineCCOutput",
                     "APIClassName" => __CLASS__
                    );
    }
    /**
     * Clears the Settings table.
     */
    function clearSettingsInDB()
    {
        execQuery('DELETE__PM_OFFLINE_CC_SETTINGS', array());
    }

    /**
     * Gets current module settings from Settings.
     *
     * @return array - module settings array
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'pm_offline_cc_settings'));
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
        $columns = $tables['pm_offline_cc_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $params = array(
                            'key'   => $key,
                            'value' => serialize($value),
                           );
            execQuery('INSERT_PM_OFFLINE_CC_SETTINGS',$params);
        }
    }

    /**
     * Gets credit card info by the order.
     *
     * @return array - the array of module settings
     */
    function getOrderCCInfoFromDB($order_id)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['pm_offline_cc_order_cc_info']['columns'];

        $query = new DB_Select();
        $query->addSelectField($columns["key"], "set_key");
        $query->addSelectField($columns["value"], "set_value");
        $query->WhereValue($field['order_id'], DB_EQ, $order_id);
        $result = $application->db->getDB_Result($query);

        $res = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $res[$result[$i]['key']] = $result[$i]['value'];
        }
        return $res;
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
        $columns = $tables['pm_offline_cc_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $params = array('key' => $key,
                            'value' => serialize($value));
            execQuery('UPDATE_PM_OFFLINE_CC_SETTINGS', $params);
        }
    }

    function updateRSAPublicKey($rsa_public_key_asc_format)
    {
        $Settings = array(
                          "RSA_PUBLIC_KEY_ASC_FORMAT"  => $rsa_public_key_asc_format
                         );

        modApiFunc("Payment_Module_Offline_CC", "updateSettings", $Settings);
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
        return ($price == PRICE_N_A) ? 0.0 : $price;
    }

    /**
     * Uninstalls the module.
     * It deletes all module tables.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Offline_CC::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Payment_Module_Offline_CC::getTables());
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

        $settings = 'pm_offline_cc_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.pm_offline_cc_setting_id'
               ,'key'               => $settings.'.pm_offline_cc_setting_key'
               ,'value'             => $settings.'.pm_offline_cc_setting_value'
            );
        $tables[$settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'value'             => DBQUERY_FIELD_TYPE_TEXT
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

    var $CCInfo;

    /**#@-*/
}
?>