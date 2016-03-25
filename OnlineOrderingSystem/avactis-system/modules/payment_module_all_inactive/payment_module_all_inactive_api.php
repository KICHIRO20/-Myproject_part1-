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

//: move "defines" to member variables
define("PAYMENT_ALL_INACTIVE_URL", "checkout.php");
//define("PAYPAL_NOTIFY_URL", "https://www.sandbox.paypal.com");

/**
 *
 * @package PaymentModuleAllInactive
 * @author Vadim Lyalikov
 */
class Payment_Module_All_Inactive extends pm_sm_api
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * PaymentModuleAllInactive constructor.
     */
    function Payment_Module_All_Inactive()
    {
        global $application;
        $this->Settings = NULL;
        $obj = &$application->getInstance('MessageResources',"payment-module-all-inactive-messages", "AdminZone");

        //: gen real UID
        $info = $this->getInfo();

        $this->getSettings();

        $this->OrderHistoryMessageTag = $obj->getMessage('MODULE_PAYMENT_ALL_INACTIVE_LOG_MODULE_ID') . $info["GlobalUniquePaymentModuleID"] . "\n" .
        $this->Settings["MODULE_NAME"];// .
    }

    function getmicrotime()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-all-inactive-messages", "AdminZone");

        list($usec, $sec) = explode(" ",microtime());

        return $obj->getMessage('MODULE_PAYMENT_ALL_INACTIVE_LOG_EVENT_TIME') . (float)($usec + $sec) . " " . $obj->getMessage('MODULE_PAYMENT_ALL_INACTIVE_SECONDS');
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_AllInactive::getTables() instead of $this->getTables().
     */
    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-all-inactive-messages", "AdminZone");

        $tables = Payment_Module_All_Inactive::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pm_all_inactive_settings';            #
        $columns = $tables[$table]['columns'];  #

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue("MODULE_NAME", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_NAME')).':"'.$obj->getMessage('MODULE_NAME').'";', $columns['value']);
        $application->db->getDB_Result($query);
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
//        return "A50F9CD5-9F45-12CB-353C-03EC75493A0A";
        include(dirname(__FILE__)."/includes/uid.php");
        return $uid;
    }

    function getInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-all-inactive-messages", "AdminZone");

        $this->getSettings();

        return array("GlobalUniquePaymentModuleID" => $this->getUid(),
                     "Name"        => $this->Settings["MODULE_NAME"],
//                     "Description" => $this->Settings["MODULE_DESCR"],
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>",
                     //"Undefined", //NOT for active/inactive.
                     // there is special config in Checkout module to handle/store
                     // active/inactive flags.
//                     "PreferencesAZViewClassName" => "paypal_cc_input_az",
                     "CZInputViewClassName" => "CheckoutPaymentModuleAllInactiveInput",
                     "CZOutputViewClassName" => "CheckoutPaymentModuleAllInactiveOutput",
                     "APIClassName" => __CLASS__
                    );
    }
    /**
     * Clears the Settings table.
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('pm_all_inactive_settings');
        $application->db->getDB_Result($query);
    }

    /**
     * Gets current module settings from Settings.
     *
     * @return array - module settings array
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'pm_all_inactive_settings'));
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
        $columns = $tables['pm_all_inactive_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('pm_all_inactive_settings');
            $query->addInsertValue($key, $columns['key']);
            $query->addInsertValue(serialize($value), $columns['value']);
            $application->db->getDB_Result($query);

            $inserted_id = $application->db->DB_Insert_Id();
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
     * Prepares and returns necessary data, passed to the payment gateway.
     *
     * @ not all data is defined
     */
    function getConfirmationData($orderId)
    {
        global $application;

        $this->getSettings();
        $confirmationData = array
            (
                "FormAction" => PAYMENT_ALL_INACTIVE_URL
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
     * Processes data on updating the order status, come from the payment gateway.
     * The flag &$bStop is set by the payment module. If it is true on return,
     * then Checkout module stops the data process, come from the payment gateway.
     */
    function processData($data, $order_id /*, &$DBPaymentStatus, &$bStop, &$bPaymentFinished, &$EventType */)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-all-inactive-messages", "AdminZone");
        //                                Payment Gateway
        //                       .
        //                   .
        //  Payment Gateway                   .
        $DBPaymentStatus = array();
        $status = $data["_GET"]["status"];

        switch ($status)
        {
            case "return":
                $paymentStatusId = 1; //"Waiting"
                $EventType = "ConfirmationSuccess";
                $result = modApiFunc("Checkout", "UpdatePaymentStatusInDB", $order_id, $paymentStatusId,
                           $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" . $obj->getMessage('MODULE_PAYMENT_ALL_INACTIVE_LOG_EVENT_DESCRIPTION') . $obj->getMessage('MODULE_PAYMENT_ALL_INACTIVE_LOG_MESSAGE_AZ_006'));
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
     * Payment_Module_AllInactive_CC::getTables() instead of $this->getTables().
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Payment_Module_All_Inactive::getTables());
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

        $settings = 'pm_all_inactive_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.pm_all_inactive_setting_id'
               ,'key'               => $settings.'.pm_all_inactive_setting_key'
               ,'value'             => $settings.'.pm_all_inactive_setting_value'
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
     * A flag, which is stored in the database. It indicates whether the specified
     * module is mapped in CZ Checkout.
     */
    var $bActive;

    var $OrderHistoryMessageTag;

    var $Settings;
    /**#@-*/

}
?>