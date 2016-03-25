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
 * Module "Quantity_Discounts"
 *
 * @package Quantity_Discounts
 * @author Vadim Lyalikov
 */
class Quantity_Discounts
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Quantity_Discounts  constructor
     */
    function Quantity_Discounts()
    {
    }

    function getNearestFromValue($product_id, $from_value, $membership, $rates=null, $b_active_only=true)
    {
        $max_from_lessers = null;
        if($rates==null) {
            $rates = $this->getQuantityDiscountRates($b_active_only);
        }
        if(!isset($rates[$product_id]))
        {
            return $max_from_lessers;
        }
        else
        {
            $max_from_lessers = 1;
            foreach($rates[$product_id] as $rate)
            {
                if($rate['customer_group_id']==$membership)
                {
                    if(($rate['rv_from'] <= $from_value) && ($rate['rv_from'] > $max_from_lessers))
                    {
                        $max_from_lessers = $rate['rv_from'];
                    }
                }
            }
        }

        return $max_from_lessers;
    }

    function getNextFromValue($product_id, $from_value, $membership){
        $min_from_greaters = null;
        $rates = $this->getQuantityDiscountRates(false);
        if(!isset($rates[$product_id]))
        {
            return $min_from_greaters;
        }
        else
        {
            $min_from_greaters = -PRICE_N_A;
            foreach($rates[$product_id] as $rate)
            {
                if($rate['customer_group_id']==$membership)
                {
                    if(($rate['rv_from'] > $from_value) && ($rate['rv_from'] < $min_from_greaters))
                    {
                        $min_from_greaters = $rate['rv_from'];
                    }
                }
            }
        }

        return $min_from_greaters;
    }

    function doesAddingRateExist($product_id, $rate_value_from, $membership)
    {
        $ret_struct = array("ret_val" => false,
                            "rate_id" => NULL,
                            "intersection_coord" => NULL);

        $rates = $this->getQuantityDiscountRates(false);
        if(isset($rates[$product_id]))
        {
            foreach($rates[$product_id] as $rate)
            {
                if($rate["rv_from"]==$rate_value_from && $rate["customer_group_id"]==$membership)
                {
                    $ret_struct["ret_val"] = true;
                    $ret_struct["id"] = $rate["id"];
                    $ret_struct["intersection_coord"] = $rate["rv_from"];
                }
            }
        }
        return $ret_struct;
    }

    function getFixedPrice($product_id, $product_quantity, $price, $membership, $b_active_only = true)
    {
        $this->getSettings();

        global $application;
        $tables = $this->getTables();

        $rates = $this->getQuantityDiscountRates($b_active_only);

        if(!isset($rates[$product_id]))
        {
            return $price;
        }
        else
        {
            $from_value = $this->getNearestFromValue($product_id, $product_quantity, $membership, $rates);
            foreach($rates[$product_id] as $rate)
            {
                if(($rate['rv_from'] == $from_value) && ($rate['cost_type_id'] == 3))
                {
                    return $rate['cost'];
                }
            }
        }
        return $price;
    }

    /**
     *                                     .
     *
     *       install()                      .
     *
     *                                          ,         ,
     * Quantity_Discounts::getTables()        $this->getTables()
     */
    function install()
    {
        $tables = Quantity_Discounts::getTables();
        $query = new DB_Table_Create($tables);
    }

    /**
     *                      Checkout.
     *                                                       ,                   ,
     *                  (      )                    .
     *                                  ,      Checkout'
     *   0.00.
     */
    function getQuantityDiscount($product_id, $product_quantity, $not_discounted_product_price, $membership, $b_active_only = true, $is_overall_sum = QD_SINGLE_PRODUCT_PRICE)
    {
        $this->getSettings();

        $tables = $this->getTables();

        $rates = $this->getQuantityDiscountRates($b_active_only);

        if(!isset($rates[$product_id]))
        {
            return PRICE_N_A;
        }
        else
        {
            $from_value = $this->getNearestFromValue($product_id, $product_quantity, $membership, $rates, $b_active_only);
            foreach($rates[$product_id] as $rate)
            {
                if($rate['rv_from'] == $from_value)
                {
                    switch($rate['cost_type_id'])
                    {
                        case 3 /* fixed price */:
                            $discount = 'FIXED_PRICE';
                        break;
                        case 2 /* percent */:
                            $discount = (($not_discounted_product_price * $rate['cost'])/100.0);
                            if (!$is_overall_sum)
                                $discount *= $product_quantity;
                        break;
                        case 1:
                            $discount = $rate['cost'] * $product_quantity;
                        break;
                        default:
                            //:  : report error.
                            $discount = PRICE_N_A;
                        break;
                    }
                    return $discount;
                }
            }
        }
        return PRICE_N_A;
    }


    /**
     * Uninstall the module.
     *                           .
     *
     *       uninstall()                      .
     *
     *                                          ,         ,
     * Quantity_Discounts::getTables()        $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Quantity_Discounts::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     *
     *
     *                                        :
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
     *          'fn1'       #                                            ,          - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      #                                                   ,          - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $table_rates = 'quantity_discounts_rates_table';
        $tables[$table_rates] = array();
        $tables[$table_rates]['columns'] = array
            (
                'id'                => $table_rates.'.discounts_rate_id'
               ,'product_id'        => $table_rates.'.discounts_rate_product_id'
               ,'rv_from'           => $table_rates.'.discounts_rate_value_from'
               ,'cost_type_id'      => $table_rates.'.discounts_rate_cost_type_id'
               ,'cost'              => $table_rates.'.discounts_rate_cost'
               ,'b_active'          => $table_rates.'.discounts_rate_b_active'
               ,'customer_group_id' => $table_rates.'.discounts_rate_customer_group_id'
            );
        $tables[$table_rates]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'product_id'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'rv_from'           => DBQUERY_FIELD_TYPE_FLOAT
               ,'cost_type_id'      => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - CURRENCY, 2 - PERCENT
               ,'cost'              => DBQUERY_FIELD_TYPE_FLOAT
               ,'b_active'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - YES, 2 - NO
               ,'customer_group_id' => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1'
            );
        $tables[$table_rates]['primary'] = array
            (
                'id'
            );
        $tables[$table_rates]['indexes'] = array
            (
                'IDX_rv_from' => 'rv_from'
            );

        $settings = 'quantity_discounts_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.discounts_setting_id'
               ,'key'               => $settings.'.discounts_setting_key'
               ,'value'             => $settings.'.discounts_setting_value'
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
     *                 Settings
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('quantity_discounts_settings');
        $application->db->getDB_Result($query);
    }

    /**
     *                                               Settings
     *
     * @return array -
     */
    function getSettings()
    {
        if (! isset($this->Settings)) {
            $result = execQuery('SELECT_QUANTITY_DISCOUNTS_SETTINGS', array());
            $this->Settings = array();
            for ($i=0; $i<sizeof($result); $i++)
            {
                $this->Settings[$result[$i]['set_key']] = unserialize($result[$i]['set_value']);
            }
        }
        return $this->Settings;
    }

    /**
     *                          -
     *
     * @param array $Settings -
     */
    function setSettings($Settings)
    {
        global $application;
        $this->clearSettingsInDB();
        $tables = $this->getTables();
        $columns = $tables['quantity_discounts_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('quantity_discounts_settings');
            $query->addInsertValue($key, $columns['key']);
            $query->addInsertValue(serialize($value), $columns['value']);
            $application->db->getDB_Result($query);

            $inserted_id = $application->db->DB_Insert_Id();
        }
    }

    /**
     *                          -
     *
     * @param array $Settings -                        .                    ,                   ,
     *                       .
     */
    function updateSettings($Settings)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['quantity_discounts_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('quantity_discounts_settings');
            $query->addUpdateValue($columns['value'], serialize($value));
            $query->WhereValue($columns['key'], DB_EQ, $key);
            $application->db->getDB_Result($query);
        }
    }

    /**
     *                                                          Localization
     *
     * @param integer $ru_type -
     *
     * @return array -
     */
