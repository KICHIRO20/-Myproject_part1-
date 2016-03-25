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
 * Module "Discounts"
 *
 * @package Discounts
 * @author Vadim Lyalikov
 */
class Discounts
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Discounts  constructor
     */
    function Discounts()
    {
    }

    /**
     *                                     .
     *
     *       install()                      .
     *
     *                                          ,         ,
     * Discounts::getTables()        $this->getTables()
     */
    function install()
    {
        $tables = Discounts::getTables();
        $query = new DB_Table_Create($tables);
    }

    /**
     *                      Checkout.
     */
    function getGlobalDiscount($order_subtotal, $b_active_only = true)
    {
        $this->getSettings();

        global $application;
        $tables = $this->getTables();

        $rates = $this->getGlobalDiscountRates($b_active_only);

        foreach($rates as $rate)
        {
            // : get EPS from settings
            $EPS = 0.0001;
            if($order_subtotal + $EPS >= $rate['rv_from'] &&
               $order_subtotal - $EPS <= $rate['rv_to'])
            {
                switch($rate['cost_type_id'])
                {
                    case 2 /* percent */:
                    {
                        $discount = ($order_subtotal * $rate['cost'])/100.0;
                        break;
                    }
                    case 1:
                    {
                        $discount = $rate['cost'];
                        break;
                    }
                    default:
                    {
                        //:  : report error.
                        $discount = PRICE_N_A;
                        break;
                    }
                }
                return $discount;
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
     * Discounts::getTables()        $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Discounts::getTables());
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

        $table_rates = 'discounts_global_discount_rates_table';
        $tables[$table_rates] = array();
        $tables[$table_rates]['columns'] = array
            (
                'id'                => $table_rates.'.discounts_rate_id'
               ,'rv_from'           => $table_rates.'.discounts_rate_value_from'
               ,'rv_to'             => $table_rates.'.discounts_rate_value_to'
               ,'cost_type_id'      => $table_rates.'.discounts_rate_cost_type_id'
               ,'cost'              => $table_rates.'.discounts_rate_cost'
               ,'b_active'          => $table_rates.'.discounts_rate_b_active'
            );
        $tables[$table_rates]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'rv_from'           => DBQUERY_FIELD_TYPE_FLOAT
               ,'rv_to'             => DBQUERY_FIELD_TYPE_FLOAT
               ,'cost_type_id'      => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - CURRENCY, 2 - PERCENT
               ,'cost'              => DBQUERY_FIELD_TYPE_FLOAT
               ,'b_active'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - YES, 2 - NO
            );
        $tables[$table_rates]['primary'] = array
            (
                'id'
            );
        $tables[$table_rates]['indexes'] = array
            (
                'IDX_rv_from' => 'rv_from'
               ,'IDX_rv_to' => 'rv_to'
            );

        $settings = 'discounts_settings';
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
        $query = new DB_Delete('discounts_settings');
        $application->db->getDB_Result($query);
    }

    /**
     *                                               Settings
     *
     * @return array -
     */
    function getSettings()
    {
        $result = execQuery('SELECT_DISCOUNTS_SETTINS', array());
        $this->Settings = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $this->Settings[$result[$i]['set_key']] = unserialize($result[$i]['set_value']);
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
        $columns = $tables['discounts_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('discounts_settings');
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
        $columns = $tables['discounts_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('discounts_settings');
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
    function getGlobalDiscountRates($b_active_only = true, $cost_type_id = NULL/*$countryId, $stateId, $rate_unit_values_array*/)
    {
        $params = array('b_active_only' => $b_active_only,
                        'cost_type_id'  => $cost_type_id);
        $result_rows = execQuery('SELECT_GLOBAL_DISCOUNT_RATES', $params);
        $result_array = array();
        foreach($result_rows as $row)
        {
            $result_array[] = $row;
        }
        return $result_array;
    }

    /**
     *
     *
     * @param array $address_id_array -        id
     */
    function deleteRowsFromGlobalDiscount($rate_id_array)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['discounts_global_discount_rates_table']['columns'];

        $query = new DB_Delete('discounts_global_discount_rates_table');
        $query->WhereField( $tr['id'], DB_IN, "('".implode("', '", $rate_id_array)."') ");
        $application->db->getDB_Result($query);
    }

    /**
     *                  : Active /   -active.
     *                -active,                                           .
     */
    function updateRowsFromGlobalDiscount($active_rates)
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
        $columns = $tables['discounts_global_discount_rates_table']['columns'];

        $query = new DB_Update('discounts_global_discount_rates_table');
        $query->addUpdateExpression($columns['b_active'], $YES);
        $query->WhereField($columns['id'], DB_IN, "('" . implode("','", $active_rates_ids) . "')");
        $application->db->getDB_Result($query);

        $query = new DB_Update('discounts_global_discount_rates_table');
        $query->addUpdateExpression($columns['b_active'], $NO);
        $query->WhereField($columns['id'], DB_IN, "('" . implode("','", $not_active_rates_ids) . "')");
        $application->db->getDB_Result($query);
    }

    /**
     *          ,                                 .         -                     .
     *                   (                     ) -            true,       - false.
     */
    function doTwoIntervalsIntersect($from1, $to1, $from2, $to2)
    {
        return !(($to1 < $from2) || ($to2 < $from1));
    }

    /**
     *          ,                                                  .
     * @param $EPS          (           )                                .
     * @return false -                                      ,                ,
     *                           ,       -                                   -
     *                                          .
     */
    function doTwoIntervalsIntersectInSinglePoint($EPS, $from1, $to1, $from2, $to2)
    {
        $ret_struct = array("ret_val" => false,
                            "intersection_coord" => NULL);
        if(!$this->doTwoIntervalsIntersect($from1, $to1, $from2, $to2))
        {
        }
        else
        {
            if(($from1 < $from2) &&
               (abs($to1 - $from2) < 0.5 * $EPS))
            {
                $ret_struct["ret_val"] = true;
                $ret_struct["intersection_coord"] = $to1;
            }
            else
            if(($from2 < $from1) &&
               (abs($to2 - $from1) < 0.5 * $EPS))
            {
                $ret_struct["ret_val"] = true;
                $ret_struct["intersection_coord"] = $to2;
            }
            else
            {
            }
        }
        return $ret_struct;
    }

    /**
     *          ,                                                   -
     *            .                                   -            true.       - false.
     */
    function doesAddingRateCreateRateIntervalsIntersection($EPS,
                                                           $rate_value_from,
                                                           $rate_value_to)
    {
        $ret_struct = array("ret_val" => false,
                            "rate_id" => NULL,
                            "intersection_coord" => NULL);
        //Fetch all rates with same $rate_unit_id
        $rates = $this->getGlobalDiscountRates(false);
        foreach($rates as $rate)
        {
            if($this->doTwoIntervalsIntersect($rate["rv_from"], $rate["rv_to"], $rate_value_from, $rate_value_to))
            {
                $ret_struct["ret_val"] = true;
                $ret_struct["id"] = $rate["id"];
                $_ret_struct = $this->doTwoIntervalsIntersectInSinglePoint($EPS, $rate["rv_from"], $rate["rv_to"], $rate_value_from, $rate_value_to);
                if($_ret_struct["ret_val"] === true)
                {
                    $ret_struct["intersection_coord"] = $_ret_struct["intersection_coord"];
                }
            }
        }

        return $ret_struct;
    }

    /**
     *
     *
     * @param array $cost_array -
     */
    function insertGlobalDiscountRates($rate_value_from, $rate_value_to, $rate_cost_type_id, $rate_cost)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['discounts_global_discount_rates_table']['columns'];

        $query = new DB_Insert('discounts_global_discount_rates_table');
        $query->addInsertValue($rate_value_from, $tr['rv_from']);
        $query->addInsertValue($rate_value_to, $tr['rv_to']);
        $query->addInsertValue($rate_cost_type_id, $tr['cost_type_id']);
        $query->addInsertValue($rate_cost, $tr['cost']);
//        $query->addInsertValue(1 /* YES */, $tr['b_active']);

        $result = $application->db->getDB_Result($query);
    }

    function setGlobalDiscountRowActive($rate_id, $rate_status)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['discounts_global_discount_rates_table']['columns'];

        $query = new DB_Update('discounts_global_discount_rates_table');
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

    /**#@-*/
}
?>