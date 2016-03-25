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
 * Module "PromoCodes"
 *
 * @package PromoCodes
 * @author Vadim Lyalikov
 */
class PromoCodes
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * PromoCodes  constructor
     */
    function PromoCodes()
    {
        if(modApiFunc('Session', 'is_Set', 'PromoCodeId'))
        {
            $this->PromoCodeId = modApiFunc('Session', 'get', 'PromoCodeId');
            $this->AddPromoCodeError = modApiFunc('Session', 'get', 'AddPromoCodeError');
        }
        else
        {
            $this->PromoCodeId = 0;
            modApiFunc('Session', 'set', 'PromoCodeId', 0);
            $this->AddPromoCodeError = 0;
            modApiFunc('Session', 'set', 'AddPromoCodeError', 0);
        }

    }

    function getPromoCodeId()
    {
        return $this->PromoCodeId;
    }

    function getAddPromoCodeError()
    {
        return $this->AddPromoCodeError;
    }

    function isPromoCodeIdSet()
    {
        return !($this->PromoCodeId === 0);
    }

    function setPromoCodeId($promo_code_id)
    {
        $this->PromoCodeId = $promo_code_id;
        modApiFunc('Session', 'set', 'PromoCodeId', $this->PromoCodeId);
    }

    function setAddPromoCodeError($add_promo_code_error)
    {
        $this->AddPromoCodeError = $add_promo_code_error;
        modApiFunc('Session', 'set', 'AddPromoCodeError', $this->AddPromoCodeError);
    }

    function removePromoCode()
    {
        $this->PromoCodeId = 0;
        modApiFunc('Session', 'set', 'PromoCodeId', $this->PromoCodeId);
    }


    /**
     *                                     .
     *
     *       install()                      .
     *
     *                                          ,         ,
     * PromoCodes::getTables()        $this->getTables()
     */
    function install()
    {
        global $application;

        $tables = PromoCodes::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'promo_codes_settings';            #
        $columns = $tables[$table]['columns'];  #


        // advanced settings parameter
        $group_info = array('GROUP_NAME'        => 'PROMO_CODE_PARAMS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('PROMOCODES', 'PROMO_CODE_GROUP_NAME'),
                                                            'DESCRIPTION'   => array('PROMOCODES', 'PROMO_CODE_GROUP_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW');

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALWAYS_SHOW_PROMO_CODES',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PROMOCODES', 'PROMO_CODE_ALWAYS_SHOW_PARAM_NAME'),
                                                       'DESCRIPTION' => array('PROMOCODES', 'PROMO_CODE_ALWAYS_SHOW_PARAM_DESCRIPTION') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PROMOCODES', 'PROMO_CODE_NO'),
                                                                       'DESCRIPTION' => array('PROMOCODES', 'PROMO_CODE_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PROMOCODES', 'PROMO_CODE_YES'),
                                                                       'DESCRIPTION' => array('PROMOCODES', 'PROMO_CODE_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
    }


    /**
     * function checks if the given promo code is applicable
     *
     * @param float $order_subtotal
     * @param int $promo_code_id
     * @param object $cart
     */
    function isPromoCodeApplicable($order_subtotal, $promo_code_id, &$cart)
    {
        // invalid coupon id
        if (($pc_info = $this->getPromoCodeInfo($promo_code_id)) === false)
            return false;

        // invalid times to use etc.
        if ($this->isPromoCodeApplicableWithoutMinSubtotal($promo_code_id) === false)
            return false;

        // invalid subtotal
        $EPS = 0.0001;
        if ($order_subtotal + $EPS < $pc_info['min_subtotal'])
            return false;

        // check area and valid strict cart
        if ($this->isPromoCodeAreaApplicable($pc_info, $cart) === false)
            return false;

        return true;
    }

	function getPromoCodeDiscount($order_subtotal, $promo_code_id, $cart)
	{
	    if ($this->isPromoCodeApplicable($order_subtotal, $promo_code_id, $cart) === false)
	       return PRICE_N_A;

        // valid coupon, apply
        $pc_info = $this->getPromoCodeInfo($promo_code_id);
        switch($pc_info['discount_cost_type_id'])
        {
            case 2: // percent value
            {
                // strict cart
                if ($pc_info['strict_cart'] == PROMO_CODE_STRICT_CART)
                {
                    $discount = ($order_subtotal * $pc_info['discount_cost'])/100.0;
                }
                else // if ($pc_info['strict_cart'] == PROMO_CODE_DIRTY_CART)
                {
                    $discount = 0.0;
                    foreach ($cart as $product)
                    {
                        if ($product['applicable'] == true)
                        {
                            $discount += ($product['total'] * $pc_info['discount_cost']) / 100.0;
                        }
                    }
                }
                break;
            }
            case 1: // absolute value
            {
                $discount = $pc_info['discount_cost'];
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

    /*
     * Returns the total number of promo codes in the database
     */
    function getPromoCodesNumber ()
    {
        global $application;

        $query = new DB_Select();
        $query->addSelectField('COUNT(*)', 'coupons_number');
        $query->addSelectTable('promo_codes_coupons_table');

        $res = $application->db->getDB_Result($query);

        return $res[0]['coupons_number'];
    }

    /**
     * min_subtotal                                 .
     */
    function isPromoCodeApplicableWithoutMinSubtotal($promo_code_id)
    {
    	$pc_info = $this->getPromoCodeInfo($promo_code_id);
    	//Exists in data base.
    	if($pc_info === false)
    	{
    		return false;
    	}

    	//Times to use is sufficient.
    	if($pc_info['times_used'] >= $pc_info['times_to_use'])
    	{
    		return PROMO_CODE_NOT_APPLICABLE_TIMES_USED;
    	}

    	//Actve only.
    	if($pc_info['status'] != 1)
    	{
    		return PROMO_CODE_NOT_APPLICABLE_STATUS;
    	}

        //           Start             ,   Expire             .

        //         ,                                                     ,                .
        //                                .                   .
        //         ,                          0       0       0        start_date
        //                                    0       0       0        (end_date + 1     )           .
		{
            if($this->is_coupon_not_expired(strtotime($pc_info['start_date']), strtotime($pc_info['end_date'])) === true)
            {
            }
            else
            {
                //                                   ,                   .
            	return PROMO_CODE_NOT_APPLICABLE_DATE;
            }
        }
        return true;
    }
    /**
     * Uninstall the module.
     *                           .
     *
     *       uninstall()                      .
     *
     *                                          ,         ,
     * PromoCodes::getTables()        $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(PromoCodes::getTables());
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

        $table_coupons = 'promo_codes_coupons_table';
        $tables[$table_coupons] = array();
        $tables[$table_coupons]['columns'] = array
            (
                'id'                    => $table_coupons.'.coupon_id'
               ,'min_subtotal'          => $table_coupons.'.coupon_min_subtotal'
               ,'promo_code'            => $table_coupons.'.coupon_promo_code'
               ,'campaign_name'         => $table_coupons.'.coupon_campaign_name'
               ,'b_ignore_other_discounts' =>  $table_coupons.'.coupon_b_ignore_other_discounts'
               ,'status'                => $table_coupons.'.coupon_status'
               ,'discount_cost'         => $table_coupons.'.coupon_discount_cost'
               ,'discount_cost_type_id' => $table_coupons.'.coupon_discount_cost_type_id'
               ,'start_date'            => $table_coupons.'.coupon_start_date'
               ,'end_date'              => $table_coupons.'.coupon_end_date'
               ,'times_to_use'          => $table_coupons.'.coupon_times_to_use'
               ,'times_used'            => $table_coupons.'.coupon_times_used'
               ,'products_affected'     => $table_coupons.'.coupon_products_affected'
               ,'categories_affected'   => $table_coupons.'.coupon_categories_affected'
               ,'free_shipping'         => $table_coupons.'.free_shipping'
               ,'free_handling'         => $table_coupons.'.free_handling'
               ,'strict_cart'           => $table_coupons.'.strict_cart'
            );
        $tables[$table_coupons]['types'] = array
            (
                'id'                    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'min_subtotal'          => DBQUERY_FIELD_TYPE_FLOAT
               ,'promo_code'            => DBQUERY_FIELD_TYPE_CHAR255
               ,'campaign_name'         => DBQUERY_FIELD_TYPE_CHAR255
               ,'b_ignore_other_discounts' =>  DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - YES, 2 - NO
               ,'status'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - YES (active), 2 - NO (not active)
               ,'discount_cost'         => DBQUERY_FIELD_TYPE_FLOAT
               ,'discount_cost_type_id' => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - CURRENCY, 2 - PERCENT
               ,'start_date'            => DBQUERY_FIELD_TYPE_DATE
               ,'end_date'              => DBQUERY_FIELD_TYPE_DATE
               ,'times_to_use'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'times_used'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'products_affected'     => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'categories_affected'   => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'free_shipping'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'free_handling'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'strict_cart'           => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
            );
        $tables[$table_coupons]['primary'] = array
            (
                'id'
            );
        $tables[$table_coupons]['indexes']=array(
            'UNIQUE KEY IDX_promo_code_key'   => 'promo_code'
        );


        $settings = 'promo_codes_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.promo_codes_setting_id'
               ,'key'               => $settings.'.promo_codes_setting_key'
               ,'value'             => $settings.'.promo_codes_setting_value'
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

        $table_order_promo_codes = 'order_promo_codes';
        $tables[$table_order_promo_codes] = array();
        $tables[$table_order_promo_codes]['columns'] = array
            (
                'order_id'              => $table_order_promo_codes.'.order_id'
               ,'coupon_id'             => $table_order_promo_codes.'.coupon_id'
               ,'coupon_promo_code'     => $table_order_promo_codes.'.coupon_promo_code'
            );
        $tables[$table_order_promo_codes]['types'] = array
            (
                'order_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'coupon_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'coupon_promo_code'     => DBQUERY_FIELD_TYPE_CHAR255
            );
        $tables[$table_order_promo_codes]['primary'] = array
            (
                'order_id'
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
        $query = new DB_Delete('promo_codes_settings');
        $application->db->getDB_Result($query);
    }

    /**
     *                                               Settings
     *
     * @return array -
     */
    function getSettings()
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['promo_codes_settings']['columns'];

        $query = new DB_Select();
        $query->addSelectField($columns["key"], "set_key");
        $query->addSelectField($columns["value"], "set_value");
        $result = $application->db->getDB_Result($query);
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
        $columns = $tables['promo_codes_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('promo_codes_settings');
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
        $columns = $tables['promo_codes_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('promo_codes_settings');
            $query->addUpdateValue($columns['value'], serialize($value));
            $query->WhereValue($columns['key'], DB_EQ, $key);
            $application->db->getDB_Result($query);
        }
    }


    /**
     *                                  0      , 0        , 0         .
     *           UNIX TIME (                     1        1970  ).
     */
    function get_zero_time_for_a_date($fyear, $fmonth, $fday)
    {
    	return mktime (0,0,0,$fmonth,$fday,$fyear);
    }

    /**
     * Input - CORRECT Gregorian date.
     *                .               .                    .
     */
	function next_day($fyear, $fmonth, $fday)
	{
	  return date ("Y-m-d", mktime (0,0,0,$fmonth,$fday+1,$fyear));
	}

    //                           Non-Applicable                                     .
    function getPromoCodesListFullAZ($promo_code_id = NULL, $use_paginator=false)
    {
    	return $this->getPromoCodesListFull(false, NULL, false, false, $promo_code_id, $use_paginator);
    }

    //                    Applicable                                     .
    function getPromoCodesListFullCZ($promo_code_id = NULL)
    {
        return $this->getPromoCodesListFull(true, NULL, true, true, $promo_code_id, false);
    }

    function is_coupon_not_expired($start_date, $end_date)
    {
        $start_date_year = date("Y", $start_date);
        $start_date_month = date("m", $start_date);
        $start_date_day = date("d", $start_date);
        $start_time_including = $this->get_zero_time_for_a_date($start_date_year, $start_date_month, $start_date_day);

        $end_date_fyear = date("Y", $end_date);
        $end_date_fmonth = date("m", $end_date);
        $end_date_fday = date("d", $end_date);

        $end_date_plus_one_day = strtotime($this->next_day($end_date_fyear, $end_date_fmonth, $end_date_fday));

        $end_date_plus_one_day_fyear = date("Y", $end_date_plus_one_day);
        $end_date_plus_one_day_fmonth = date("m", $end_date_plus_one_day);
        $end_date_plus_one_day_fday = date("d", $end_date_plus_one_day);

        $end_time_not_including = $this->get_zero_time_for_a_date($end_date_plus_one_day_fyear, $end_date_plus_one_day_fmonth, $end_date_plus_one_day_fday);
        $time_shift_in_seconds = modApiFunc('Configuration', 'getValue', 'store_time_shift') * 3600;
        $current_time = mktime() + $time_shift_in_seconds;

        if($current_time >= $start_time_including &&
           $current_time  < $end_time_not_including)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     *
     * @return array -               .
     */
    function getPromoCodesListFull($b_active_only = true, $cost_type_id = NULL, $b_between_start_date_and_date_only = true, $b_times_to_use_greater_than_times_used = true, $promo_code_id = NULL, $use_paginator=false)
    {
        global $application;
///        $tables = call_user_func(array($__CLASS__, "getTables");
        $tables = $this->getTables();
        $tr = $tables['promo_codes_coupons_table']['columns'];

        $result_array = array();
        $query = new DB_Select();
        $query->addSelectField($tr["id"], "id");
        $query->addSelectField($tr["min_subtotal"], "min_subtotal");
        $query->addSelectField($tr["promo_code"], "promo_code");
        $query->addSelectField($tr["campaign_name"], "campaign_name");
        $query->addSelectField($tr["b_ignore_other_discounts"], "b_ignore_other_discounts");
        $query->addSelectField($tr["status"], "status");
        $query->addSelectField($tr["discount_cost"], "discount_cost");
        $query->addSelectField($tr["discount_cost_type_id"], "discount_cost_type_id");
        $query->addSelectField($tr["start_date"], "start_date");
        $query->addSelectField($tr["end_date"], "end_date");
        $query->addSelectField($tr["times_to_use"], "times_to_use");
        $query->addSelectField($tr["times_used"], "times_used");
        $query->addSelectField($tr["categories_affected"], "cats");
        $query->addSelectField($tr["products_affected"], "prods");
        $query->addSelectField($tr["free_shipping"], "free_shipping");
        $query->addSelectField($tr["free_handling"], "free_handling");
        $query->addSelectField($tr["strict_cart"], "strict_cart");

        $query->WhereValue('', '', '1');

        if($cost_type_id !== NULL)
        {
            $query->WhereAnd();
            $query->WhereValue($tr["discount_cost_type_id"], DB_EQ, $cost_type_id);
        }
        if($b_active_only === true)
        {
            $query->WhereAND();
            $query->WhereValue($tr["status"], DB_EQ, 1 /* YES */);
        }
        if($b_times_to_use_greater_than_times_used === true)
        {
            $query->WhereAND();
            $query->WhereField($tr["times_to_use"], DB_GT, $tr["times_used"]);
        }

        if($promo_code_id !== NULL)
        {
            $query->WhereAND();
            $query->WhereValue($tr["id"], DB_EQ, $promo_code_id);
        }

        if($use_paginator == true)
        {
            $query = modApiFunc('paginator', 'setQuery', $query);
        }
        $query->SelectOrder($tr['id']);

        $result_rows = $application->db->getDB_Result($query);

        foreach($result_rows as $row)
        {
            if($b_between_start_date_and_date_only === true)
            {
	            //         ,                                                     ,                .
	            //                                .                   .
	            //         ,                          0       0       0        start_date
	            //                                    0       0       0        (end_date + 1     )           .

                if($this->is_coupon_not_expired(strtotime($row['start_date']), strtotime($row['end_date'])) === true)
	            {
                    $result_array[] = $row;
	            }
	            else
	            {
	                //                                   ,                   .
	            }
            }
            else
            {
                $result_array[] = $row;
            }
        }

        return $result_array;
    }

    function getPromoCodeInfo($promo_code_id)
	{
		$res_array = $this->getPromoCodesListFullAZ($promo_code_id);
		if(sizeof($res_array) == 1)
		{
			return $res_array[0];
		}
		else
		{
			//: report error
			return false;
		}
	}

	function getPromoCodeIdByPromoCode($promo_code)
	{
		$res_array = $this->getPromoCodesListFullAZ();
		foreach($res_array as $pc_info)
		{
			if(_ml_strtolower($pc_info['promo_code']) === _ml_strtolower($promo_code))
			{
				return $pc_info['id'];
			}
		}
        return false;
	}

    function insertOrderCoupon($order_id, $coupon_id)
    {
        global $application;
        $tables = $this->getTables();

        $coupon_promo_code_info = $this->getPromoCodeInfo($coupon_id);
        if($coupon_promo_code_info != false)
        {
            $coupon_promo_code = $coupon_promo_code_info['promo_code'];
        }
        else
        {
            //: report error
            return;
        }

        $tr = $tables['order_promo_codes']['columns'];

        $query = new DB_Insert('order_promo_codes');
        $query->addInsertValue($order_id, $tr['order_id']);
        $query->addInsertValue($coupon_id, $tr['coupon_id']);
        $query->addInsertValue($coupon_promo_code, $tr['coupon_promo_code']);
        $result = $application->db->getDB_Result($query);
    }

    function getOrderCoupons($order_id = NULL, $coupon_id = NULL)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['order_promo_codes']['columns'];

        $result_array = array();
        $query = new DB_Select();
        $query->addSelectField($tr["order_id"], "order_id");
        $query->addSelectField($tr["coupon_id"], "coupon_id");
        $query->addSelectField($tr["coupon_promo_code"], "coupon_promo_code");
        $query->WhereValue('', '', '1');

        if($order_id !== NULL)
        {
            $query->WhereAnd();
            $query->WhereValue($tr["order_id"], DB_EQ, $order_id);
        }
        if($coupon_id !== NULL)
        {
            $query->WhereAND();
            $query->WhereValue($tr["coupon_id"], DB_EQ, $coupon_id);
        }
        $result_rows = $application->db->getDB_Result($query);

        return $result_rows;
    }

    function DeleteOrders($ordersId)
    {
        global $application;

        $tables = $this->getTables();
        $opc = $tables['order_promo_codes']['columns'];
        $DB_IN_string = "('".implode("', '", $ordersId)."')";

        $query = new DB_Delete('order_promo_codes');
        $query->WhereField($opc['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);
    }

    function insertPromoCode
    (
        $PromoCodeCampaignName,
        $PromoCodePromoCode,
        $PromoCodeBIgnoreOtherDiscounts,
        $PromoCodeStatus,
        $PromoCodeMinSubtotal,
        $PromoCodeDiscountCost,
        $PromoCodeDiscountCostTypeId,
        $PromoCodeStartDateFYear,
        $PromoCodeStartDateMonth,
        $PromoCodeStartDateDay,
        $PromoCodeEndDateFYear,
        $PromoCodeEndDateMonth,
        $PromoCodeEndDateDay,
        $PromoCodeTimesToUse,
        $PromoCodeFreeShipping,
        $PromoCodeFreeHandling,
        $PromoCodeStrictCart
    )
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['promo_codes_coupons_table']['columns'];

        $query = new DB_Insert('promo_codes_coupons_table');

        $PromoCodeStartDate = date("Y-m-d", mktime(0, 0, 0, $PromoCodeStartDateMonth, $PromoCodeStartDateDay, $PromoCodeStartDateFYear));
        $PromoCodeEndDate = date("Y-m-d", mktime(0, 0, 0, $PromoCodeEndDateMonth, $PromoCodeEndDateDay, $PromoCodeEndDateFYear));

        $query->addInsertValue($PromoCodeMinSubtotal, $tr['min_subtotal']);
        $query->addInsertValue($PromoCodePromoCode, $tr['promo_code']);
        $query->addInsertValue($PromoCodeCampaignName, $tr['campaign_name']);
        $query->addInsertValue($PromoCodeBIgnoreOtherDiscounts, $tr['b_ignore_other_discounts']);
        $query->addInsertValue($PromoCodeStatus, $tr['status']);
        $query->addInsertValue($PromoCodeDiscountCost, $tr['discount_cost']);
        $query->addInsertValue($PromoCodeDiscountCostTypeId, $tr['discount_cost_type_id']);
        $query->addInsertValue($PromoCodeStartDate, $tr['start_date']);
        $query->addInsertValue($PromoCodeEndDate, $tr['end_date']);
        $query->addInsertValue($PromoCodeTimesToUse, $tr['times_to_use']);
        $query->addInsertValue($PromoCodeFreeShipping, $tr['free_shipping']);
        $query->addInsertValue($PromoCodeFreeHandling, $tr['free_handling']);
        $query->addInsertValue($PromoCodeStrictCart, $tr['strict_cart']);
        $query->addInsertValue('1', $tr['categories_affected']);

        $result = $application->db->getDB_Result($query);
        return $application->db->DB_Insert_Id();
    }

    function updatePromoCode
    (
        $PromoCodeID,
        $PromoCodeCampaignName,
        $PromoCodePromoCode,
        $PromoCodeBIgnoreOtherDiscounts,
        $PromoCodeStatus,
        $PromoCodeMinSubtotal,
        $PromoCodeDiscountCost,
        $PromoCodeDiscountCostTypeId,
        $PromoCodeStartDateFYear,
        $PromoCodeStartDateMonth,
        $PromoCodeStartDateDay,
        $PromoCodeEndDateFYear,
        $PromoCodeEndDateMonth,
        $PromoCodeEndDateDay,
        $PromoCodeTimesToUse,
        $PromoCodeFreeShipping,
        $PromoCodeFreeHandling,
        $PromoCodeStrictCart
    )
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['promo_codes_coupons_table']['columns'];

        $query = new DB_Update('promo_codes_coupons_table');

        $PromoCodeStartDate = date("Y-m-d", mktime(0, 0, 0, $PromoCodeStartDateMonth, $PromoCodeStartDateDay, $PromoCodeStartDateFYear));
        $PromoCodeEndDate = date("Y-m-d", mktime(0, 0, 0, $PromoCodeEndDateMonth, $PromoCodeEndDateDay, $PromoCodeEndDateFYear));

        $query->addUpdateValue($tr['min_subtotal'], $PromoCodeMinSubtotal);
        $query->addUpdateValue($tr['promo_code'], $PromoCodePromoCode);
        $query->addUpdateValue($tr['campaign_name'], $PromoCodeCampaignName);
        $query->addUpdateValue($tr['b_ignore_other_discounts'], $PromoCodeBIgnoreOtherDiscounts);
        $query->addUpdateValue($tr['status'], $PromoCodeStatus);
        $query->addUpdateValue($tr['discount_cost'], $PromoCodeDiscountCost);
        $query->addUpdateValue($tr['discount_cost_type_id'], $PromoCodeDiscountCostTypeId);
        $query->addUpdateValue($tr['start_date'], $PromoCodeStartDate);
        $query->addUpdateValue($tr['end_date'], $PromoCodeEndDate);
        $query->addUpdateValue($tr['times_to_use'], $PromoCodeTimesToUse);
        $query->addUpdateValue($tr['free_shipping'], $PromoCodeFreeShipping);
        $query->addUpdateValue($tr['free_handling'], $PromoCodeFreeHandling);
        $query->addUpdateValue($tr['strict_cart'], $PromoCodeStrictCart);

        $query->WhereValue($tr['id'], DB_EQ, $PromoCodeID);
        $application->db->getDB_Result($query);
    }

    function updatePromoCodeTimesUsed($PromoCodeID)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['promo_codes_coupons_table']['columns'];

        $query = new DB_Update('promo_codes_coupons_table');
        $query->addUpdateExpression($tr['times_used'], $tr['times_used'] . "+1");
        $query->WhereValue($tr['id'], DB_EQ, $PromoCodeID);
        $application->db->getDB_Result($query);
    }

    /**
     *
     *
     * @param array $address_id_array -        id
     */
    function deleteRowsFromPromoCode($id_array)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['promo_codes_coupons_table']['columns'];

        $query = new DB_Delete('promo_codes_coupons_table');
        $query->WhereField( $tr['id'], DB_IN, "('".implode("', '", $id_array)."') ");
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

    /**
     * Sets editable promo code ID.
     * :                                     ,                                                .
     *
     */
    function setEditablePromoCodeID($epmid)
    {
//        if ($this->isCorrectPromoCodeId($epmid))
//        {
            $this->editablePromoCodeID = $epmid;
//        }
    }

    function unsetEditablePromoCodeID()
    {
        $this->editablePromoCodeID=NULL;
    }

    /**
     * Get editable promo code ID.
     *
     * @return integer Editable promo code ID.
     */
    function getEditablePromoCodeID()
    {
        return $this->editablePromoCodeID;
    }

    /**
     *                            .
     */
    function saveState()
    {
        modApiFunc('Session', 'set', 'editablePromoCodeID', $this->editablePromoCodeID);
    }

    /**
     *                                          .
     */
    function loadState()
    {
        //                                    promo code
        if(modApiFunc('Session', 'is_Set', 'editablePromoCodeID'))
        {
            $this->setEditablePromoCodeID(modApiFunc('Session', 'get', 'editablePromoCodeID'));
        }
        else
        {
            $this->editablePromoCodeID = NULL;
        }
    }

    /**
     * function gets a PromoCode ID
     * returns an array of products, affected by this coupon
     *
     * @param unknown_type $id
     */
    function getCatsProductsAffected($id)
    {
        $params = array('id' => $id);
        $res = execQuery('PROMOCODE_SELECT_PRODUCTS_AFFECTED', $params);
        if (!$res)
        {
            return array('prods' => array(), 'cats' => array());
        }
        $res = $res[0];

        if ($res['prods'] && !empty($res['prods']))
            $res['prods'] = explode('|', $res['prods']);
        else
            $res['prods'] = array();

        if ($res['cats'] && !empty($res['cats']))
            $res['cats']  = explode('|', $res['cats']);
        else
            $res['cats'] = array();

        return $res;
    }

    function isPromoCodeEffectiveAreaNotEmpty($id)
    {
        $area = $this->getCatsProductsAffected($id);
        if (empty($area['prods']) && empty($area['cats']))
            return false;

        return true;
    }

    /**
     * returns categories' parent paths
     *
     */
    function dictGetCategoryFullPath($cid)
    {
        if (isset($this->cat_array[$cid]))
            return $this->cat_array[$cid];

        $path = modApiFunc('Catalog', 'getCategoryFullPath', $cid);

        $current_path = array();
        foreach ($path as $cat)
        {
            $current_path[] = $cat;
        	if (!isset($this->cat_array[$cat['id']]))
        	   $this->cat_array[$cat['id']] = $current_path;
        }

        if (isset($this->cat_array[$cid]))
            return $this->cat_array[$cid];

        return array();
    }

    function registerAffectedCategory($cid)
    {
        $this->affected_cats[$cid] = true;
    }

    function registerAffectedProduct($pid)
    {
        $this->affected_prods[$pid] = true;
    }

    function clearAffected()
    {
        $this->affected_cats = array();
        $this->affected_prods = array();
    }

    /**
     * gets category id to be checked
     * returns whether the category belongs to the coupons effective area
     *
     * @param int $cid
     * @return bool
     */
    function isAffectedCategory($cid)
    {
        $path = $this->dictGetCategoryFullPath($cid);
        foreach ($path as $node)
        {
            if (isset($this->affected_cats[$node['id']]))
                return true;
        }
        return false;
    }

    /**
     * gets a product id to be checked
     * returns whether the product belongs to the coupons effective area products
     * do not checks products' category or parent categories
     *
     * @param int $pid
     * @param int $cid
     * @return bool
     */
    function isAffectedProduct($pid, $cid)
    {
        if (isset($this->affected_prods[$pid]))
            return true;

		#ignoring $cid now, so category id is being fetched using pid.
		$mcids = modApiFunc('Catalog','getMultiCatByPid',$pid);
		foreach($mcids as $mcid)
		{
			if($this->isAffectedCategory($mcid))
				return true;
		}
        return false;
    }

    function isPromoCodeAreaApplicable($pc_info, &$cart)
    {
        $this->clearAffected();

        $affected = $this->getCatsProductsAffected($pc_info['id']);
        foreach ($affected['prods'] as $pid)
        {
            $this->registerAffectedProduct($pid);
        }
        foreach ($affected['cats'] as $cid)
        {
            $this->registerAffectedCategory($cid);
        }

        $applicable = ($pc_info['strict_cart'] == PROMO_CODE_STRICT_CART) ? true : false;
        foreach ($cart as $generic_id => $product)
        {
            if ($this->isAffectedProduct($product['id'], $product['cat']))
            {
                $cart[$generic_id]['applicable'] = true;
                if ($pc_info['strict_cart'] == PROMO_CODE_DIRTY_CART)
                    $applicable = true;
            }
            else
            {
                $cart[$generic_id]['applicable'] = false;
                // strict cart demand - no products outside effective area allowed
                if ($pc_info['strict_cart'] == PROMO_CODE_STRICT_CART)
                    $applicable = false;
            }
        }

        return $applicable;
    }

    /**
     * function updates products_affected and categories_affected
     * for a given promocode_id
     *
     */
    function updatePromoCodeArea($data)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['promo_codes_coupons_table']['columns'];

        $query = new DB_Update('promo_codes_coupons_table');

        $query->addUpdateValue($tr['products_affected'],   $data["prods"]);
        $query->addUpdateValue($tr['categories_affected'], $data["cats"]);

        $query->WhereValue($tr['id'], DB_EQ, $data['pcid']);
        $application->db->getDB_Result($query);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $cat_array;

    var $affected_cats;
    var $affected_prods;

    var $is_debug;
    var $_debug_info;

    /**#@-*/
}
?>