/*    function getRateUnitUnitsValue($ru_type)
    {
        return modApiFunc("Localization", "getUnitTypeValue", $ru_type);
    }
*/

    /**
     *
     *
     * @return array -        of rates, ordered by c_id, s_id, rv_from.
     */
    function getQuantityDiscountRates($b_active_only = true, $cost_type_id = NULL/*$countryId, $stateId, $rate_unit_values_array*/)
    {
        global $zone;
        static $rates;

        $current_customer_group = null;
        if ($zone == 'CustomerZone') {
            $current_customer_group = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
        }
        $hash = ($b_active_only ? '1' : '0').':'.(isset($cost_type_id) ? (int) $cost_type_id : '').':'.(isset($current_customer_group) ? $current_customer_group : '');
        if (! isset($rates[$hash])) {

            $result_rows = execQuery('SELECT_QUANTITY_DISCOUNTS_RATES', array(
            	'b_active_only' => $b_active_only,
                'cost_type_id' => $cost_type_id,
                'current_customer_group' => $current_customer_group
            ));

            $result_array = array();
            $existing_customer_groups = modApiFunc('Customer_Account','getGroups');
            foreach ($result_rows as $row) {
                if (! isset($existing_customer_groups[$row['customer_group_id']])) continue;
                if ($zone == 'CustomerZone') {
                    if ($row['customer_group_id']==$current_customer_group)
                        $result_array[$row['product_id']][] = $row;
                }
                else {
                    $result_array[$row['product_id']][] = $row;
                }
            }
            $rates[$hash] = $result_array;
        }
        return $rates[$hash];
    }

    /**
     *
     *
     * @param array $address_id_array -        id
     */
    function deleteRowsFromQuantityDiscount($rate_id_array)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['quantity_discounts_rates_table']['columns'];

        $query = new DB_Delete('quantity_discounts_rates_table');
        $query->WhereField( $tr['id'], DB_IN, "('".implode("', '", $rate_id_array)."') ");
        $application->db->getDB_Result($query);
    }

    /**
     *                  : Active /   -active.
     *                -active,                                           .
     */
    function updateRowsFromQuantityDiscount($active_rates)
    {
        global $application;

        //                ,                             ,
        //                  View.                    ,               View.
        $YES = 1; //Active
        $NO = 2; //Disabled
        $not_active_rates_ids = array();
        $active_rates_ids = array();
        foreach($active_rates as $rate_id => $status)
        {
            if($status == $YES /* active */)
            {
                $active_rates_ids[] = $rate_id;
            }
            else if($status == $NO/*not active */)
            {
                $not_active_rates_ids[] = $rate_id;
            }
            else
            {
                //: report error.
                exit(1);
            }
        }

        $tables = $this->getTables();
        $columns = $tables['quantity_discounts_rates_table']['columns'];

        $query = new DB_Update('quantity_discounts_rates_table');
        $query->addUpdateExpression($columns['b_active'], $YES);
        $query->WhereField($columns['id'], DB_IN, "('" . implode("','", $active_rates_ids) . "')");
        $application->db->getDB_Result($query);

        $query = new DB_Update('quantity_discounts_rates_table');
        $query->addUpdateExpression($columns['b_active'], $NO);
        $query->WhereField($columns['id'], DB_IN, "('" . implode("','", $not_active_rates_ids) . "')");
        $application->db->getDB_Result($query);
    }

    /**
     *
     *
     * @param array $cost_array -
     */
    function insertQuantityDiscountRates($product_id,$rate_value_from,$rate_cost_type_id,$rate_cost,$membership)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['quantity_discounts_rates_table']['columns'];

        $query = new DB_Insert('quantity_discounts_rates_table');
        $query->addInsertValue($product_id, $tr['product_id']);
        $query->addInsertValue($rate_value_from, $tr['rv_from']);
        $query->addInsertValue($rate_cost_type_id, $tr['cost_type_id']);
        $query->addInsertValue($rate_cost, $tr['cost']);
        $query->addInsertValue($membership, $tr['customer_group_id']);
//        $query->addInsertValue(1 /* YES */, $tr['b_active']);

        $result = $application->db->getDB_Result($query);
    }

    function setQuantityDiscountRowActive($rate_id, $rate_status)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['quantity_discounts_rates_table']['columns'];

        $query = new DB_Update('quantity_discounts_rates_table');
        $query->addUpdateValue($columns['b_active'], $rate_status);
        $query->WhereValue($columns['id'], DB_EQ, $rate_id);
        $application->db->getDB_Result($query);
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

    var $is_debug;
    var $_debug_info;
    var $Settings;

    /**#@-*/
}
?>