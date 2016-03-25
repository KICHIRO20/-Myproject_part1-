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
 * @package PaymentModuleCod
 * @author Egor Makarov
 */
class Payment_Module_Cod extends pm_sm_api
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * PaymentModuleCod  constructor.
     */
    function Payment_Module_Cod()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");
        //: gen real UID
        $info = $this->getInfo();

        $settings = $this->getSettings();
        $this->Settings = $settings;

        $this->OrderHistoryMessageTag = $obj->getMessage('MODULE_PAYMENT_COD_LOG_MODULE_ID') .
                $info["GlobalUniquePaymentModuleID"] . "\n" .
                $this->Settings["MODULE_NAME"];
    }


    function getmicrotime()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");

        list($usec, $sec) = explode(" ", microtime());

        return $obj->getMessage('MODULE_PAYMENT_COD_LOG_EVENT_TIME') .
                (float)($usec + $sec) . " " .
                $obj->getMessage('MODULE_PAYMENT_COD_SECONDS');
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
        $obj = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");

        $tables = Payment_Module_Cod::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pm_cod_settings';              //
        $columns = $tables[$table]['columns'];   //

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue("MODULE_NAME", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_NAME')).':"'.$obj->getMessage('MODULE_NAME').'";', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue("PER_ORDER_SHIPPING_FEE", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen("0.00").':"'. "0.00" .'";', $columns['value']);
        $application->db->getDB_Result($query);
/*
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue("MODULE_DESCR", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_DESCR')).':"'.$obj->getMessage('MODULE_DESCR').'";', $columns['value']);
        $application->db->getDB_Result($query);
*/
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
//        return "AAE28225-3EEF-58E9-318F-92FCA6766E6B";
        include(dirname(__FILE__)."/includes/uid.php");
        return $uid;
    }

    function getInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");

        $settings = $this->getSettings();
        $this->Settings = $settings;

        return array("GlobalUniquePaymentModuleID" => $this->getUid(),
                     "Name"        => $this->Settings["MODULE_NAME"],
//                     "Description" => $this->Settings["MODULE_DESCR"],
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>",

					 "PreferencesAZViewClassName" => "CheckoutPaymentModuleCodInputAZ",
                     "CZInputViewClassName" => "CheckoutPaymentModuleCodInput",
                     "CZOutputViewClassName" => "CheckoutPaymentModuleCodOutput",
                     "APIClassName" => __CLASS__
                    );
    }

    /**
     * Clears the Settings table.
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('pm_cod_settings');
        $application->db->getDB_Result($query);
    }

    /**
     * Gets current module settings from Settings.
     *
     * @return array - module settings array
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'pm_cod_settings'));
        $Settings = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $Settings[$result[$i]['set_key']] = unserialize($result[$i]['set_value']);
        }
        if (isset($this))
        {
            $this->Settings = $Settings;
        }
        return $Settings;
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
        $columns = $tables['pm_cod_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('pm_cod_settings');
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
        $columns = $tables['pm_cod_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('pm_cod_settings');
            $query->addUpdateValue($columns['value'], serialize($value));
            $query->WhereValue($columns['key'], DB_EQ, $key);
            $application->db->getDB_Result($query);
        }
    }

    /**
     *                      Shipping&Handling.                                     FS_OO, FH_OO.
     */
    function getPerOrderPaymentModuleShippingFee()
    {
        $settings = Payment_Module_Cod::getSettings();
        return floatval($settings["PER_ORDER_SHIPPING_FEE"]);
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
     * Prepares and returns necessary data, passed to the payment gateway.
     *
     * @ not all data is defined
     */
    function getConfirmationData($orderId)
    {
        global $application;

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction("UpdatePaymentStatus");
        $request->setKey("asc_oid", $orderId);
        $self_link = $request->getURL();

        $confirmationData = array
            (
                "FormAction" => $self_link
                ,"FormMethod" => "POST"
                ,"DataFields" => array()
            );

        $msg = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");
        $this->addRequestLog("LOG_PM_INPUT", "Payment Module Logs", $msg->getMessage("MODULE_PAYMENT_COD_TIMELINE_HEADER"), $confirmationData);

        return $confirmationData;
    }


    /**
     * Processes data on updating the order status, come from the payment gateway.
     * The flag &$bStop is set by the payment module. If it is true on return,
     * then Checkout module stops the data process, come from the payment gateway.
     */
    function processData($data, $order_id)
    {
        global $application;
        $msg = &$application->getInstance('MessageResources',"payment-module-cod-messages", "AdminZone");

        //
        // The checking on the delivery to the selected place might be
        // added here
        //

        $EventType = "ConfirmationSuccess";
        $result = modApiFunc("Checkout", "addOrderHistory", $order_id,
            $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" .
            $msg->getMessage('MODULE_PAYMENT_COD_LOG_EVENT_DESCRIPTION') .
            $msg->getMessage('MODULE_PAYMENT_COD_LOG_MESSAGE_INCOMMING'));

        return array("EventType" => $EventType, "statusChanged" => $result);
    }

    /**
     * Uninstalls the module.
     * It deletes all module tables.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Paypal_CC::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Payment_Module_Cod::getTables());
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

        $settings = 'pm_cod_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.pm_cod_setting_id'
               ,'key'               => $settings.'.pm_cod_setting_key'
               ,'value'             => $settings.'.pm_cod_setting_value'
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