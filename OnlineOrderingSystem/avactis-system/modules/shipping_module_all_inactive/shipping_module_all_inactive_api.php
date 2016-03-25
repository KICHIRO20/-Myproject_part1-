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
 * Shipping Module "All Inactive".
 *
 * @package ShippingModuleAllInactive
 * @author Girin Alexander
 */
class Shipping_Module_All_Inactive extends pm_sm_api
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * ShippingModuleAllInactive constructor.
     */
    function Shipping_Module_All_Inactive()
    {
        $this->Settings = null;
    }

    function getUID()
    {
        return "6F82BA03-C5B1-585B-CE2E-B8422A1A19F6";
    }

    /**
     * Returns module info.
     */
    function getInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"shipping-module-all-inactive-messages", "AdminZone");

        $this->getSettings();

        return array("GlobalUniqueShippingModuleID" => $this->getUID(),
                     "Name"        => $this->Settings["MODULE_NAME"],
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>", //NOT for active/inactive.
                     // there is special config in Checkout module to handle/store
                     // active/inactive flags.
                     "PreferencesAZViewClassName" => NULL, //"all_inactive_input_az",
                     "APIClassName" => __CLASS__
                    );
    }

    function getShippingMethodInfo($method_id,$and_calc=false)
    {
        if($method_id == $this->single_available_method_id)
        {
            return array(
                        //'module_id' => $this->getUID(),
                        'id' => $this->single_available_method_id,
                        // Single available method. //$this->getUID(),
                        //@ do a separate resourse for the method name
                        'method_name' => $this->Settings["MODULE_NAME"],
                        'method_code' => '',
                        'destination' => 'UNDEFINED',
                        'available' => $this->IsActive() ? 'Y' : 'N',
                        'cost' => ( $and_calc ? $this->getShippingCost() : PRICE_N_A )
                        );
        }
        else
        {
            return array();
        }
    }

    function getSingleAvailableMethodId()
    {
        return $this->single_available_method_id;
    }

    /**
     *
     */
    function getShippingMethods($type="ALL", $and_calc = false)
    {
        global $application;

       /* The $type="AVAILABLE" condition in this module equals the whole
           module, which is isActive.
        */
        if($type == "AVAILABLE" &&
           $this->isActive() === false)
        {
            return array();
        }

        $info = $this->getShippingMethodInfo($this->single_available_method_id, $and_calc);

       /* If the parameter $and_calc == true, the shipping cost should be
           determined.
        */

        if($and_calc === true &&
           $info["cost"] == PRICE_N_A)
        {
            return array();
        }

        return array($info);
    }


    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_All_Inactive::getTables() instead of $this->getTables().
     */
    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"shipping-module-all-inactive-messages", "AdminZone");

        $tables = Shipping_Module_All_Inactive::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'sm_all_inactive_settings';            #
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
        $active_modules = modApiFunc("Checkout","getActiveModules", "shipping");
        return array_key_exists($this->getUid(), $active_modules);
    }

    /**
     * This method is invoked by Checkout. It returns the cost of the selected
     * shipping method. Some shipping modules can have several shipping methods
     * (e.g. by airplane, by train etc.), the other have only one.
     * 'All Inactive' has one method. The result is always the same
     * "the cost is not defined" ("N/A").
     */
    function getShippingCost($method_id="")
    {
        return 0;// it is used to output the modules in CZ. The unification. PRICE_N_A;
    }

    /**
     * Uninstalls the module.
     * It deletes all module tables.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_All_Inactive::getTables() instead of $this->getTables().
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Shipping_Module_All_Inactive::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Returns true if in this shipping module the shipping method with the
     * specified id exists, false otherwise.
     */
    function isValidShippingMethodId($method_id)
    {
        if($method_id == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
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

        $settings = 'sm_all_inactive_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.sm_all_inactive_setting_id'
               ,'key'               => $settings.'.sm_all_inactive_setting_key'
               ,'value'             => $settings.'.sm_all_inactive_setting_value'
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

    /**
     * Clears the Settings table.
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('sm_all_inactive_settings');
        $application->db->getDB_Result($query);
    }

    /**
     * Gets current module settings from Settings.
     *
     * @return array - module settings array
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'sm_all_inactive_settings'));
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
     * @param array $Settings - module settins array.
     */
    function setSettings($Settings)
    {
        global $application;
        $this->clearSettingsInDB();
        $tables = $this->getTables();
        $columns = $tables['sm_all_inactive_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('sm_all_inactive_settings');
            $query->addInsertValue($key, $columns['key']);
            $query->addInsertValue(serialize($value), $columns['value']);
            $application->db->getDB_Result($query);

            $inserted_id = $application->db->DB_Insert_Id();
        }
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    //The ID of the single available method in this module.
    var $single_available_method_id = 1;

    /**
     * A flag, which is stored in the database. It indicates, if the specified
     * module is mapped in CZ Checkout.
     */
    var $bActive;

    var $Settings;

    /**#@-*/

}
?>