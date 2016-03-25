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
 * Localization module.
 *
 * @package Localization
 * @author Alexey Florinsky, Alexander Girin
 */
class Localization
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Localization constructor.
     */
    function Localization()
    {
        $this->getSettings();
    }

    /**
     * Reads out module settings from the database.
     *
     * @return
     */
    function getSettings()
    {
        $result = execQuery('SELECT_LOCALIZATION_SETTINGS', array());
        foreach ($result as $value)
        {
            $this->$value["setting_key"] = $value["setting_val"];
        }
        $this->currency_display_stack = array();
    }

    function loadState()
    {
        $this->pushDisplayCurrency($this->getMainStoreCurrency(), $this->getSessionDisplayCurrency());
    }

    /**
     *                                                        .
     *
     *
     *                            .
     *
     *      @param $pm_sm_module_id       GET_PAYMENT_MODULE_FROM_ORDER,                  UUID
     *                 .
     *
     * @param unknown_type $order_id
     */
    function whichCurrencySendOrderToPaymentShippingGatewayIn($order_id /* =ORDER_NOT_CREATED_YET */
                                                             ,$pm_sm_module_id /* =GET_PAYMENT_MODULE_FROM_ORDER */)
    {
    	//        :
    	//  -       ,                       - customer currency
    	//  - main store currency
    	//  -                (                  )                                            /

    	//         :
    	//                             (                  )                     :
    	//      ACTIVE_AND_SELECTED_BY_CUSTOMER                        - customer currency
    	//      ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER  - customer currency
    	//      THE_ONLY_ACCEPTED                                      -       ,
    	//      MAIN_STORE_CURRENCY                                    - main store currency

        //                       ,                                              /     main store currency:
        if($order_id == ORDER_NOT_CREATED_YET)
        {
            $customer_currency_id = modApiFunc("Localization", "getSessionDisplayCurrency");
            $main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
        }
        else
        {
            $currencies = modApiFunc('Checkout', 'getOrderCurrencyList', $order_id);
            $customer_currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $currencies[CURRENCY_TYPE_CUSTOMER_SELECTED]["currency_code"]);
            $main_store_currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $currencies[CURRENCY_TYPE_MAIN_STORE_CURRENCY]["currency_code"]);
        }
        if($pm_sm_module_id == GET_PAYMENT_MODULE_FROM_ORDER &&
           $order_id != ORDER_NOT_CREATED_YET)
        {
            $orderInfo = modApiFunc('Checkout', 'getOrderInfo', $order_id, $main_store_currency_id);
            $pm_sm_module_id = $orderInfo['PaymentModuleId'];
        }

        //                :
        $pm_settings = modApiFunc("Checkout", "getSelectedModules", "payment");
        $sm_settings = modApiFunc("Checkout", "getSelectedModules", "shipping");
        $pm_sm_settings = array_merge_recursive($pm_settings, $sm_settings);
        if(!isset($pm_sm_settings[$pm_sm_module_id]))
            return $main_store_currency_id;
        $module_settings = $pm_sm_settings[$pm_sm_module_id];

        //                      .                            ,                              .
        $rule_name = DEFAULT_CURRENCY_ACCEPTANCE_RULE_NAME;
        foreach($module_settings['currency_acceptance_rules'] as $rule)
        {
        	if($rule['rule_selected'] == DB_TRUE)
        	{
        		$rule_name = $rule['rule_name'];
        	}
        }
        //                                                ,
        $the_one_only_accepted_currency_id = NULL;
        if($rule_name == THE_ONLY_ACCEPTED)
        {
	        foreach($module_settings['accepted_currencies'] as $currency)
	        {
	            if($currency['currency_status'] == THE_ONE_ONLY_ACCEPTED)
	            {
	                $the_one_only_accepted_currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $currency['currency_code']);
	                break;
	            }
	        }
        }

        $currency_id = NULL;
        switch($rule_name)
        {
        	case ACTIVE_AND_SELECTED_BY_CUSTOMER:
            {
            	$currency_id = $customer_currency_id;
            	break;
            }
            case ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER:
            {
                $currency_id = $customer_currency_id;
                break;
            }
            case THE_ONLY_ACCEPTED:
            {
                $currency_id = $the_one_only_accepted_currency_id;
                break;
            }
            case MAIN_STORE_CURRENCY:
            {
                $currency_id = $main_store_currency_id;
                break;
            }
        }
        return $currency_id;
    }

    function doesPMSMAcceptCurrency($order_id, $pm_sm_module_id, $currency_id)
    {
    	//                                                                                .
    	//                                                        ,
    	//                             .
    	//                                      :
        //      ACTIVE_AND_SELECTED_BY_CUSTOMER                        -                 (        )       ,
        //            .
        //      ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER  -           (        )       ,
        //            .                                     "ACCEPTED"               ,                    .
        //         .
        //      THE_ONLY_ACCEPTED                                      -                        ,                          (1 id)
        //      MAIN_STORE_CURRENCY                                    -                  main store currency

        //                       ,                                              /     main store currency:
        if($order_id == ORDER_NOT_CREATED_YET)
        {
            $customer_currency_id = modApiFunc("Localization", "getSessionDisplayCurrency");
            $main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
        }
        else
        {
            $currencies = modApiFunc('Checkout', 'getOrderCurrencyList', $order_id);
            $customer_currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $currencies[CURRENCY_TYPE_CUSTOMER_SELECTED]["currency_code"]);
            $main_store_currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $currencies[CURRENCY_TYPE_MAIN_STORE_CURRENCY]["currency_code"]);
        }
        if($pm_sm_module_id == GET_PAYMENT_MODULE_FROM_ORDER &&
           $order_id != ORDER_NOT_CREATED_YET)
        {
            $orderInfo = modApiFunc('Checkout', 'getOrderInfo', $order_id, $main_store_currency_id);
            $pm_sm_module_id = $orderInfo['PaymentModuleId'];
        }

        //                :
        $pm_settings = modApiFunc("Checkout", "getSelectedModules", "payment");
        $sm_settings = modApiFunc("Checkout", "getSelectedModules", "shipping");
        $pm_sm_settings = array_merge_recursive($pm_settings, $sm_settings);
        $module_settings = $pm_sm_settings[$pm_sm_module_id];

        //                      .                            ,                              .
        $rule_name = DEFAULT_CURRENCY_ACCEPTANCE_RULE_NAME;
        foreach($module_settings['currency_acceptance_rules'] as $rule)
        {
            if($rule['rule_selected'] == DB_TRUE)
            {
                $rule_name = $rule['rule_name'];
            }
        }
        //                                                ,
        $the_one_only_accepted_currency_id = NULL;
        if($rule_name == THE_ONLY_ACCEPTED)
        {
            foreach($module_settings['accepted_currencies'] as $currency)
            {
                if($currency['currency_status'] == THE_ONE_ONLY_ACCEPTED)
                {
                    $the_one_only_accepted_currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $currency['currency_code']);
                    //break;
                }
            }
        }
        //                                                      ,
        $pm_sm_accepted_currencies = array();
        if($rule_name == ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER)
        {
            foreach($module_settings['accepted_currencies'] as $currency)
            {
                if($currency['currency_status'] == ACCEPTED)
                {
                    $pm_sm_accepted_currencies[modApiFunc('Localization', 'getCurrencyIdByCode', $currency['currency_code'])] = $currency;
                }
            }
        }
        $store_wide_accepted_currencies = array();
        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");
        foreach($currency_list as $info)
        {
        	if($info['active'] == DB_TRUE)
        	{
        		$store_wide_accepted_currencies[$info['id']] = $info;
        	}
        }

        $value = NULL;
        switch($rule_name)
        {
            case ACTIVE_AND_SELECTED_BY_CUSTOMER:
            {
                $value = (($currency_id == $customer_currency_id) &&
                          (array_key_exists($currency_id, $store_wide_accepted_currencies)));
                break;
            }
            case ACTIVE_AND_ACCEPTED_BY_PM_SM_AND_SELECTED_BY_CUSTOMER:
            {
                $value = (($currency_id == $customer_currency_id) &&
                          (array_key_exists($currency_id, $store_wide_accepted_currencies)) &&
                          (array_key_exists($currency_id, $pm_sm_accepted_currencies)));
                break;
            }
            case THE_ONLY_ACCEPTED:
            {
                $value = ($currency_id == $the_one_only_accepted_currency_id);
                break;
            }
            case MAIN_STORE_CURRENCY:
            {
                $value = ($currency_id == $main_store_currency_id);
                break;
            }
        }

    	return $value;
    }

    /**
     *                                        .
     *
     * @param unknown_type $order_id
     * @return unknown
     */
    function whichCurrencyToDisplayOrderIn($order_id, $order_currencies_list = NULL)
    {
    	//                              -                                 .
    	//                                     ,
    	//           checkout        .
    	//                 -                 .
    	//                                      (                            ),
    	//                   .
    	if($order_currencies_list === NULL)
    	{
    	    $order_currencies_list = modApiFunc('Checkout', 'getOrderCurrencyList', $order_id);
    	}
    	$currency_id = modApiFunc('Localization', 'getCurrencyIdByCode', $order_currencies_list[CURRENCY_TYPE_CUSTOMER_SELECTED]['currency_code']);
    	return $currency_id;
    }

    /**
     *                           main_store_currency                   .
     *
     * @param unknown_type $order_id
     * @return unknown
     */
    function getOrderMainCurrency($order_id, $currencies = NULL)
    {
        if($currencies === NULL)
        {
            $currencies = modApiFunc('Checkout', 'getOrderCurrencyList', $order_id);
        }
        return $currencies[CURRENCY_TYPE_MAIN_STORE_CURRENCY]['currency_code'];
    }

    function whichCurrencyToDisplayInCurrentZone()
    {
        if (modApiFunc('Users', 'getZone') == "CustomerZone")
        {
            //      ,                        .
            $id = modApiFunc('Localization', 'getSessionDisplayCurrency');
        }
        else
        {
            //
            $id = modApiFunc('Localization', 'getMainStoreCurrency');
        }
        return $id;
    }

    /**
     *                                          -             .       -
     *                      .
     */
    function loadCurrencyDisplaySettings($id)
    {
    	$currency_var_name = "CURRENCY_" . $id;
    	$currency_format_var_name = "CURRENCY_FORMAT_" . $id;
    	$currency_positive_format_var_name = "CURRENCY_POSITIVE_FORMAT_" . $id;
        $currency_negative_format_var_name = "CURRENCY_NEGATIVE_FORMAT_" . $id;

    	if(isset($this->$currency_var_name))
    	{
    		//                                             .
            $this->CURRENCY                 = $this->$currency_var_name;
            $this->CURRENCY_FORMAT          = $this->$currency_format_var_name;
            $this->CURRENCY_POSITIVE_FORMAT = $this->$currency_positive_format_var_name;
            $this->CURRENCY_NEGATIVE_FORMAT = $this->$currency_negative_format_var_name;
    	}
    	else
    	{
    		//                              .
    	    $info = $this->getCurrencyInfo($id);
			$this->CURRENCY                 = $id . "|" . $info['sign'];
			$this->CURRENCY_FORMAT          = DEFAULT_CURRENCY_FORMAT;
			$this->CURRENCY_POSITIVE_FORMAT = DEFAULT_CURRENCY_POSITIVE_FORMAT;
			$this->CURRENCY_NEGATIVE_FORMAT = DEFAULT_CURRENCY_NEGATIVE_FORMAT;
    	}
    }

    function getSettingsRaw()
    {
    	global $application;
        $tables = $this->getTables();

        $s = $tables['localization_settings']['columns'];
        $query = new DB_Select();
        $query->addSelectField($s["key"], "setting_key");
        $query->addSelectField($s["val"], "setting_val");
        $result = $application->db->getDB_Result($query);

        $settings = array();
        foreach ($result as $value)
        {
            $settings[$value["setting_key"]] = $value["setting_val"];
        }
    	return $settings;
    }

    /**
     * Saves setting values (date format, time format etc.) in the database.
     *
     * @return
     */
    function setValue($key, $val)
    {
        global $application;

        $tables = $this->getTables();

        $table = 'localization_settings';
        $columns = $tables[$table]['columns'];

        $settings = $this->getSettingsRaw();
        if(isset($settings[$key]))
        {
	        $query = new DB_Update($table);
	        $query->addUpdateValue($columns['val'], $val);
	        $query->WhereValue($columns['key'], DB_EQ, $key);
	        $application->db->getDB_Result($query);
        }
        else
        {
            //                      -
	        $query = new DB_Insert($table);
	        $query->addInsertValue($key, $columns['key']);
	        $query->addInsertValue($val, $columns['val']);
	        $application->db->getDB_Result($query);
        }
        /**** execQuery('UPDATE_LOCALIZATION_SETTINGS', array('key'=>$key, 'val'=>$val)); ****/
    }

    /**
     * Sets the symbol for the current selected currency.
     *
     * @param integer $currency_id - the currency ID
     * param string - the currency symbol
     * @return
     */
    function setCurrencySign($currency_id, $sign)
    {
        $params = array('sign' => $sign,
                        'currency_id' => $currency_id);
        execQuery('UPDATE_LOCALIZATION_CURRENCY_SIGN', $params);
    }

    /**
     * Gets the value of the Localization setting by the key:
     *      DATE_FORMAT - date format
     *      TIME_FORMAT - time format
     *      NUMBER_FORMAT - number format
     *      NEGATIVE_FORMAT - negative numbers format
     *      CURRENCY - current selected currency
     *      CURRENCY_FORMAT - currency amount format
     *      CURRENCY_FORMAT_EXPONENT - positive currency exponent (2 for US Dollars, 0 for Japanese Yens, 3 for Kuwaiti Dinars)
     *      CURRENCY_POSITIVE_FORMAT - positive currency amount format
     *      CURRENCY_NEGATIVE_FORMAT - negative currency amount format
     *      WEIGHT_UNIT -  symbol for unit of weight
     *      WEIGHT_COEFF - coefficient of casting the custom unit of weight to 1 kg
     *      ITEM_UNIT - symbol for numeric parameter unit
     *
     * @param integer $currency_id - the currency ID
     * param string - the currency symbol
     * @return string - the setting value
     */
    function getValue($key)
    {
    	switch($key)
    	{
    		case 'CURRENCY_FORMAT_EXPONENT':
    		{
                $format = explode("|", $this->CURRENCY_FORMAT);
    			return $format[0];
    		}
    		default:
    		{
                return $this->$key;
    		}
    	}
    }

    /**
     * Gets the currency symbol.
     *
     * @return
     */
    function getCurrencySign()
    {
        $sign = explode("|", $this->CURRENCY);
        //patch to allow single unicode character as currency symbol
        $value = preg_match("/^\&\#[0-9]{1,10};$/i", $sign[1]) ? $sign[1] : prepareHTMLDisplay($sign[1]);
        return $value;
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables()
     */
    function install()
    {
        _use(dirname(__FILE__).'/install/install.php');
    }

    /**
     * Installs the specified module in the system.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of the meta description of the table:
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

        $table = 'localization_settings';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.localization_setting_id'
               ,'key'               => $table.'.setting_key'
               ,'val'               => $table.'.setting_value'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'val'               => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'date_time_formats';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.date_time_format_id'
               ,'d_t'               => $table.'.date_time'
               ,'format'            => $table.'.format'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'d_t'               => DBQUERY_FIELD_TYPE_CHAR5
               ,'format'            => DBQUERY_FIELD_TYPE_CHAR20
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'negative_formats';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.negative_format_id'
               ,'format'            => $table.'.format'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'format'            => DBQUERY_FIELD_TYPE_CHAR20
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'currencies';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.currency_id'
               ,'name'              => $table.'.currency_name'
               ,'code'              => $table.'.currency_code'
               ,'iso'               => $table.'.currency_iso'
               ,'sign'              => $table.'.currency_sign'
               ,'active'            => $table.'.currency_active'
               ,'default'           => $table.'.currency_default'
               ,'visible'           => $table.'.currency_visible'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'code'              => DBQUERY_FIELD_TYPE_CHAR5
               ,'iso'               => DBQUERY_FIELD_TYPE_CHAR5
               ,'sign'              => DBQUERY_FIELD_TYPE_CHAR5
               ,'active'            => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'default'           => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'visible'           => DBQUERY_FIELD_BOOLEAN_DEFAULT_TRUE
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                 'IDX_unique' => 'code'
                ,'IDX_unique' => 'iso'
            );

        $table = 'positive_currency_formats';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.negative_format_id'
               ,'format'            => $table.'.format'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'format'            => DBQUERY_FIELD_TYPE_CHAR20
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'negative_currency_formats';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.negative_format_id'
               ,'format'            => $table.'.format'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'format'            => DBQUERY_FIELD_TYPE_CHAR20
            );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $patterns = 'patterns';
        $tables[$patterns] = array();
        $tables[$patterns]['columns'] = array
            (
                'id'                => 'patterns.pattern_id'
               ,'name'              => 'patterns.pattern_name'
               ,'descr'             => 'patterns.pattern_descr'
               ,'value'             => 'patterns.pattern_value'
               ,'type'              => 'patterns.pattern_type'
            );
        $tables[$patterns]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
               ,'descr'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$patterns]['primary'] = array
            (
                'id'
            );


        global $application;
        return $application->addTablePrefix($tables);
    }

    /**
     * Gets the unit symbol.
     *
     * @param string $entity - the entity, the units: 'currency', 'weight', 'item'
     * @return the unit symbol
     */
    function getUnitTypeValue($entity)
    {
        $value = '';
        switch ($entity)
        {
            case 'currency':
                $value = $this->getCurrencySign();
                break;
            case 'weight':
                $value = prepareHTMLDisplay($this->getValue('WEIGHT_UNIT'));
                break;
            case 'item':
                $value = prepareHTMLDisplay(cz_getMsg('ITEM_UNIT'));
                break;
        }
        return $value;
    }

    /**
     * Gets the specyfied regular expression for the parameter.
     *
     * @param strign $pattern - 'integer', 'floet', 'currency', 'weight', 'item', 'string' ...
     * @return the regular expression
     */
    function getPattern($pattern)
    {
    	if($pattern == "currency")
    	{
    		//                                           .
    		//                                 -                                     .
    		//                                                                  ,
    		//              -                             .
    	    switch(modApiFunc('Users', 'getZone'))
            {
                case "CustomerZone":
                {
                    $id = modApiFunc("Localization", "getSessionDisplayCurrency");
                    break;
                }
                default://"AdminZone":
                {
                    // April 13 - deciding to turn of JS-formatting
                    // forcing to use DEFAULT_CURRENCY_FORMAT,
                    // as currency_id=0 does not exist
                	$id = 0;//modApiFunc("Localization", "getMainStoreCurrency");
                    break;
                }
            }

    		$selected_pattern =  $pattern . "_" . $id;
    		$patterns = $this->getPatterns();
    		if(isset($patterns[$selected_pattern]))
    		{
    			$patt = $patterns[$selected_pattern];
    		}
    		else if($patterns[$pattern])
    		{
    			//                                     .
    			$patt = $patterns[$pattern];
    		}
    		else
    		{
    			$patt =  null;
    		}
    		if($patt !== NULL)
    		{
    			return array
    			(
    			    "patt_type" => "currency"
    			   ,"patt_value" => $patt
    			);
    		}
    		else
    		{
    			return NULL;
    		}
    	}
    	else
    	{
            $patterns = Localization::getPatterns();
            foreach($patterns as $info)
            {
                if($info['type'] == $pattern)
                {
                    return $info;
                }
            }
            return null;
    	}
    }

    function getPatterns()
    {
        $result = execQuery('SELECT_ALL_LOCALIZATION_PATTERNS', array(),true);
        $patterns = array();
        foreach ($result as $value)
        {
            $patterns[$value["patt_type"]] = $value["patt_value"];
        }
        return $patterns;
    }

    /**
     * Sets the pattern.
     *
     * @param
     * @return
     */
    function setPattern($pattern, $entity)
    {
        global $application;
        $tables = $this->getTables();

        $patterns = $this->getPatterns();
        if(isset($patterns[$entity]))
        {
	        $p = $tables['patterns']['columns'];

	        $query = new DB_Update('patterns');
	        $query->addUpdateValue($p['value'], $pattern);
	        $query->WhereValue($p['type'], DB_EQ, $entity);
	        $application->db->getDB_Result($query);
        }
        else
        {
            //                      -
            $query = new DB_Insert('patterns');
            $p = $tables['patterns']['columns'];
            $query->addInsertValue($pattern, $p['value']);
            $query->addInsertValue($entity, $p['type']);
            $application->db->getDB_Result($query);
        }
    }

    /**
     * Gets the list of possible formats.
     *
     * @return
     */
    function getFormatsList($entity)
    {
    	static $r = array();
    	if (!isset($r[$entity]))
    	{
        	$r[$entity] = execQuery('SELECT_LOCALIZATION_FORMATS_LIST', array('entity'=>$entity));
    	}
    	return $r[$entity];
    }

    /**
     * Gets the current, specyfied format of any entity.
     */
    function getFormat($entity)
    {
        switch($entity)
        {
            case 'date':
                return $this->DATE_FORMAT;
                break;
            case 'time':
                return $this->TIME_FORMAT;
                break;
            case 'number':
                return $this->NUMBER_FORMAT;
                break;
            case 'negative':
                return $this->NEGATIVE_FORMAT;
                break;
            case 'currency':
                return $this->CURRENCY;
                break;
            case 'currency_format':
                return $this->CURRENCY_FORMAT;
                break;
            case 'currency_positive_format':
                return $this->CURRENCY_POSITIVE_FORMAT;
                break;
            case 'currency_negative_format':
                return $this->CURRENCY_NEGATIVE_FORMAT;
                break;
        }
    }

    /**
     * Casts the parameter value to the specified format before outputting it
     * to the browser window.
     *
     * @param mixed $value - the parameter value
     * @param string $entity - 'currency', 'weight'
     * @return string - the formatted parameter value
     */
    function format($value, $entity)
    {
        switch ($entity)
        {
            case 'currency':
                $value = $this->currency_format($value);
                break;
            case 'weight':
            case 'item':
            case 'number':
                $value = $this->num_format($value);
                break;
            default:

                break;
        }
        return $value;
    }

    /**
     * Gets the settings, used in the javascript functions.
     *
     * @param string $entity - 'currency', 'weight'
     * @return string
     */
    function format_settings_for_js($entity)
    {
        switch ($entity)
        {
            case 'currency':
                // April 13 - deciding to turn of JS-formatting
                // forcing to use DEFAULT_CURRENCY_FORMAT
                $format = explode("|", DEFAULT_CURRENCY_FORMAT);//$this->CURRENCY_FORMAT);
                $value = "decimals = \"".$format[0]."\" dec_point = \"".$format[1]."\"";
                break;
            case 'weight':
                $format = explode("|", $this->NUMBER_FORMAT);
                $value = "decimals = \"".$format[0]."\" dec_point = \"".$format[1]."\"";
                break;
            case 'weight_coeff':
                $format = explode("|", $this->NUMBER_FORMAT);
                $value = "decimals = \"6\" dec_point = \"".$format[1]."\"";
                break;
            default:
                $value = "";
                break;
        }
        return $value;
    }

    function getPrecision($entity)
    {
        $precision = 1;
        switch ($entity)
        {
            case "currency":
                $format = explode("|", $this->CURRENCY_FORMAT);
                $num = $format[0];
                break;
            case "weight":
                $format = explode("|", $this->NUMBER_FORMAT);
                $num = $format[0];
                break;
            case "item":
                $num = 0;
                break;
            default:

                break;
        }
        for ($i=0; $i<$num; $i++)
        {
            $precision /= 10;
        }
        return $precision;
    }

    /**
     * Converts the string with the value, with the number, as float,
     * inputting by user, before saving it to the database.
     *
     * @param mixed $value - the parameter value
     * @param string $entity - 'currency', 'weight'
     * @return - returns the value, which will be written to the database.
     */
    function FormatStrToFloat($value, $entity)
    {
        switch ($entity)
        {
            case 'currency':
                $format = explode("|", $this->CURRENCY_FORMAT);
                $value = strtr($value, $format[1], '.');
                break;
            case 'weight':
            case 'weight_coeff':
                $format = explode("|", $this->NUMBER_FORMAT);
                $value = strtr($value, $format[1], '.');
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * Converts the value from the database to the formatted string, according
     * to the current settings, before outputting to the entry field.
     *
     * @param mixed $value - the parameter value
     * @param string $entity - 'currency', 'weight'
     * @return - returns the value, which will be outputted to the entry field
     */
    function FloatToFormatStr($value, $entity)
    {
        switch ($entity)
        {
            case 'currency':
                if ($value != null && $value != "")// && $value!=0)
                {
                    // 2009 April 13 - deciding to turn of JS-formatting
                    // forcing to use DEFAULT_CURRENCY_FORMAT
//                    $format = explode("|", $this->CURRENCY_FORMAT);
                    $format = explode("|", DEFAULT_CURRENCY_FORMAT);
                    $value = number_format($value, $format[0], $format[1], '');
                }
                break;
            case 'weight':
                if ($value != null && $value != "")// && $value!=0)
                {
                    $format = explode("|", $this->NUMBER_FORMAT);
                    $value = number_format($value, $format[0], $format[1], '');
                }
                break;
            case 'weight_coeff':
                if ($value != null && $value != "")// && $value!=0)
                {
                    $format = explode("|", $this->NUMBER_FORMAT);
                    $value = number_format($value, 6, $format[1], '');
                }
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * Converts the number to the form, appropriate to the current number format.
     *
     * @param float $num - the number, which should be formatted
     * @return string - the formatted number of the string type
     */
    function num_format($num)
    {
        //           ,                  get-   ,
        global $__localization_disable_formatting__;
        if ($__localization_disable_formatting__ == true)
        {
            return $num;
        }

        $negative = false;
        $num_format = explode("|", $this->NUMBER_FORMAT);
        $num *= 1;
        if (!is_int($num)&&!is_float($num))
        {
            return $num;
        }
        if ($num < 0)
        {
            $negative = true;
            $num *= -1;
        }
        if (!is_int($num))
        {
            $num = number_format($num, $num_format[0], $num_format[1], $num_format[2]);
        }
        else
        {
            $num = number_format($num, 0, $num_format[1], $num_format[2]);
        }
        if ($negative)
        {
            $num = sprintf($this->NEGATIVE_FORMAT, $num);
        }
        return $num;
    }

    function currency_round($sum, $currency_id)
    {
    	$main_store_currency = modApiFunc("Localization", "getMainStoreCurrency");
    	if($sum == PRICE_N_A)
    	{
    		return $sum;
    	}
    	else
    	{
  	        if($main_store_currency != $currency_id)
            {
                modApiFunc("Localization", "pushDisplayCurrency", $currency_id, $currency_id);
            }
            $cur_format = explode("|", $this->CURRENCY_FORMAT);
            $sum = number_format($sum, $cur_format[0], ".", "");
            if($main_store_currency != $currency_id)
            {
                modApiFunc("Localization", "popDisplayCurrency");
            }
            return $sum;
    	}
    }

    /**
     * Converts the sum to the form, appropriate to the current currency format.
     *
     * @param float $sum - the sum, which should be formatted
     * @return string - the fomatted sum of the string type
     */
    function currency_format($sum, $dont_convert=false)
    {
        global $application;
        if($sum == PRICE_N_A)
        {
            switch(modApiFunc('Users', 'getZone'))
            {
                case "CustomerZone":
                {
                    $obj = &$application->getInstance('MessageResources',"messages", "CustomerZone");
                    $value = $obj->getMessage('PRICE_N_A');
                    break;
                }
                case "AdminZone":
                {
                    $obj = &$application->getInstance('MessageResources',"system-messages", "AdminZone");
                    $value = $obj->getMessage('PRICE_N_A');
                    break;
                }
                default:
                {
                    $value = "";
                    //: : call _fatal()
                }
            }
            return $value;
        }

        if ($sum !== 0.0 and ($sum == '' || empty($sum)))
        {
            return $sum;
        }

        $negative = false;
        $cur_sign = explode("|", $this->CURRENCY);
        $cur_id = modApiFunc("Localization" , "getLocalMainCurrency");

        //patch to allow single unicode character as currency symbol
        $cur_sign = preg_match("/^\&\#[0-9]{1,10};$/i", $cur_sign[1]) ? $cur_sign[1] : prepareHTMLDisplay($cur_sign[1]);

        $cur_format = explode("|", $this->CURRENCY_FORMAT);
        $sum *= 1;
        if (!is_int($sum)&&!is_float($sum))
        {
            return $sum;
        }
        if ($sum < 0)
        {
            $negative = true;
            $sum *= -1;
        }

		//                          -
        if($dont_convert === false)
        {
            $from = $this->getLocalMainCurrency();
            $to = $this->getLocalDisplayCurrency();
            if($from != $to)
            {
                $from_code = $this->getCurrencyCodeById($from);
                $to_code = $this->getCurrencyCodeById($to);
                $sum = modApiFunc("Currency_Converter", "convert", $sum, $from_code, $to_code);
            }
        }

        //           ,                  get-   ,
        global $__localization_disable_formatting__;
        if ($__localization_disable_formatting__ == true)
        {
            return $sum;
        }

        $sum = number_format($sum, $cur_format[0], $cur_format[1], $cur_format[2]);
        if ($negative)
        {
            $sum = strtr($this->CURRENCY_NEGATIVE_FORMAT, array("{s}" => $cur_sign, "{v}" => $sum));
        }
        else
        {
            $sum = strtr($this->CURRENCY_POSITIVE_FORMAT, array("{s}" => $cur_sign, "{v}" => $sum));
        }
        return $sum;
    }

    /**
     * Returns the formatted date, depending on the specified time format.
     *
     * @param mixed $date - the date might have different types:
     *                       integer - timestamp;
     *                       string  - the date in the format MySQL: '0000-00-00'     '0000-00-00 00:00:00';
     *                       array(
     *                             int $year,
     *                             int $month,
     *                             int $day
     *                            )  - the date of the array type year, month, day
     * @return the formatted date
     */
    function date_format($date, $f_apply_time_shift = true)
    {
        if ($f_apply_time_shift === true)
        {
            $time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT)*60*60;
        }
        else
        {
            $time_shift = 0;
        }
        if (is_int($date))
        {
            return date($this->DATE_FORMAT, ($date + $time_shift));
        }
        elseif (is_string($date))
        {
            if (_ml_strlen($date)<=10)
            {
                $date = explode("-", $date);
                return date($this->DATE_FORMAT, (mktime(0, 0, 0, $date[1], $date[2], $date[0]) + $time_shift));
            }
            else
            {
                $date = explode(" ", $date);
                $date = explode("-", $date[0]);
                $time = explode(":", $date[1]);
                $time[0] = isset($time[0]) ? $time[0] : 0;
                $time[1] = isset($time[1]) ? $time[1] : 0;
                $time[2] = isset($time[2]) ? $time[2] : 0;
                return date($this->DATE_FORMAT, (mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]) + $time_shift));
            }
        }
        elseif (is_array($date))
        {
            return date($this->DATE_FORMAT, (mktime(0, 0, 0, $date[1], $date[2], $date[0]) + $time_shift));
        }
        return $date;
    }

    /**
     * Casts the date to the specified format from timestamp.
     *
     * @param integer $date -  the date timestamp
     * @return string - the formatted date
     */
    function timestamp_date_format($date, $b_apply_time_shift = true)
    {
        $date *=1;
        if ($b_apply_time_shift == true)
        {
            $time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT)*60*60;
        }
        else
        {
            $time_shift = 0;
        }
        return date($this->DATE_FORMAT, ($date + $time_shift));
    }

    /**
     * Casts the date to the specified format from the string in the format,
     * defined as SQL - '2005-12-31' or '2005-12-31 12:34:56'
     *
     * @param integer $date -  the date timestamp
     * @return string - the formatted date
     */
    function SQL_date_format($date, $format = false)
    {
        if ($format === false)
            $format = $this -> DATE_FORMAT;
        $time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT)*60*60;
        if (_ml_strlen($date)<=10)
        {
            // if $date does not include time then no adjusting is applied...
            $date = explode("-", $date);
            return date($format, mktime(0, 0, 0, $date[1], $date[2], $date[0]));
        }
        else
        {
            $date = explode(" ", $date);
            $time = explode(':', $date[1]);
            $date = explode("-", $date[0]);
            return date($format, (mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]) + $time_shift));
        }
    }

    /**
     * Casts the time to the specified format from timestamp.
     *
     * @param integer $date - the date timestamp
     * @return string - the formatted date
     */
    function timestamp_time_format($time, $b_apply_time_shift = true)
    {
        $time *=1;
        if ($b_apply_time_shift == true)
        {
            $time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT)*60*60;
        }
        else
        {
            $time_shift = 0;
        }
        return date($this->TIME_FORMAT, ($time + $time_shift));
    }

    /**
     * Casts the time to the specified format from the string in the format,
     * defined as SQL - '12:34:56' or '2005-12-31 12:34:56'
     *
     * @param integer $date -  the date timestamp
     * @return string - the formatted date
     */
    function SQL_time_format($time,$b_apply_time_shift = true)
    {
	if ($b_apply_time_shift == true)
		$time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT)*60*60;
	else
		$time_shift = 0;

        if (_ml_strlen($time)<=8)
        {
            $time = explode(":", $time);
            return date($this->TIME_FORMAT, (mktime($time[0], $time[1], $time[2], 2, 1, 1970) + $time_shift));
        }
        else
        {
            $time = explode(" ", $time);
            $time = explode(":", $time[1]);
            return date($this->TIME_FORMAT, (mktime($time[0], $time[1], $time[2], 2, 1, 1970) + $time_shift));
        }
    }

    /**
     * Returns the SQL interval for the time_shift
     */
    function getSQLInterval()
    {
        $time_shift = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT);
        return 'INTERVAL ' . $time_shift . ' HOUR';
    }

    /**
     * Rounds the monetary unit.
     *
     * @return string - the rounded unit
     */
    function roundMonetaryUnit($value)
    {
        return round($value, modApiFunc("Localization", "getValue", "CURRENCY_FORMAT_EXPONENT"));
    }

    function formatTimeDuration($seconds)
    {
        if (Validator::isValidInt($seconds) == false)
        {
            return $seconds;
        }

        if ($seconds < 60)
        {
            return $this->__formatSecondsDuration($seconds);
        }
        elseif ($seconds < 3600)
        {
            return $this->__formatMinutesDuration($seconds);
        }
        else
        {
            return $this->__formatHoursDuration($seconds);
        }
    }

    function __formatSecondsDuration($seconds)
    {
        if ($seconds <= 1)
        {
            return $seconds.' '.getMsg('SYS','SECOND');
        }
        else
        {
            return $seconds.' '.getMsg('SYS','SECONDS');
        }
    }

    function __formatMinutesDuration($seconds)
    {
        if ($seconds < 60)
        {
            return $this->__formatSecondsDuration($seconds);
        }
        elseif ($seconds == 60)
        {
            return '1 '.getMsg('SYS','MINUTE');
        }
        elseif ($seconds < 3600)
        {
            $s = $seconds % 60;
            $m = floor($seconds / 60);
            $m_hint = ($m == 1) ? getMsg('SYS','MINUTE') : getMsg('SYS','MINUTES');
            if ($s == 0)
            {
                return $m.' '.$m_hint;
            }
            else
            {
                return $m.' '.$m_hint.' '.$this->__formatSecondsDuration($s);
            }
        }
    }

    function __formatHoursDuration($seconds)
    {
        if ($seconds < 3600)
        {
            return $this->__formatMinutesDuration($seconds);
        }
        elseif ($seconds == 3600)
        {
            return '1 '.getMsg('SYS','HOUR');
        }
        else
        {
            $h = floor($seconds/3600);
            $r = $seconds % 3600; //                    ,
            if ($r == 0)
            {
                return '1 '.getMsg('SYS','HOUR');
            }
            else
            {
                return $h.' '.($h==1 ? getMsg('SYS','HOUR') : getMsg('SYS','HOURS')).' '.$this->__formatMinutesDuration($r);
            }
        }
    }

    /**
     *                             .
     */
    function formatFileSize($size)
    {
        $a = array('b','Kb','Mb','Gb','Tb');
        $i = 0;
        $have_mod = false;
        while($size > 1024 and $i < 4)
        {
            $have_mod = ($have_mod or (($size % 1024) > 0));
            $size = $size / 1024;
            $i++;
        };

        $str = '';
        $frm = "%d ";
        if($i)
        {
            if($have_mod)
                $str .= '~';
            $frm = "%.2f ";
        };
        $str .= sprintf($frm,round($size,2)).$a[$i];
        return $str;
    }

    function isCorrectCurrencyID($id)
    {
    	$info = $this->getCurrencyInfo($id);
    	if($info === NULL)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }

    function getCurrencyIdByCode($code)
    {
        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");
        foreach($currency_list as $info)
        {
            if($info['code'] == $code)
            {
                return $info['id'];
            }
        }
        return NULL;
    }

    function getCurrencyIsoByCode($code)
    {
        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");
        foreach($currency_list as $info)
        {
            if($info['code'] == $code)
            {
                return $info['iso'];
            }
        }
        return NULL;
    }

    function getCurrencyCodeById($id)
    {
    	$info = modApiFunc("Localization", "getCurrencyInfo", $id);
    	if($info === NULL)
    	{
    		return NULL;
    	}
    	else
    	{
    		return $info["code"];
    	}
    }

    function getCurrencyIdByIso($iso)
    {
        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");
        foreach($currency_list as $info)
        {
            if($info['iso'] == $iso)
            {
                return $info['id'];
            }
        }
        return NULL;
    }

    function getCurrencyCodeByIso($iso)
    {
        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");
        foreach($currency_list as $info)
        {
            if($info['iso'] == $iso)
            {
                return $info['code'];
            }
        }
        return NULL;
    }

    function getCurrencyIsoById($id)
    {
    	$info = modApiFunc("Localization", "getCurrencyInfo", $id);
    	if($info === NULL)
    	{
    		return NULL;
    	}
    	else
    	{
    		return $info["iso"];
    	}
    }

    function getCurrencyInfo($id)
    {
        $currency_list = modApiFunc("Localization", "getFormatsList", "currency");
        foreach($currency_list as $info)
        {
            if($info['id'] == $id)
            {
                return $info;
            }
        }
        return NULL;
    }

    /**
     *          ,                                  CZ:
     *          ,                 ,           ,
     *        .
     *
     *   AZ                .
     *
     * @param unknown_type $id
     */
    function setSessionDisplayCurrency($id)
    {
        $ac = modApiFunc("Localization", "getActiveCurrenciesList", RETURN_AS_ID_OBJECT_LIST);
        foreach ($ac as $cid => $item)
        {
            if ($item['visible'] !== 'true')
                unset($ac[$cid]);
        }
    	if(modApiFunc("Localization", "isCorrectCurrencyID", $id) && isset($ac[$id]))
    	{
    		modApiFunc('Session','set','SessionDisplayCurrencyID',$id);
            modApiFunc("Localization", "pushDisplayCurrency", modApiFunc("Localization", "getMainStoreCurrency"), $id);
    	}
    }

    /**
     *                                 .
     *                             id-            ,
     *              :
     *      array(
     *          'id' => number
     *          'name' => string
     *          'code' => ISO code
     *          'sign' =>
     *          'active' => DB_TRUE|DB_FALSE
     *          'dflt' =>
     *      )
     *
     * @param unknown_type $b_as_id_list
     * @return unknown
     */
    function getActiveCurrenciesList($b_as_id_list = RETURN_AS_ID_LIST)
    {
        static $active_currencies;
        if (! isset($active_currencies)) {
            $active_currencies = array();
            $list = $this->getFormatsList("currency");
            $main_store_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
            foreach($list as $info)
            {
                if( $info['active'] == DB_TRUE &&
                    ($main_store_currency_code == $info['code'] ||
                     modApiFunc("Currency_Converter", "isConvertAvail", $info['code'], $main_store_currency_code) == TRUE))
                {
                    $active_currencies[] = $info;
                }
            }
        }

        $store_wide_accepted_currencies = array();
        foreach($active_currencies as $info)
        {
            if ($b_as_id_list == RETURN_AS_ID_LIST)
            {
                $store_wide_accepted_currencies[] = $info['id'];
            }
            if ($b_as_id_list == RETURN_AS_ID_OBJECT_LIST)
            {
                $store_wide_accepted_currencies[$info['id']] = $info;
            }
            else if ($b_as_id_list == RETURN_AS_CODE_LIST)
            {
                $store_wide_accepted_currencies[] = $info['code'];
            }
            else if ($b_as_id_list == RETURN_AS_CODE_OBJECT_LIST)
            {
                $store_wide_accepted_currencies[$info['code']] = $info;
            }
            else
            {
                $store_wide_accepted_currencies[] = $info;
            }
        }
        return $store_wide_accepted_currencies;
    }


    function getSessionDisplayCurrency()
    {
        $ac = modApiFunc("Localization", "getActiveCurrenciesList", RETURN_AS_ID_OBJECT_LIST);
        foreach ($ac as $cid => $item)
        {
            if ($item['visible'] !== 'true')
                unset($ac[$cid]);
        }

      	if (modApiFunc('Session','is_set','SessionDisplayCurrencyID'))
      	{
            $value = modApiFunc('Session','get','SessionDisplayCurrencyID');
            if (!modApiFunc("Localization", "isCorrectCurrencyID", $value) || !isset($ac[$value]))
                $value = modApiFunc('Localization','getDefaultDisplayCurrencyID');
       	}
       	else
       	{
    		$value = modApiFunc('Localization','getDefaultDisplayCurrencyID');
    	}
        return $value;
    }

    /**
     *                           Storefront,
     *                             .
     *
     * @return unknown
     */
    function getDefaultDisplayCurrencyID()
    {
        return modApiFunc("Localization", "getMainStoreCurrency");
    }

    /**
     *                                      id,           id         ,
     *
     *          .
     *                                       -
     *         .
     *
     *   CZ                .
     *
     * @param unknown_type $id
     */
    function setCurrencyFormatEdited($id)
    {
        if(modApiFunc("Localization", "isCorrectCurrencyID", $id))
        {
            modApiFunc('Session','set','CurrencyFormatEditedID',$id);
        }
    }

    function unsetCurrencyFormatEdited()
    {
        modApiFunc('Session','un_set','CurrencyFormatEditedID');
    }

    function getCurrencyFormatEdited()
    {
        if(modApiFunc('Session','is_set','CurrencyFormatEditedID'))
        {
            return modApiFunc('Session','get','CurrencyFormatEditedID');
        }
        else
        {
        	return modApiFunc("Localization", "getMainStoreCurrency");
        }
    }

    function getMainStoreCurrency()
    {
    	$currencies = modApiFunc("Localization", "getFormatsList", "currency");
    	foreach($currencies as $info)
    	{
    		if($info['dflt'] == DB_TRUE)
    		{
    			return $info['id'];
    		}
    	}
    	//                                   (default)
    	//          ,                     .
        return DEFAULT_CURRENCY_ID;
    }


    function updateCurrency($c_id, $c_name, $c_active, $c_default, $c_visible)
    {
        global $application;
        $tables = $this->getTables();
        $c  = $tables['currencies']['columns'];

        $query = new DB_Update('currencies');
        $query->addUpdateValue($c["active"],  $c_active);
        $query->addUpdateValue($c["default"], $c_default);
        $query->addUpdateValue($c["visible"], $c_visible);
        if ($c_name)
        {
            $query->addUpdateValue($c["name"], $c_name);
        }
        $query->WhereValue($c["id"], DB_EQ, $c_id);
        $application->db->getDB_Result($query);
    }


    function clearActiveAndDefaultCurrenciesList()
    {
        global $application;
        $tables = $this->getTables();
        $c  = $tables['currencies']['columns'];

        $query = new DB_Update('currencies');
        $query->addUpdateValue($c["active"], "false");
        $query->addUpdateValue($c["default"], "false");
        $application->db->getDB_Result($query);
    }

    function addNewAdditionalCurrency(
        $new_ac_id,         // currency ID to add, Avactis internal numeric
        $rate_method,       // 1 - add manual rate, 2- web rate
        $new_ac_rate = null, // new rate to MainStoreCurrency
        $visible = 'true',    // is visible for customers
        $add_anyway = false // if web rate is false, add manually with 1
    )
    {
        $current_msc_id = $this->getMainStoreCurrency();
        $current_msc_code = $this->getCurrencyCodeById($current_msc_id);
        $new_ac_code = $this->getCurrencyCodeById($new_ac_id);

        //          ,
        if ($current_msc_id == $new_ac_id)
        {
            return STORE_CURRENCIES_CANNOT_ADD_MAIN_AS_ADDITIONAL;
        }

        //
        $currencies = $this->getActiveCurrenciesList(true);
        foreach ($currencies as $val)
        {
            if ($new_ac_id == $val)
            {
                return STORE_CURRENCIES_CANNOT_ADD_DUPLICATE;
            }
        }

        //
        if ($rate_method == 1)   //
        {
            $rate = 0;
            //                      ,             -
            if ($new_ac_rate)
            {
                $rate = 1 / str_replace(',', '.', $new_ac_rate);
                $rate = number_format($rate, 4, '.', '');
                if ($rate < 0) $rate = -$rate;
            }

            if ($rate == 0)
            {
                return STORE_CURRENCIES_INVALID_MANUAL_RATE_ERROR;
            }
        }
        else // if ($method == 2)   // internet service
        {
            //                 ,             -
            $rate = modApiFunc("Currency_Converter", "getRateFromWeb", $new_ac_code, $current_msc_code);
            if ($rate == false)
            {
                // add anyway with 1:1 rate
                if ($add_anyway)
                    return $this->addNewAdditionalCurrency($new_ac_id, 1, 1, $visible);

                // or generate error
                return STORE_CURRENCIES_CANNOT_OBTAIN_NEW_RATE_FROM_WEB;
            }
        }

        //
        $this->updateCurrency($new_ac_id, "", "true", "false", $visible);

        //
        modApiFunc("Currency_Converter", "addManualRate", $current_msc_code, $new_ac_code, $rate);

        //
        return true;
    }

    //                  ,                     currency_format
    /**
     *                            currency_format.
     *                                ,
     * LocalMainCurrency   DisplayCurrency.
     */
    function setLocalMainCurrency($id)
    {
        $this->localMainCurrency = $id;
    }

    function getLocalMainCurrency()
    {
        return $this->localMainCurrency;
    }

    /**
     *                                        currency_format.
     *                                ,
     * LocalMainCurrency   LocalDisplayCurrency.
     */
    function setLocalDisplayCurrency($id)
    {
        modApiFunc("Localization", "loadCurrencyDisplaySettings", $id);
        $this->localDisplayCurrency = $id;
    }

    function getLocalDisplayCurrency()
    {
        return $this->localDisplayCurrency;
    }


	// LIFO
    /**
     *                                                                        .
     * -
     * -
     * -
     */
    function pushDisplayCurrency($main_currency_id, $display_currency_id)
    {
        array_push($this->currency_display_stack, array("main_currency_id" => $main_currency_id, "display_currency_id" => $display_currency_id));
        modApiFunc("Localization", "setLocalMainCurrency", $main_currency_id);
        modApiFunc("Localization", "setLocalDisplayCurrency", $display_currency_id);
    }

    /**
     *   . pushDisplayCurrency()
     *
     */
    function popDisplayCurrency()
    {
    	if(sizeof($this->currency_display_stack) < 2)
    	{
    		//      .                      .   -                            .
    	}
    	else
    	{
    		//             .
            array_pop($this->currency_display_stack);

            //                                .        ,                    .
            $info = array_pop($this->currency_display_stack);
            modApiFunc("Localization", "setLocalMainCurrency",    $info["main_currency_id"]);
            modApiFunc("Localization", "setLocalDisplayCurrency", $info["display_currency_id"]);
            array_push($this->currency_display_stack, $info);
    	}
    }

    function getCurrencySettings()
    {
        $currency = explode('|', $this->CURRENCY);
        $format = explode('|', $this->CURRENCY_FORMAT);
        return array(
            'symbol' => $currency[1],
            'precision' => (int) $format[0],
            'decimal' => $format[1],
            'thousands' => $format[2],
            'positive' => $this->CURRENCY_POSITIVE_FORMAT,
            'negative' => $this->CURRENCY_NEGATIVE_FORMAT,
        );
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