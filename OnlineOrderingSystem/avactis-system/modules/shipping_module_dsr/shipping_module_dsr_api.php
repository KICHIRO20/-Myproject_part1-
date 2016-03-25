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
 * Shipping Module "Custom Shipping Rates"
 *
 * @package ShippingModuleDSR
 * @author Egor V. Derevyankin
 */
class Shipping_Module_DSR extends pm_sm_api
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * ShippingModuleDSR  constructor.
     */
    function Shipping_Module_DSR()
    {
    }

    function getUID()
    {
        return "BCE5D24D-666C-43CA-94A0-D6F775903BE2";
    }

    /**
     * @return Info about this shipping module
     */
    function getInfo()
    {

        global $application;
        $obj = &$application->getInstance('MessageResources',"shipping-module-dsr-messages", "AdminZone");

        return array("GlobalUniqueShippingModuleID" => $this->getUID(),
                     "Name"          => $obj->getMessage('MODULE_NAME'),
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>", //NOT for active/inactive.
                     "PreferencesAZViewClassName" => "dsr_input_az",
                     "APIClassName" => __CLASS__
                    );
    }

    /**
     * Checks the requirments.
     *
     * @return array with the failed requirments
     */
    function checkRequirments()
    {
        $return=array();

        return $return;
    }

    /**
     * Module installator.
     */
    function install()
    {
        _use(dirname(__FILE__).'/includes/install.inc');

        //                                RemovePersonInfoType
        //                   ,          -     CheckoutEditor
        //             PersonInfoType,         , BillingInfo, ShippingInfo,
        //  CustomerInfo, CreditCardInfo, BankAccountInfo, PaymentMethodInfo (
        //             ), ShippingMethodInfo (              ).
        //       ShippingModule'                                                 ,
        //                                     .
        //                    ,                 PersonInfoType          .
        //
        //  PersonInfoType.
        modApiFunc('EventsManager',
               'addEventHandler',
               'RemovePersonInfoTypeEvent',
               'Shipping_Module_DSR',
               'OnRemovePersonInfoType');
    }

    /**
     *                    RemovePersonInfoType.
     *                  ,          -     CheckoutEditor
     *            PersonInfoType,         , BillingInfo, ShippingInfo,
     * CustomerInfo, CreditCardInfo, BankAccountInfo, PaymentMethodInfo (
     *            ), ShippingMethodInfo (              ).
     *      ShippingModule'                                                 ,
     *                                    .
     *                   ,                 PersonInfoType          .
     *
     * PersonInfoType.
     *
     *        DSR                         PersonInfoType:
     * shippingInfo
     *
     * @param string $person_info_type_tag - tag      person info type' ,
     *                      (turn off)   CheckoutFormEditor' .
     *
     * @return mixed - NULL,                       - "          ",       -
     *                                     ,            ,
     *        (                                   ).
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
            $this->MessageResources = &$application->getInstance('MessageResources',"shipping-module-dsr-messages", "AdminZone");
            $msg = $this->MessageResources->getMessage('MODULE_SHIPPING_DSR_API_MSG_REMOVE_PERSON_INFO_TYPE');

            $value = NULL;
            switch($person_info_type_tag)
            {
                case "shippingInfo":
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
     * @return true if module is active or false if it is not
     */
    function isActive()
    {
        global $application;
        $active_modules = modApiFunc("Checkout","getActiveModules", "shipping");
        return array_key_exists($this->getUid(), $active_modules);
    }

    function _addShippingMethod($method_name)
    {
        if($method_name=="")
            return false;

        global $application;
        $tables = $this->getTables();
        $table = 'sm_dsr_methods';
        $columns = $tables[$table]['columns'];
        $query = new DB_Insert($table);
        $query -> addMultiLangInsertValue($method_name, $columns['method_name'],
                                          $columns['id'], 'Shipping_Module_DSR');
        $application->db->getDB_Result($query);

        return $application->db->DB_Insert_Id();
    }

    function _cloneShippingMethod($method_id)
    {
        global $application;
        $_methods = $this->getShippingMethods();
        $method = -1;
        foreach ($_methods as $m)
        {
            if ($m["id"] == $method_id)
            {
                $method = $m;
                break;
            }
        }
        if ($method == -1)
            return false;

        $rates = $this->getShippingRates($method_id);

        $tables = $this->getTables();

        $tables = $this->getTables();
        $table = 'sm_dsr_methods';
        $columns = $tables[$table]['columns'];
        $query = new DB_Insert($table);
        $query -> addMultiLangInsertValue($method["method_name"],
                                          $columns['method_name'],
                                          $columns['id'],
                                          'Shipping_Module_DSR');
        $query->addInsertValue($method["method_code"], $columns['method_code']);
        $query->addInsertValue($method["destination"], $columns['destination']);
        $query->addInsertValue($method["available"], $columns['available']);
        $application->db->getDB_Result($query);

        $new_method_id = $application->db->DB_Insert_Id();

        foreach ($rates as $rate)
        {
            $rate_data = array(
                     "country_id"   => $rate["dst_country"]
                    ,"state_id"     => $rate["dst_state"]
                    ,"rate_data"    => array(
                             "wrange_from"      => $rate["wrange_from"]
                            ,"wrange_to"        => $rate["wrange_to"]
                            ,"bcharge_abs"      => $rate["bcharge_abs"]
                            ,"bcharge_perc"     => $rate["bcharge_perc"]
                            ,"acharge_pi_abs"   => $rate["acharge_pi_abs"]
                            ,"acharge_pwu_abs"  => $rate["acharge_pwu_abs"]
                        )
                );
            $this->_addShippingRate($new_method_id, $rate_data);
        }

        return $new_method_id;
    }

    function _deleteShippingMethod($method_id)
    {
        global $application;
        $tables = $this->getTables();

        // delete method
        $table = 'sm_dsr_methods';
        $columns = $tables[$table]['columns'];
        $query = new DB_Delete($table);
        $query -> deleteMultiLangField($columns['method_name'],
                                       $columns['id'], 'Shipping_Module_DSR');
        $query->Where($columns['id'], DB_EQ, $method_id);
        $application->db->getDB_Result($query);

        // delete rates of deleted method
        $table = 'sm_dsr_rates';
        $columns = $tables[$table]['columns'];
        $query = new DB_Delete($table);
        $query->Where($columns['method_id'], DB_EQ, $method_id);
        $application->db->getDB_Result($query);

        if(count($this->getShippingMethods('AVAILABLE')) == 0)
            //$this->setActive(false);
            modApiFunc("Checkout", "setModuleActive", (modApiFunc("Shipping_Module_DSR", "getUid")), false);
    }

    function _renameShippingMethod($method_id, $method_name)
    {
        if($method_name == "")
            return false;

        global $application;
        $tables = $this->getTables();
        $table = 'sm_dsr_methods';
        $columns = $tables[$table]['columns'];
        $query = new DB_Update($table);
        $query -> addMultiLangUpdateValue($columns['method_name'], $method_name,
                                          $columns['id'], $method_id,
                                          'Shipping_Module_DSR');
        $query->Where($columns['id'], DB_EQ, $method_id);

        $application->db->getDB_Result($query);

        return true;
    }

    function _addShippingRate($method_id, $new_rate_data)
    {
        global $application;

        $return=array();
        if(!$this->isValidShippingMethodId($method_id))
            $return[]="E_INVALID_MID";

        if(!in_array($new_rate_data["state_id"],array_keys(modApiFunc("Location","getStates",$new_rate_data["country_id"]))) &&
           $new_rate_data["state_id"] != ALL_OTHER_STATES_STATE_ID &&
           !($new_rate_data["state_id"] == STATE_UNDEFINED_STATE_ID && $new_rate_data["country_id"] == ALL_OTHER_COUNTRIES_COUNTRY_ID))
            $return[]="E_INVALID_COUNTRY_STATE";

        $rate_data=$new_rate_data["rate_data"];
        $rate_data=array_map("trim",$rate_data);
        $rate_data=array_map(array("Shipping_Module_DSR","replace_coma"),$rate_data);
        $rate_data=array_map("abs",$rate_data);
        $rate_data=array_map("floatval",$rate_data);

        if($rate_data["wrange_from"]>$rate_data["wrange_to"] or $rate_data["wrange_to"]==0)
            $return[]="E_INVALID_WRANGE";

        if(!empty($return))
            return $return;

        $result = execQuery('SELECT_SM_DSR_RATES',array('ApiClassName' => __CLASS__, 'method_id' => $method_id, 'new_rate_data' => $new_rate_data, 'rate_data' => $rate_data));

        if(!empty($result))
            $return[]="E_INVALID_WRANGE_EXISTS";

        if(empty($return))
        {
            global $application;
            $tables = $this->getTables();
            $table = 'sm_dsr_rates';
            $columns = $tables[$table]['columns'];

            $query = new DB_Insert('sm_dsr_rates');
            $query->addInsertValue($method_id,$columns['method_id']);
            $query->addInsertValue($new_rate_data["country_id"],$columns['dst_country']);
            $query->addInsertValue($new_rate_data["state_id"],$columns['dst_state']);
            $query->addInsertValue($rate_data["wrange_from"],$columns['wrange_from']);
            $query->addInsertValue($rate_data["wrange_to"],$columns['wrange_to']);
            $query->addInsertValue($rate_data["bcharge_abs"],$columns['bcharge_abs']);
            $query->addInsertValue($rate_data["bcharge_perc"], $columns['bcharge_perc']);
            $query->addInsertValue($rate_data["acharge_pi_abs"],$columns['acharge_pi_abs']);
            $query->addInsertValue(0.00 /*$rate_data["acharge_pi_perc"]*/,$columns['acharge_pi_perc']);
            $query->addInsertValue($rate_data["acharge_pwu_abs"],$columns['acharge_pwu_abs']);
            $query->addInsertValue(0.00 /*$rate_data["acharge_pwu_perc"]*/,$columns['acharge_pwu_perc']);
            $application->db->getDB_Result($query);
        }

        return $return;
    }

    function _deleteShippingRate($rate_id)
    {
        global $application;
        $tables = $this->getTables();
        $table = 'sm_dsr_rates';
        $columns = $tables[$table]['columns'];
        $query = new DB_Delete($table);
        $query->Where($columns['rate_id'], DB_EQ, $rate_id);
        $application->db->getDB_Result($query);
    }

    function getShippingRates($method_id)
    {
        $result = execQuery('SELECT_SM_DSR_RATES_BY_METHOD',array('ApiClassName' => __CLASS__, 'method_id' => $method_id));
        return $result;
    }

    /**
     * Uninstalls the module.
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Shipping_Module_DSR::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * @param $method_id ID of shipping method of this module
     * @return true if ID is valid or false if it is not
     */
    function isValidShippingMethodId($method_id)
    {
        $check_result = execQuery('SELECT_SM_DSR_METHODS_BY_METHOD',array('ApiClassName' => __CLASS__, 'method_id' => $method_id));

        if(isset($check_result[0]['id']))
            return true;
        else
            return false;
    }

    /**
     * @param $method_id ID of shipping method of this module
     * @param $and_calc true if it is necessary to calculate the shipping cost
     *         or false if it is not
     * @return array with the shipping method info (id,method_name,method_code,
     *         destination,available,cost)
     */
    function getShippingMethodInfo($method_id,$and_calc=false)
    {
        $select_result = execQuery('SELECT_SM_DSR_METHODS_BY_METHOD_EXT',array('ApiClassName' => __CLASS__, 'method_id' => $method_id));

        $method=$select_result[0];
        $method["cost"]=PRICE_N_A;

        if($and_calc!=false)
        {
            //$ShippingInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo");
            //$pak_weight=modApiFunc("Checkout", "getOrderWeight", "netto");
            $ShippingInfo = modApiFunc("Shipping_Cost_Calculator","getShippingInfo");
            $pak_weight = modApiFunc("Shipping_Cost_Calculator","getPackWeight");
            $rate=$this->getRateFromCache($ShippingInfo,$pak_weight,$method_id);
            if($rate!=false)
                $method["cost"]=$rate;
            else
            {
                $pak_weight=modApiFunc("Shipping_Cost_Calculator","getPackWeight");
                $Subtotal=modApiFunc("Shipping_Cost_Calculator","getCartSubtotal");
                $ItemsCount=modApiFunc("Shipping_Cost_Calculator","getItemsCount");
                $method["cost"] = $this->calcMethodCost($method_id, $ShippingInfo, $pak_weight, $Subtotal, $ItemsCount);
            }
        };

        return $method;

    }

    /**
     * @return structure of the tables of this module
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $settings = 'sm_dsr_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.sm_dsr_setting_id'
               ,'key'               => $settings.'.sm_dsr_setting_key'
               ,'value'             => $settings.'.sm_dsr_setting_value'
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

        $methods = 'sm_dsr_methods';
        $tables[$methods] = array();
        $tables[$methods]['columns'] = array
            (
                'id'                => $methods.'.sm_dsr_method_id'
               ,'method_code'       => $methods.'.sm_dsr_method_code'
               ,'method_name'       => $methods.'.sm_dsr_method_name'
               ,'destination'       => $methods.'.sm_dsr_method_destination'
               ,'available'         => $methods.'.sm_dsr_method_available'
            );
        $tables[$methods]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'method_code'       => DBQUERY_FIELD_TYPE_CHAR20
               ,'method_name'       => DBQUERY_FIELD_TYPE_CHAR255
               ,'destination'       => DBQUERY_FIELD_TYPE_CHAR1 .' default \'L\''
               ,'available'         => DBQUERY_FIELD_TYPE_CHAR1 .' default \'N\''
            );
        $tables[$methods]['primary'] = array
            (
                'id'
            );

        $rates = 'sm_dsr_rates';
        $tables[$rates] = array();
        $tables[$rates]['columns'] = array
            (
                 'rate_id'            => $rates.'.rate_id'
                ,'method_id'          => $rates.'.method_id'
                ,'dst_country'        => $rates.'.dst_country'
                ,'dst_state'          => $rates.'.dst_state'
                ,'wrange_from'        => $rates.'.wrange_from'
                ,'wrange_to'          => $rates.'.wrange_to'
                ,'bcharge_abs'        => $rates.'.bcharge_abs'
                ,'bcharge_perc'       => $rates.'.bcharge_perc'
                ,'acharge_pi_abs'     => $rates.'.acharge_pi_abs'
                ,'acharge_pwu_abs'    => $rates.'.acharge_pwu_abs'
                ,'acharge_pi_perc'    => $rates.'.acharge_pi_perc'
                ,'acharge_pwu_perc'   => $rates.'.acharge_pwu_perc'
            );
        $tables[$rates]['types'] = array
            (
                 'rate_id'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
                ,'method_id'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
                ,'dst_country'        => DBQUERY_FIELD_TYPE_CHAR5 .' NOT NULL DEFAULT \'\''
                ,'dst_state'          => DBQUERY_FIELD_TYPE_CHAR5 .' NOT NULL DEFAULT \'\''
                ,'wrange_from'        => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'wrange_to'          => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'bcharge_abs'        => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'bcharge_perc'       => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'acharge_pi_abs'     => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'acharge_pwu_abs'    => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'acharge_pi_perc'    => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
                ,'acharge_pwu_perc'   => DBQUERY_FIELD_TYPE_DECIMAL20_5 .' NOT NULL DEFAULT 0'
            );
        $tables[$rates]['primary'] = array
            (
                'rate_id'
            );


        $rates_cache = 'sm_dsr_rates_cache';
        $tables[$rates_cache] = array();
        $tables[$rates_cache]['columns'] = array
            (
                'id'                => $rates_cache.'.sm_dsr_crate_id'
               ,'hash'              => $rates_cache.'.sm_dsr_crate_hash'
               ,'method_id'         => $rates_cache.'.sm_dsr_crate_method_id'
               ,'rate'              => $rates_cache.'.sm_dsr_crate_rate'
               ,'expire'            => $rates_cache.'.sm_dsr_crate_expire'
            );
        $tables[$rates_cache]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'hash'              => DBQUERY_FIELD_TYPE_CHAR50
               ,'method_id'         => DBQUERY_FIELD_TYPE_INT
               ,'rate'              => DBQUERY_FIELD_TYPE_DECIMAL20_5
               ,'expire'            => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$rates_cache]['primary'] = array
            (
                'id'
            );


        global $application;
        return $application->addTablePrefix($tables);

    }

    /**
     *
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('sm_dsr_settings');
        $application->db->getDB_Result($query);
    }

    /**
     * @return array of the settings of this module
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'sm_dsr_settings'));
        $this->Settings = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $this->Settings[$result[$i]['set_key']] = unserialize($result[$i]['set_value']);
        }
        return $this->Settings;
    }

    /**
     * Sets settings for this module.
     *
     * @param $Settings array of all the settings of this module
     */
    function setSettings($Settings)
    {
        global $application;
        $this->clearSettingsInDB();
        $tables = $this->getTables();
        $columns = $tables['sm_dsr_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('sm_dsr_settings');
            $query->addInsertValue($key, $columns['key']);
            $query->addInsertValue(serialize($value), $columns['value']);
            $application->db->getDB_Result($query);

            $inserted_id = $application->db->DB_Insert_Id();
        }
    }

    /**
     * Updates settings for this module.
     *
     * @param $Settings array of the updated settings of this module
     */
    function updateSettings($Settings)
    {

        global $application;
        $tables = $this->getTables();
        $columns = $tables['sm_dsr_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('sm_dsr_settings');
            $query->addUpdateValue($columns['value'], serialize($value));
            $query->WhereValue($columns['key'], DB_EQ, $key);
            $application->db->getDB_Result($query);
        }

    }

    /**
     * @param $type type of the requested methods (ALL|AVAILABLE|NOT_AVAILABLE)
     * @param $and_calc true if it is necessary to calculate the shipping cost
     *        or false if it is not
     * @return array of the shipping methods arrays, every will be as
     *        (id,method_name,method_code,destination,available,cost)
     */
    function getShippingMethods($type="ALL",$and_calc=false)
    {
        $available_condition=($type=="AVAILABLE"?"'Y'":($type=="NOT_AVAILABLE"?"'N'":""));
        $methods = execQuery('SELECT_SM_DSR_METHODS_BY_AVAILABILITY',array('ApiClassName' => __CLASS__, 'available_condition' => $available_condition));
        if($and_calc!=false and !empty($methods))
        {
            if($this->is_debug==true)
                $this->_debug_info[]=array("Calculation type","Offline by custom shipping rates");

            /*
            if($this->is_debug==true)
            {
                $ShippingInfo = modApiFunc("Shipping_Tester","getShippingInfo");
                $pak_weight=modApiFunc("Shipping_Tester","getPackWeight");
            }
            else
            {
                $ShippingInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo");
                $pak_weight=modApiFunc("Checkout", "getOrderWeight", "netto");
            }
            */

            $ShippingInfo = modApiFunc("Shipping_Cost_Calculator","getShippingInfo");
            $pak_weight=modApiFunc("Shipping_Cost_Calculator","getPackWeight");
            $Subtotal=modApiFunc("Shipping_Cost_Calculator","getCartSubtotal");
            $ItemsCount=modApiFunc("Shipping_Cost_Calculator","getItemsCount");

            $cmethods=array();

            foreach($methods as $k => $method_info)
            {
                if($this->is_debug==true)
                    $this->_debug_info[]=array("Try to calculate for", $method_info['method_name']);

                $method_cost=$this->calcMethodCost($method_info['id'],$ShippingInfo,$pak_weight,$Subtotal,$ItemsCount);

                if($this->is_debug==true)
                {
                    if($method_cost==PRICE_N_A)
                        $this->_debug_info[]=array("Result","Delivery is not available");
                    else
                        $this->_debug_info[]=array("Result",modApiFunc('Localization','currency_format',$method_cost));

                }

                if($method_cost!=PRICE_N_A)
                    $cmethods[]=array_merge($method_info,array("cost"=>$method_cost,"days"=>0));
            }

            $methods=$cmethods;

            $this->saveRatesToCache($ShippingInfo,$pak_weight,$methods);
        };

        return $methods;
    }

    /**
     * @param $update_array array if the IDs of the available methods,
     *      all other methods will be set to 'not available'
     */
    function _updateShippingMethods($update_array)
    {
        global $application;

        $tables=$this->getTables();
        $methods_table=$tables['sm_dsr_methods']['columns'];

        $query = new DB_Update('sm_dsr_methods');
        $query->addUpdateValue($methods_table['available'],"N");
        $application->db->getDB_Result($query);

        if(count($update_array)==0 or !is_array($update_array))
            return;

        $query = new DB_Update('sm_dsr_methods');
        $query->addUpdateValue($methods_table['available'],"Y");
        $query->Where($methods_table['id'], DB_IN, "('".implode("','",array_keys($update_array))."')");
        $application->db->getDB_Result($query);

        return;
    }

    /**
     * @param $s_info shipping info of the destination
     * @param $weight weight of the package
     * @param $rates array of the shipping rates for saving
     */
    function saveRatesToCache($s_info,$weight,$rates)
    {
        if($s_info["isMet"]==false or empty($rates))
            return;

        $s_country=modApiFunc("Location","getCountry",$s_info["validatedData"]["Country"]["value"]);
        $s_zipcode=$s_info["validatedData"]["Postcode"]["value"];
        $s_name = isset($s_info["validatedData"]["Firstname"]["value"]) ? $s_info["validatedData"]["Firstname"]["value"] : '';
        $s_name.= isset($s_info["validatedData"]["Lastname"]["value"])  ? $s_info["validatedData"]["Lastname"]["value"]  : '';

        $hash=md5($s_country.$s_name.$s_zipcode.$weight);

        $tables=$this->getTables();
        $rc_table=$tables['sm_dsr_rates_cache']['columns'];

        global $application;

        $query = new DB_Delete('sm_dsr_rates_cache');
        $query->Where($rc_table['hash'], DB_EQ, "'$hash'");
        $application->db->getDB_Result($query);

        $query = new DB_Delete('sm_dsr_rates_cache');
        $query->Where($rc_table['expire'], DB_LT, time());
        $application->db->getDB_Result($query);

        foreach($rates as $key => $rate_info)
        {
            $query = new DB_Insert('sm_dsr_rates_cache');
            $query->addInsertValue($hash, $rc_table["hash"]);
            $query->addInsertValue($rate_info["id"],$rc_table["method_id"]);
            $query->addInsertValue($rate_info["cost"],$rc_table["rate"]);
            $query->addInsertValue(time()+18000,$rc_table["expire"]);
            $application->db->getDB_Result($query);
        };

        return;
    }

    /**
     * @param $s_info shipping info of the destination
     * @param $weight weight of the package
     * @param $method_id ID of the required shipping method
     * @return cost of the shipping from the cache
     *      or false if the requested cost is not saved in the cache
     */
    function getRateFromCache($s_info,$weight,$method_id)
    {
        if($s_info["isMet"]==false or !is_numeric($method_id))
            return false;

        $s_country=modApiFunc("Location","getCountry",$s_info["validatedData"]["Country"]["value"]);
        $s_zipcode=$s_info["validatedData"]["Postcode"]["value"];
        $s_name = isset($s_info["validatedData"]["Firstname"]["value"]) ? $s_info["validatedData"]["Firstname"]["value"] : '';
        $s_name.= isset($s_info["validatedData"]["Lastname"]["value"])  ? $s_info["validatedData"]["Lastname"]["value"]  : '';

        $hash=md5($s_country.$s_name.$s_zipcode.$weight);

        $cached = execQuery('SELECT_SM_DSR_CACHED_RATES',array('ApiClassName' => __CLASS__, 'hash' => $hash, 'method_id' => $method_id));
        if(isset($cached[0]["rate"]))
            return $cached[0]["rate"];
        else
            return false;
    }

    /**
     * @param $method_id inner shipping method ID
     * @param $ShippingInfo shipping info of the destination
     * @param $pak_weight weight of the package
     * @param $Subtotal subtotal of the cart
     * @param $ItemsCount count of the products in the cart
     * @return delivery cost for the shipping method
     */
    function calcMethodCost($method_id, $ShippingInfo, $pak_weight, $Subtotal, $ItemsCount)
    {
        global $application;
        $shipping_cost=PRICE_N_A;

        $settings = $this->getSettings();
        switch ($settings["RATE_UNIT"])
        {
            case "currency":
                $rate_unit = $Subtotal;
                break;

            case "weight":
                $rate_unit = $pak_weight;
                if ($rate_unit <= 0)
                    $rate_unit = 1;
                break;

            case "item":
                $rate_unit = $ItemsCount;
                break;

            default:
                $rate_unit = $Subtotal;
                break;
        }

        $country = (isset($ShippingInfo["validatedData"]["Country"]["value"]))
            ? $ShippingInfo["validatedData"]["Country"]["value"]
            : "-3";

        $state = (isset($ShippingInfo["validatedData"]["Statemenu"]["value"]))
            ? $ShippingInfo["validatedData"]["Statemenu"]["value"]
            : "-1";

        $result = execQuery('SELECT_SM_DSR_RATES_EXT',
            array(
                'ApiClassName' => __CLASS__,
                'method_id' => $method_id,
                'shipping_info_country_id' => $country,
                'shipping_info_state_id' => $state,
                'pak_weight' => $rate_unit
            )
        );

        if(!empty($result))
        {
        	//
        	$max_priority_rate_index = 0;
        	for($i = $max_priority_rate_index; $i < sizeof($result); $i++)
        	{
        		$max_priority = 2 *  ($result[$max_priority_rate_index]['dst_country'] != ALL_OTHER_COUNTRIES_COUNTRY_ID ? 1 : 0) +
        		                1 * (($result[$max_priority_rate_index]['dst_state'] != ALL_OTHER_STATES_STATE_ID &&
        		                      $result[$max_priority_rate_index]['dst_state'] != STATE_UNDEFINED_STATE_ID)  ? 1 : 0);

        		$priority = 2 *  ($result[$i]['dst_country'] != ALL_OTHER_COUNTRIES_COUNTRY_ID ? 1 : 0) +
                            1 * (($result[$i]['dst_state']   != ALL_OTHER_STATES_STATE_ID &&
                                  $result[$i]['dst_state']   != STATE_UNDEFINED_STATE_ID)  ? 1 : 0);
                if($priority > $max_priority)
                {
                	$max_priority_rate_index = $i;
                }
        	}
            $charges=$result[$max_priority_rate_index];
            $base_charge=$charges['bcharge_abs'] + ($charges['bcharge_perc']*$Subtotal / 100);
            $add_charge_pi=$ItemsCount*($charges['acharge_pi_abs']/*+$charges['acharge_pi_perc']/100*$base_charge*/);
            $add_charge_pwu=$pak_weight*($charges['acharge_pwu_abs']/*+$charges['acharge_pwu_perc']/100*$base_charge*/);
            $shipping_cost=round(($base_charge+$add_charge_pi+$add_charge_pwu),2);
        }

        return $shipping_cost;
    }

    function setDebugFlag($flag=true)
    {
        if(is_bool($flag))
            $this->is_debug=$flag;
    }

    function getDebugInfo()
    {
        return $this->_debug_info;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function replace_coma($str)
    {
        return str_replace(",",".",$str);
    }


    var $bActive;

    var $is_debug;
    var $_debug_info;

    /**#@-*/
}
?>