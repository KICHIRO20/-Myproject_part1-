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
 * CReportDataSource class
 * @author Alexey Florinsky
 * @version $Id: report_data.php 5013 2008-04-09 14:04:24Z af $
 * @package Reports
 */
class CReportDataSource
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor
     *
     * @return CReportDataSource
     */
    function CReportDataSource()
    {
        $this->__params = array();
    }

    function setDatetimePeriod($from, $to)
    {
    }

    function setStatusFilter($ord_statuses, $pay_statuses, $show_deleted_orders=0)
    {
    	$this->__params['status_ids'] = $ord_statuses;
        $this->__params['payment_status_ids'] = $pay_statuses;
        $this->__params['show_deleted_orders'] = $show_deleted_orders;
    }

    /*
     * This function reads statistics collection settings and applies status filters to report data queriers
     */
    function applyReportFilteredStatuses()
    {
    	$param_data = modApiFunc("Settings", "getParamListByGroup","STATISTICS_COLLECTION");

    	$ord_status_ids = array();
    	$pay_status_ids = array();
    	$del_status = 0;

    	foreach ($param_data as $i => $p)
    	{
    		if ($p['param_current_value'] === "YES")
    		{
	    		switch ($p['param_name'])
	    		{
	    			case "ADV_CFG_DELETE_STATUS_TRACKING": $del_status = 1; break;
	    			case "TRACK_ORDER_STATUS_CANCELLED": $ord_status_ids[] = 5; break;
	    			case "TRACK_ORDER_STATUS_COMPLETED": $ord_status_ids[] = 7; break;
	    			case "TRACK_ORDER_STATUS_DECLINED": $ord_status_ids[] = 6; break;
	    			case "TRACK_ORDER_STATUS_INPROGRESS": $ord_status_ids[] = 2; break;
	    			case "TRACK_ORDER_STATUS_NEWORDER": $ord_status_ids[] = 1; break;
	    			case "TRACK_ORDER_STATUS_READYTOSHIP": $ord_status_ids[] = 3; break;
	    			case "TRACK_ORDER_STATUS_SHIPPED": $ord_status_ids[] = 4; break;
	    			case "TRACK_PAYMENT_ORDER_STATUS_DECLINED": $pay_status_ids[] = 3; break;
	    			case "TRACK_PAYMENT_ORDER_STATUS_FULLYPAID": $pay_status_ids[] = 2; break;
	    			case "TRACK_PAYMENT_ORDER_STATUS_WAITING": $pay_status_ids[] = 1; break;
                                case "TRACK_PAYMENT_ORDER_STATUS_SUSPICIOUS": $pay_status_ids[] = 4; break;
	    		}
    		}

    		/*if ($p['param_current_value'] === "YES" && preg_match("/TRACK_ORDER_STATUS_(.+)/", $p['param_name'], $match) != 0)
    		{
    			# current parameter is ORDER status tracking setting
    			$ord_status_ids[] = $this->getStatusIdbyName($match[1]);
    		}
    		else if ($p['param_current_value'] === "YES" && preg_match("/TRACK_PAYMENT_ORDER_STATUS_(.+)/", $p['param_name'], $match) != 0)
    		{
    			# current parameter is order PAYMENT status tracking setting
    			$pay_status_ids[] = $this->getStatusIdbyName($match[1],true);
    		}
    		else if ($p['param_name'] == "ADV_CFG_DELETE_STATUS_TRACKING" && $p['param_current_value'] === "YES" )
    		{
    			# current parameter is deleted orders tracking setting
    			$del_status = 1;
    		}*/
    	}
    	$this->setStatusFilter($ord_status_ids, $pay_status_ids, $del_status);

    }

    function setParams($key, $value)
    {
        $this->__params[$key] = $value;
    }

    function __computePercentField($rows_list, $key_name, $percent_key_name)
    {
        $max_percent = 0;
        $max_pixel = 0;
        reset($rows_list);
        foreach ($rows_list as $row)
        {
            $max_pixel = max($max_pixel, $row[$key_name]);
            $max_percent += $row[$key_name];
        }

        $data = array();
        reset($rows_list);
        foreach ($rows_list as $row)
        {
            if ($max_percent == 0)
            {
                $row[$percent_key_name] = 0;
            }
            else
            {
                $row[$percent_key_name] = round($row[$key_name]*100/$max_percent, 2);
            }

            if ($max_pixel == 0)
            {
                $row[$percent_key_name.'_pixel'] = 0;
            }
            else
            {
                $row[$percent_key_name.'_pixel'] = round($row[$key_name]*100/$max_pixel, 2);
            }
            $data[] = $row;
        }
        return $data;
    }

    /**
     *                                   .
     *
     */
    function run()
    {
    }

    /**
     *                                        .
     *
     * @return array One record
     */
    function fetchRecord()
    {
        if (empty($this->__data) == true)
        {
            return false;
        }
        else
        {
            return array_shift($this->__data);
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     *                                    $arrays_list,
     *                 $key_fields_list.
     *
     *        $arrays_list                 ,                      .
     *        $empty_items_list                                                 ,         $arrays_list.
     *
     *         :                                                      ,
     *                                   .
     *
     * $key_fields_list -             ,
     *                 ,                      .
     *
     *         :                                                             ,
     *        $key_fields_list                       ,             .
     *
     *                                                                                   ,
     *                                           .
     *
     *       :
     *
     * $select_1 = array(
     *          array(
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 15
     *          ),
     *          ...
     *          array(
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 20
     *          ),
     * );
     *
     * $select_2 = array(
     *          array(
     *                  order_total => 2000,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 15
     *          ),
     *          ...
     *          array(
     *                  order_total => 4000,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 20
     *          ),
     * );
     *
     *          ,                                                           :
     * datetime_year, datetime_month, datetime_day.
     *
     *            :
     * $key_fields = array(
     *          'datetime_year',
     *          'datetime_month',
     *          'datetime_day'
     * );
     *
     * $to_merge = array(
     *          $select_1,
     *          $select_2
     * );
     *
     * $empty_items = array(
     *          array('order_qty'=>0),
     *          array('order_total'=>0),
     * );
     *
     * $result = $this->____margeArrays($to_merge, $empty_items, $key_fields);
     *
     *                :
     * $data_list = array(
     *          array(
     *                  order_total_sum => 100,
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 15
     *          ),
     *          ...
     *          array(
     *                  order_total_sum => 100,
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 20
     *          ),
     * );
     */
    function __margeArrays($arrays_list, $empty_items_list, $key_fields_list)
    {
        // $arrays_list == $empty_items_list
        // not empty $key_...

        $result = array();

        $maps_list = array();
        $keys_list = array();
        foreach ($arrays_list as $list)
        {
            $map = $this->__makeMapByKeys($list, $key_fields_list);
            $maps_list[] = $map;
            $keys_list = array_merge($keys_list, array_keys($map));
        }
        $keys_list = array_unique($keys_list);
        sort($keys_list);

        foreach ($keys_list as $key)
        {
            $item = array();
            foreach ($maps_list as $map_key=>$map_item)
            {
                if (isset($map_item[$key]))
                {
                    $item = array_merge($item, $map_item[$key]);
                }
                else
                {
                    $item = array_merge($item, $empty_items_list[$map_key]);
                }
            }
            $result[] = $item;
        }
        return $result;
    }

    /**
     *                               $data_list                              $key_fields_list.
     *
     *        $data_list                  ,                                              .
     *        -      ,                                   $data_list                  ,
     *                                                                             .
     *       -                        $key_fields_list.
     *
     *         :                                                             ,
     *        $key_fields_list                       ,             .
     *
     *       :
     * $data_list = array(
     *          array(
     *                  order_total_sum => 100,
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 15
     *          ),
     *          ...
     *          array(
     *                  order_total_sum => 100,
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 20
     *          ),
     * );
     *
     *                                                         datetime_year, datetime_month, datetime_day.
     *       ,        $key_fields_list                 :
     *  array(datetime_year, datetime_month, datetime_day);
     *
     *                :
     * $data_list = array(
     *          '2008415' => array(
     *                  order_total_sum => 100,
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 15
     *          ),
     *          ...
     *          '2008420' => array(
     *                  order_total_sum => 100,
     *                  order_qty => 20,
     *                  datetime_year => 2008,
     *                  datetime_month => 4,
     *                  datetime_day => 20
     *          ),
     * );
     *
     *                                ,                                                .
     */
    function __makeMapByKeys($data_list, $key_fields_list)
    {
        $result = array();
        foreach ($data_list as $item)
        {
            $key = '';
            foreach ($key_fields_list as $field)
            {
                //                                                           ,
                //                                                 .
                $key .= isset($item[$field]) ? str_pad($item[$field], 4, '0', STR_PAD_LEFT) : '';
            }
            if (!empty($key))
            {
                $result[$key] = $item;
            }
            else
            {
                //           ,
                _fatal(get_class($this).'::'.__FUNCTION__.' ERROR! The key cannot be empty:<br>Data array:<br>'.print_r($data_list, true).'<br>Key fields:<br>'.print_r($key_fields_list, true));
            }
        }
        return $result;
    }

    /**
     *         :                                                      ,
     *                                   .
     */
    function __addZeroItems($data, $zero_item)
    {

        // MySQL format
        $from = $this->__params['from'];
        $to = $this->__params['to'];

        if ($from == null or $to == null)
        {
            $store_time = new CStoreDatetime();
            $to = $store_time->getTimestamp(); // shifted now()

            //                       ,
            $first_item = isset($data[0]) ? $data[0] : array('datetime_year'=>2007); //
            $first_year = $first_item['datetime_year'];
            $first_month = isset($first_item['datetime_month']) ? $first_item['datetime_month'] : 1;
            $first_day = isset($first_item['datetime_day']) ? $first_item['datetime_day'] : 1;
            $from = mktime(0,0,1,$first_month, $first_day, $first_year);
        }
        else
        {
            $from = strtotime($from);
            $to = strtotime($to);
        }

        $discontinuity = $this->__params['discontinuity'];
        $full_data_list = $this->__getCalendar($from, $to, $discontinuity);

        //          (                                                        )
        $__keys = array('datetime_year', 'datetime_month', 'datetime_day');

        //
        $to_merge = array(
            $full_data_list,
            $data,
        );

        //                                     (                              $to_merge)
        $empty_items = array(
            array(),
            $zero_item,
        );

        return $this->__margeArrays($to_merge, $empty_items, $__keys);
    }

    function __getCalendar($from, $to, $discontinuity)
    {
        $from_obj = new CDatetime($from);
        $to_obj = new CDatetime($to);
        $result = array();

        //
        $month_start_flag = false;
        //
        // $month_end_flag = ($from_obj->getYear() < $to_obj->getYear());
        $day_start_flag = false;
        //
        for($year=$from_obj->getYear(); $year<=$to_obj->getYear(); $year++)
        {
            if ($discontinuity == DATETIME_PERIOD_DISCONTINUITY_YEAR)
            {
                $result[] = array('datetime_year'=>$year);
            }
            else
            {
                //                 -         $from_obj->getMonth(),  . .
                //                             ,                1
                $month_start_num = $month_start_flag ? 1 : $from_obj->getMonth();
                //                                                 ,
                //              12
                $month_end_num = ($year < $to_obj->getYear()) ? 12 : $to_obj->getMonth();
                //
                for($month = $month_start_num; $month <= $month_end_num; $month++)
                {
                    if ($discontinuity == DATETIME_PERIOD_DISCONTINUITY_MONTH)
                    {
                        $result[] = array('datetime_year'=>$year, 'datetime_month'=>$month);
                    }
                    else
                    {
                        //                              :                           ,                       ,
                        //              1
                        $day_start_num = $day_start_flag ? 1 : $from_obj->getMonthDay();
                        //                                      :                                                 ,
                        //                                                  ,
                        $day_end_num = ($year <= $to_obj->getYear() and $month < $month_end_num) ? date("t", mktime(0, 0, 1, $month, 1, $year)) : $to_obj->getMonthDay();
                        for($day = $day_start_num; $day <= $day_end_num; $day++)
                        {
                            $result[] = array('datetime_year'=>$year, 'datetime_month'=>$month, 'datetime_day'=>$day);
                        }

                        $day_start_flag = true;
                    }
                }
                //                                          -
                //              1
                $month_start_flag = true;
            }
        }
        return $result;
    }

/*
     * Returns status id by given name. Please note that name should be given in upper case and without spaces
     * @param string Status name
     * @param bool if status name is order payment status name
     * @return int status id
     */
    function getStatusIdbyName($status_name, $is_payment=false)
    {
    	if (!$is_payment)
    	{
    	    $statuses = modApiFunc("Checkout","getOrderStatusList");
    	}
    	else
    	{
    	    $statuses = modApiFunc("Checkout","getOrderPaymentStatusList");
    	}

    	foreach ($statuses as $id => $s)
    	{
    		$s['name'] = _ml_strtoupper(preg_replace("/ /","",$s['name']));
    		if ($s['name'] == $status_name)
    		{
    			return $s['id'];
    		}
    	}

    	return -1;
    }

    var $__data = null;
    var $__params = null;
    /**#@-*/
}

?>