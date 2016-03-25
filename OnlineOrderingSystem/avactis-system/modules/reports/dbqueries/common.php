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

loadModuleFile('reports/reports_api.php');

/**
 *                                                   ,
 *
 *                           .
 */
/*abstract*/class DB_Select_By_Datetime_Period extends DB_Select
{
    function initQuery($discontinuity, $from, $to, $datetime_field)
    {
        if ($discontinuity == DATETIME_PERIOD_DISCONTINUITY_YEAR)
        {
            $this->addSelectField('YEAR('.$datetime_field.')','datetime_year');
            $this->SelectGroup('datetime_year');
            $this->SelectOrder('datetime_year', 'ASC');
        }
        else if ($discontinuity == DATETIME_PERIOD_DISCONTINUITY_MONTH)
        {
            $this->addSelectField('YEAR('.$datetime_field.')','datetime_year');
            $this->addSelectField('MONTH('.$datetime_field.')','datetime_month');

            $this->SelectGroup('datetime_year');
            $this->SelectGroup('datetime_month');

            $this->SelectOrder('datetime_year', 'ASC');
            $this->SelectOrder('datetime_month', 'ASC');
        }
        else
        {
            // columns to select
            $this->addSelectField('YEAR('.$datetime_field.')','datetime_year');
            $this->addSelectField('MONTH('.$datetime_field.')','datetime_month');
            $this->addSelectField('DAYOFMONTH('.$datetime_field.')','datetime_day');

            // grouping
            $this->SelectGroup('datetime_year');
            $this->SelectGroup('datetime_month');
            $this->SelectGroup('datetime_day');

            // sort order
            $this->SelectOrder('datetime_year', 'ASC');
            $this->SelectOrder('datetime_month', 'ASC');
            $this->SelectOrder('datetime_day', 'ASC');
        }

        if ($from != null and $to != null)
        {
            $this->WhereValue($datetime_field, DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($datetime_field, DB_LTE, $to);
        }
    }
}

class SELECT_MAX_ORDER_ID extends DB_Select
{
    function initQuery()
    {
        $tables = Reports::getTables();
        $c = $tables['reports_orders_products_stat']['columns'];

        $this->addSelectTable('reports_orders_products_stat');
        $this->addSelectField($this->fMax($c['order_id']), 'max_order_id');
    }
}

class SELECT_REPORT_PERIOD extends DB_Select
{
    function initQuery($params)
    {
        $report_class_name = $params['report_class_name'];

        $tables = Reports::getTables();
        $c = $tables['report_periods']['columns'];

        $this->addSelectField($c['report_period_label'], 'report_period_label');
        $this->WhereValue($c['report_class_name'], DB_EQ, $report_class_name);
    }
}

class REPLACE_REPORT_PERIOD extends DB_Replace
{
    function REPLACE_REPORT_PERIOD()
    {
        parent::DB_Replace('report_periods');
    }

    function initQuery($params)
    {
        $report_class_name = $params['report_class_name'];
        $report_period_label = $params['report_period_label'];

        $tables = Reports::getTables();
        $c = $tables['report_periods']['columns'];

        $this->addReplaceValue($report_class_name,   $c['report_class_name']);
        $this->addReplaceValue($report_period_label, $c['report_period_label']);
    }
}

class PRODUCT_STAT_SELECT_RECORD_BY_PK extends DB_Select
{
    function isCachable()
    {
        return false;
    }

    function initQuery($params)
    {
        $datetime = $params['datetime'];
        $product_id = $params['product_id'];

        $tables = Reports::getTables();
        $c = $tables['reports_product_stat']['columns'];

        $this->addSelectField($c['datetime'], 'datetime');
        $this->addSelectField($c['product_id'], 'product_id');
        $this->addSelectField($c['views'], 'views');
        $this->addSelectField($c['sale_items'], 'sale_items');
        $this->addSelectField($c['added_to_cart_times'], 'added_to_cart_times');
        $this->addSelectField($c['deleted_from_cart_times'], 'deleted_from_cart_times');
        $this->addSelectField($c['added_to_cart_qty'], 'added_to_cart_qty');
        $this->addSelectField($c['deleted_from_cart_qty'], 'deleted_from_cart_qty');

        $this->WhereValue($c['datetime'], DB_EQ, $datetime);
        $this->WhereAND();
        $this->WhereValue($c['product_id'], DB_EQ, $product_id);
    }
}

class PRODUCT_STAT_SELECT_MULTIPLE_RECORDS_BY_PK extends DB_Select
{
    function isCachable()
    {
        return false;
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_product_stat']['columns'];

        $this->addSelectField($c['datetime'], 'datetime');
        $this->addSelectField($c['product_id'], 'product_id');
        $this->addSelectField($c['views'], 'views');
        $this->addSelectField($c['sale_items'], 'sale_items');
        $this->addSelectField($c['added_to_cart_times'], 'added_to_cart_times');
        $this->addSelectField($c['deleted_from_cart_times'], 'deleted_from_cart_times');
        $this->addSelectField($c['added_to_cart_qty'], 'added_to_cart_qty');
        $this->addSelectField($c['deleted_from_cart_qty'], 'deleted_from_cart_qty');

        foreach ($params as $p)
        {
            $this->addWhereOpenSection();
            $this->WhereValue($c['datetime'], DB_EQ, $p['datetime']);
            $this->WhereAND();
            $this->WhereValue($c['product_id'], DB_EQ, $p['product_id']);
            $this->addWhereCloseSection();
            $this->WhereOR();
        }
        $this->Where('1', DB_EQ, '2'); // always false
    }
}

class PRODUCT_STAT_SELECT_TOP_10 extends DB_Select
{
    function initQuery($params)
    {
        $field_alias = $params['field_alias'];
        $aggregating_field = $params['aggregating_field'];
        $from = $params['from'];
        $to = $params['to'];

        $tables = Reports::getTables();
        $c_product_stat = $tables['reports_product_stat']['columns'];
        $c_product_info = $tables['reports_product_info']['columns'];

        $this->addSelectField($c_product_info['product_name'], 'product_name');
        $this->addSelectField($c_product_stat['product_id'], 'product_id');
        $this->addSelectField($this->fSum($c_product_stat[$aggregating_field]), $field_alias);

        $this->WhereField($c_product_stat['product_id'], DB_EQ, $c_product_info['product_id']);
        $this->WhereAND();
        $this->WhereValue($c_product_stat[$aggregating_field], DB_GT, 0);

        if ($from != null and $to != null)
        {
            $this->WhereAND();
            $this->WhereValue($c_product_stat['datetime'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c_product_stat['datetime'], DB_LTE, $to);
        }

        $this->SelectGroup($c_product_stat['product_id']);
        $this->SelectOrder($field_alias, 'DESC');
        $this->SelectLimit(0,10);

    }
}

/*
 * This query selects sold items depending on new order products data
 */
class SELECT_STAT_TOP_10_PRODUCTS_FILTERED extends DB_Select
{
    function initQuery($params)
    {
        $from = $params['from'];
        $to = $params['to'];
        $status_ids = implode(", ",$params['status_ids']);
        $payment_status_ids = implode(", ",$params['payment_status_ids']);
        $show_deleted_orders = $params['show_deleted_orders'];

        $field_alias = "items_sold";

        $tables = Reports::getTables();
        $c_order_stat   = $tables['reports_orders_stat']['columns'];
        $c_product_stat = $tables['reports_orders_products_stat']['columns'];
        $c_product_info = $tables['reports_product_info']['columns'];

        $this->addSelectField($c_product_info['product_name'], 'product_name');
        $this->addSelectField($c_product_stat['product_id'], 'product_id');
        $this->addSelectField($this->fSum($c_product_stat["amount"]), $field_alias);

        $this->WhereField($c_product_stat['product_id'], DB_EQ, $c_product_info['product_id']);
        $this->WhereAND();
        $this->WhereField($c_product_stat['order_id'], DB_EQ, $c_order_stat['order_id']);
        $this->WhereAND();
        $this->WhereValue($c_product_stat["amount"], DB_GT, 0);


        if ($from != null and $to != null)
        {
            $this->WhereAND();
            $this->WhereValue($c_order_stat['order_datetime'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c_order_stat['order_datetime'], DB_LTE, $to);
        }

        if ($show_deleted_orders == 0)
        {
            $this->WhereAND();
            $this->WhereValue($c_order_stat['order_deleted'], DB_EQ, $show_deleted_orders);
        }

        if ($status_ids != null)
        {
            $this->WhereAND();
            $this->WhereField($c_order_stat['order_status_id'], DB_IN, "(".$status_ids.")");
        }

        if ($payment_status_ids != null)
        {
            $this->WhereAND();
            $this->WhereField($c_order_stat['order_payment_status_id'], DB_IN, "(".$payment_status_ids.")");
        }

        $this->SelectGroup($c_product_stat['product_id']);
        $this->SelectOrder($field_alias, 'DESC');
        $this->SelectLimit(0,10);

    }
}

//////////////////////////

class PRODUCT_STAT_SELECT extends DB_Select
{
	function initQuery($params)
	{
		$field_alias = $params['field_alias'];
		$aggregating_field = $params['aggregating_field'];
		$from = $params['from'];
		$to = $params['to'];

		$tables = Reports::getTables();
		$c_product_stat = $tables['reports_product_stat']['columns'];
		$c_product_info = $tables['reports_product_info']['columns'];

		$this->addSelectField($c_product_info['product_name'], 'product_name');
		$this->addSelectField($c_product_stat['product_id'], 'product_id');
		$this->addSelectField($this->fSum($c_product_stat[$aggregating_field]), $field_alias);

		$this->WhereField($c_product_stat['product_id'], DB_EQ, $c_product_info['product_id']);
		$this->WhereAND();
		$this->WhereValue($c_product_stat[$aggregating_field], DB_GT, 0);

		if ($from != null and $to != null)
		{
			$this->WhereAND();
			$this->WhereValue($c_product_stat['datetime'], DB_GTE, $from);
			$this->WhereAND();
			$this->WhereValue($c_product_stat['datetime'], DB_LTE, $to);
		}

		$this->SelectGroup($c_product_stat['product_id']);
		$this->SelectOrder($field_alias, 'DESC');
//		$this->SelectLimit(0,10);

	}
}

/*
 * This query selects sold items depending on new order products data
*/
class SELECT_STAT_PRODUCTS_FILTERED extends DB_Select
{
	function initQuery($params)
	{
		$from = $params['from'];
		$to = $params['to'];
		$status_ids = implode(", ",$params['status_ids']);
		$payment_status_ids = implode(", ",$params['payment_status_ids']);
		$show_deleted_orders = $params['show_deleted_orders'];

		$field_alias = "items_sold";

		$tables = Reports::getTables();
		$c_order_stat   = $tables['reports_orders_stat']['columns'];
		$c_product_stat = $tables['reports_orders_products_stat']['columns'];
		$c_product_info = $tables['reports_product_info']['columns'];

		$this->addSelectField($c_product_info['product_name'], 'product_name');
		$this->addSelectField($c_product_stat['product_id'], 'product_id');
		$this->addSelectField($this->fSum($c_product_stat["amount"]), $field_alias);

		$this->WhereField($c_product_stat['product_id'], DB_EQ, $c_product_info['product_id']);
		$this->WhereAND();
		$this->WhereField($c_product_stat['order_id'], DB_EQ, $c_order_stat['order_id']);
		$this->WhereAND();
		$this->WhereValue($c_product_stat["amount"], DB_GT, 0);


		if ($from != null and $to != null)
		{
			$this->WhereAND();
			$this->WhereValue($c_order_stat['order_datetime'], DB_GTE, $from);
			$this->WhereAND();
			$this->WhereValue($c_order_stat['order_datetime'], DB_LTE, $to);
		}

		if ($show_deleted_orders == 0)
		{
			$this->WhereAND();
			$this->WhereValue($c_order_stat['order_deleted'], DB_EQ, $show_deleted_orders);
		}

		if ($status_ids != null)
		{
			$this->WhereAND();
			$this->WhereField($c_order_stat['order_status_id'], DB_IN, "(".$status_ids.")");
		}

		if ($payment_status_ids != null)
		{
			$this->WhereAND();
			$this->WhereField($c_order_stat['order_payment_status_id'], DB_IN, "(".$payment_status_ids.")");
		}

		$this->SelectGroup($c_product_stat['product_id']);
		$this->SelectOrder($field_alias, 'DESC');
		//$this->SelectLimit(0,10);

	}
}

///////////////////////////





class PRODUCT_STAT_SELECT_STATISTICS_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];

        $tables = Reports::getTables();
        $c_product_stat = $tables['reports_product_stat']['columns'];

        $this->addSelectField($this->fSum($c_product_stat['views']),                     'product_views');
        //$this->addSelectField($this->fSum($c_product_stat['sale_items']),                'items_sold'); # this line has been commented as new algorithm of sold item numbers calculation has been implemented
        $this->addSelectField($this->fSum($c_product_stat['added_to_cart_times']),       'product_added_to_cart_times');
        $this->addSelectField($this->fSum($c_product_stat['deleted_from_cart_times']),   'product_deleted_from_cart_times');
        $this->addSelectField($this->fSum($c_product_stat['added_to_cart_qty']),         'product_added_to_cart_qty');
        $this->addSelectField($this->fSum($c_product_stat['deleted_from_cart_qty']),     'product_deleted_from_cart_qty');

        parent::initQuery($discontinuity, $from, $to, $c_product_stat['datetime']);
    }
}

class SELECT_SOLD_ITEMS_STATISTICS_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];
        $field_alias = "items_sold";
        $status_ids = implode(", ",$params['status_ids']);
        $payment_status_ids = implode(", ",$params['payment_status_ids']);
        $show_deleted_orders = $params['show_deleted_orders'];

        $tables = Reports::getTables();
        $c_order_stat   = $tables['reports_orders_stat']['columns'];
        $c_product_stat = $tables['reports_orders_products_stat']['columns'];

        $this->addSelectField($this->fSum($c_product_stat["amount"]), $field_alias);

        $this->WhereField($c_product_stat['order_id'], DB_EQ, $c_order_stat['order_id']);
        $this->WhereAND();
        $this->WhereValue($c_product_stat["amount"], DB_GT, 0);


        if ($from != null and $to != null)
        {
            $this->WhereAND();
            $this->WhereValue($c_order_stat['order_datetime'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c_order_stat['order_datetime'], DB_LTE, $to);
        }

        if ($show_deleted_orders == 0)
        {
            $this->WhereAND();
            $this->WhereValue($c_order_stat['order_deleted'], DB_EQ, $show_deleted_orders);
        }

        if ($status_ids != null)
        {
            $this->WhereAND();
            $this->WhereField($c_order_stat['order_status_id'], DB_IN, "(".$status_ids.")");
        }

        if ($payment_status_ids != null)
        {
            $this->WhereAND();
            $this->WhereField($c_order_stat['order_payment_status_id'], DB_IN, "(".$payment_status_ids.")");
        }

        parent::initQuery($discontinuity, null, null, $c_order_stat['order_datetime']);
    }
}

class PRODUCT_STAT_REPLACE_RECORD_BY_PK extends DB_Replace
{
    function PRODUCT_STAT_REPLACE_RECORD_BY_PK()
    {
        parent::DB_Replace('reports_product_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_product_stat']['columns'];

        $this->addReplaceValue($params['datetime'],                 $c['datetime']);
        $this->addReplaceValue($params['product_id'],               $c['product_id']);
        $this->addReplaceValue($params['views'],                    $c['views']);
        $this->addReplaceValue($params['sale_items'],               $c['sale_items']);
        $this->addReplaceValue($params['added_to_cart_times'],      $c['added_to_cart_times']);
        $this->addReplaceValue($params['deleted_from_cart_times'],  $c['deleted_from_cart_times']);
        $this->addReplaceValue($params['added_to_cart_qty'],        $c['added_to_cart_qty']);
        $this->addReplaceValue($params['deleted_from_cart_qty'],    $c['deleted_from_cart_qty']);
    }
}

loadCoreFile('db_multiple_replace.php');
class PRODUCT_STAT_REPLACE_MULTIPLE_RECORDS_BY_PK extends DB_Multiple_Replace
{
    function PRODUCT_STAT_REPLACE_MULTIPLE_RECORDS_BY_PK()
    {
        parent::DB_Multiple_Replace('reports_product_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_product_stat']['columns'];

        $this->setReplaceFields(array(
                                        'datetime',
                                        'product_id',
                                        'views',
                                        'sale_items',
                                        'added_to_cart_times',
                                        'deleted_from_cart_times',
                                        'added_to_cart_qty',
                                        'deleted_from_cart_qty',
                                    )
                               );
        foreach ($params as $row)
        {
            $this->addReplaceValuesArray($row);
        }
    }
}

class PRODUCT_INFO_SELECT_RECORD_BY_PK extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_product_info']['columns'];

        $this->addSelectField($c['product_name'], 'product_name');
        $this->addSelectField($c['product_id'], 'product_id');

        foreach ($params as $p)
        {
            $this->WhereValue($c['product_id'], DB_EQ, $p);
            $this->WhereOR();
        }
        $this->Where('1', DB_EQ, '2');
    }
}

class PRODUCT_INFO_REPLACE_RECORD_BY_PK extends DB_Replace
{
    function PRODUCT_INFO_REPLACE_RECORD_BY_PK()
    {
        parent::DB_Replace('reports_product_info');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_product_info']['columns'];

        $this->addReplaceValue($params['product_name'], $c['product_name']);
        $this->addReplaceValue($params['product_id'],   $c['product_id']);
    }
}

class PRODUCT_INFO_REPLACE_MULTIPLE_RECORDS extends DB_Multiple_Replace
{
    function PRODUCT_INFO_REPLACE_MULTIPLE_RECORDS()
    {
        parent::DB_Multiple_Replace('reports_product_info');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_product_info']['columns'];

        $this->setReplaceFields(array('product_id', 'product_name'));
        foreach($params as $rec)
        {
            $this->addReplaceValuesArray($rec);
        }
    }
}


class INSERT_VISITOR_INFO extends DB_Insert
{
    function INSERT_VISITOR_INFO()
    {
        parent::DB_Insert('reports_visitor_info');
    }

    function initQuery($params)
    {
        $visitor_session_id     = $params['visitor_session_id'];
        $visitor_os             = $params['visitor_os'];
        $visitor_browser        = $params['visitor_browser'];
        $visitor_register_time  = $params['visitor_register_time'];
        $visitor_agent          = $params['visitor_agent'];
        $visitor_type           = $params['visitor_type'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_info']['columns'];

        $this->addInsertValue($visitor_session_id,  $c['visitor_session_id']);
        $this->addInsertValue($visitor_os,          $c['visitor_os']);
        $this->addInsertValue($visitor_browser,     $c['visitor_browser']);
        $this->addInsertValue($visitor_register_time,$c['visitor_register_time']);
        $this->addInsertValue($visitor_agent,       $c['visitor_agent']);
        $this->addInsertValue($visitor_type,        $c['visitor_type']);
    }
}

class SELECT_WEB_ROBOT_ID extends DB_Select
{
    function initQuery($params)
    {
        $useragent = $params['user-agent'];

        $tables = Reports::getTables();
        $c = $tables['reports_crawlers_info']['columns'];

        $this->addSelectTable('reports_crawlers_info');

        $this->addSelectField($c['agent_string']);
        $this->addSelectField($c['name']);
        $this->addSelectField($c['type']);

        $this->Where($c['agent_string'], DB_LIKE, "'%".$useragent."%'");
    }
}

class SELECT_VISITOR_ID_BY_SESSION_ID extends DB_Select
{
    function isCachable()
    {
        return false;
    }

    function initQuery($params)
    {
        $visitor_session_id = $params['visitor_session_id'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_info']['columns'];

        $this->addSelectField($c['visitor_id'], 'visitor_id');

        $this->WhereValue($c['visitor_session_id'], DB_EQ, $visitor_session_id);
    }
}


class INSERT_VISITOR_SEANCE extends DB_Insert
{
    function INSERT_VISITOR_SEANCE()
    {
        parent::DB_Insert('reports_visitor_seances');
    }

    function initQuery($params)
    {
        $visitor_id     = $params['visitor_id'];
        $seance_id      = $params['seance_id'];
        $prev_seance_id = $params['prev_seance_id'];
        $visit_number   = $params['visit_number'];
        $creation_time  = $params['creation_time'];
        $referer        = $params['referer'];
        $referer_host   = $params['referer_host'];
        $remote_ip      = $params['remote_ip'];
        $remote_host    = $params['remote_host'];
        $entry_page     = $params['entry_page'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seances']['columns'];

        $this->addInsertValue($visitor_id,  $c['visitor_id']);
        $this->addInsertValue($seance_id,   $c['seance_id']);
        $this->addInsertValue($prev_seance_id,   $c['prev_seance_id']);
        $this->addInsertValue($visit_number,     $c['visit_number']);
        $this->addInsertValue($creation_time,$c['creation_time']);
        $this->addInsertValue($entry_page,  $c['entry_page']);
        $this->addInsertValue($referer,     $c['referer']);
        $this->addInsertValue($referer_host,$c['referer_host']);
        $this->addInsertValue($remote_ip,   $c['remote_ip']);
        $this->addInsertValue($remote_host, $c['remote_host']);
    }
}

class SELECT_CURRENT_SEANCE_LAST_PAGE_BY_VISITOR_ID extends DB_Select
{
    function isCachable()
    {
        return false;
    }

    function initQuery($params)
    {
        $visitor_id = $params['visitor_id'];
        $time_period = $params['time_period'];

        $tables = Reports :: getTables();
        $c_seances = $tables['reports_visitor_seances']['columns'];
        $c_seance_info = $tables['reports_visitor_seance_info']['columns'];
        $c_page_info = $tables['reports_page_urls']['columns'];

        $time_shift_hours = Configuration::getValue(SYSCONFIG_STORE_TIME_SHIFT);

        $this -> addSelectField($c_seance_info['seance_id'], 'seance_id');
        $this -> addSelectField($c_page_info['page_url'], 'page_url');

        $this -> addSelectTable('reports_visitor_seances');

        $this -> addInnerJoin('reports_page_urls',
                              $c_seance_info['page_url'],
                              DB_EQ,
                              $c_page_info['id']);

        $this -> WhereField($c_seances['seance_id'], DB_EQ,
                            $c_seance_info['seance_id']);
        $this -> WhereAND();
        $this -> Where($c_seance_info['visit_time'], DB_GTE,
                       'DATE_ADD(DATE_ADD(NOW(), INTERVAL '.$time_shift_hours.' HOUR), INTERVAL -'.$time_period.' MINUTE)');
        $this -> WhereAND();
        $this -> WhereValue($c_seances['visitor_id'], DB_EQ, $visitor_id);

        $this -> SelectOrder($c_seance_info['visit_time'], 'DESC');

        $this -> SelectLimit(0, 1);
    }
}

class SELECT_LAST_SEANCE_ID_BY_VISITOR_ID extends DB_Select
{
    function isCachable()
    {
        return false;
    }

    function initQuery($params)
    {
        $visitor_id = $params['visitor_id'];

        $tables = Reports :: getTables();
        $c_seances = $tables['reports_visitor_seances']['columns'];

        $this -> addSelectField($c_seances['seance_id'], 'seance_id');

        $this -> WhereValue($c_seances['visitor_id'], DB_EQ, $visitor_id);

        $this -> SelectOrder($c_seances['creation_time'], 'DESC');

        $this -> SelectLimit(0, 1);
    }
}

class SELECT_SEANCE_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $seance_id = $params['seance_id'];

        $tables = Reports::getTables();
        $c_seances = $tables['reports_visitor_seances']['columns'];

        $this->addSelectField($c_seances['seance_id'], 'seance_id');
        $this->addSelectField($c_seances['prev_seance_id'], 'prev_seance_id');
        $this->addSelectField($c_seances['visit_number'], 'visit_number');
        $this->addSelectField($c_seances['visitor_id'], 'visitor_id');
        $this->addSelectField($c_seances['referer'], 'referer');
        $this->addSelectField($c_seances['remote_ip'], 'remote_ip');
        $this->addSelectField($c_seances['remote_host'], 'remote_host');

        $this->WhereValue($c_seances['seance_id'], DB_EQ, $seance_id);
    }

    function isCachable()
    {
        return false;
    }
}

class INSERT_VISITOR_SEANCE_INFO extends DB_Insert
{
    function INSERT_VISITOR_SEANCE_INFO()
    {
        parent::DB_Insert('reports_visitor_seance_info');
    }

    function initQuery($params)
    {
        $visit_time      = $params['visit_time'];
        $seance_id       = $params['seance_id'];
        $page_url        = $params['page_url'];
        $visitors_online = $params['visitors_online'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seance_info']['columns'];

        $this->addInsertValue($visit_time,         $c['visit_time']);
        $this->addInsertValue($seance_id,          $c['seance_id']);
        $this->addInsertValue($page_url,           $c['page_url']);
        $this->addInsertValue($visitors_online,    $c['visitors_online']);
    }
}

class SELECT_BROWSERS_STAT extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_visitor_info']['columns'];

        $this->addSelectField($c['visitor_browser'], 'browser_name');
        $this->addSelectField('COUNT(1)', 'number');

        $this->WhereValue($c['visitor_type'], DB_EQ, "C");

        $this->SelectGroup($c['visitor_browser']);
        $this->SelectOrder('number', 'DESC');
    }
}

class SELECT_OS_STAT extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_visitor_info']['columns'];

        $this->addSelectField($c['visitor_os'], 'os_name');
        $this->addSelectField('COUNT(1)', 'number');

        $this->WhereValue($c['visitor_type'], DB_EQ, "C");

        $this->SelectGroup($c['visitor_os']);
        $this->SelectOrder('number', 'DESC');
    }
}

class SELECT_UNIQUE_VISITORS_TOTAL_NUMBER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_visitor_info']['columns'];

        $from = $params['from'];
        $to = $params['to'];

        $this->addSelectField('COUNT(*)', 'number');
        $this->addSelectTable('reports_visitor_info');

        if ($from !== null and $to !== null)
        {
            $this->WhereValue($c['visitor_register_time'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c['visitor_register_time'], DB_LTE, $to);
        }
    }
}

class SELECT_VISITOR_SEANCES_TOTAL_NUMBER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seances']['columns'];

        $from = $params['from'];
        $to = $params['to'];

        $this->addSelectField($this->fCount($c['seance_id']), 'number');
        $this->addSelectTable('reports_visitor_seances');

        if ($from !== null and $to !== null)
        {
            $this->WhereValue($c['creation_time'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c['creation_time'], DB_LTE, $to);
        }
    }
}

class SELECT_PAGE_VIEWS_TOTAL_NUMBER extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seance_info']['columns'];

        $from = $params['from'];
        $to = $params['to'];

        $this->addSelectField($this->fCount($c['page_url']), 'number');
        $this->addSelectTable('reports_visitor_seance_info');

        if ($from !== null and $to !== null)
        {
            $this->WhereValue($c['visit_time'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c['visit_time'], DB_LTE, $to);
        }
    }
}

class SELECT_REFERER_HOSTS_BY_TIME_PERIOD extends DB_Select
{
    function initQuery($params)
    {
        $from = $params['from'];
        $to = $params['to'];
        $limits = $params['limits'];
        $select_full_referer_url = $params['select_full_referer_url'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seances']['columns'];
        $v = $tables['reports_visitor_info']['columns'];

        if ($select_full_referer_url == true)
        {
            $this->addSelectField($c['referer'], 'referer');
        }
        else
        {
            $this->addSelectField($c['referer_host'], 'referer');
        }
        $this->addSelectField('COUNT(1)', 'visit_number');

        if ($from !== null and $to !== null)
        {
            $this->WhereValue($c['creation_time'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c['creation_time'], DB_LTE, $to);
            $this->WhereAND();
        }

        #
        # show human visitors only
        #
        $this->WhereField($c['visitor_id'], DB_EQ, $v['visitor_id']);
        $this->WhereAND();
        $this->WhereValue($v['visitor_type'], DB_EQ, "C");

        if ($select_full_referer_url == true)
        {
            $this->SelectGroup($c['referer']);
        }
        else
        {
            $this->SelectGroup($c['referer_host']);
        }
        $this->SelectOrder('visit_number', 'DESC');

        if ($limits !== null and !empty($limits) and isset($limits[0]) and isset($limits[1]))
        {
            list($offset, $number) = $limits;
            $this->SelectLimit($offset, $number);
        }
    }
}

class SELECT_TOP_VIEWED_PAGE_BY_TIME_PERIOD extends DB_Select
{
    function initQuery($params)
    {
        $from = $params['from'];
        $to = $params['to'];
        $limits = $params['limits'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seance_info']['columns'];

        $this->addSelectField($c['page_url'], 'page_url');
        $this->addSelectField('COUNT(1)', 'view_number');

        if ($from !== null and $to !== null)
        {
            $this->WhereValue($c['visit_time'], DB_GTE, $from);
            $this->WhereAND();
            $this->WhereValue($c['visit_time'], DB_LTE, $to);
        }

        $this->SelectGroup($c['page_url']);
        $this->SelectOrder('view_number', 'DESC');

        if ($limits !== null and !empty($limits) and isset($limits[0]) and isset($limits[1]))
        {
            list($offset, $number) = $limits;
            $this->SelectLimit($offset, $number);
        }
    }
}

class SELECT_SEANCE_STATISTICS_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seance_info']['columns'];

        $this->addSelectField('COUNT(DISTINCT('.$c['seance_id'].'))', 'seance_number');
        $this->addSelectField('COUNT('.$c['page_url'].')', 'page_number');
        $this->addSelectField('ROUND(count('.$c['page_url'].')/count(distinct('.$c['seance_id'].')), 2)', 'page_views_per_seance');

        parent::initQuery($discontinuity, $from, $to, $c['visit_time']);
    }
}

class SELECT_VISITOR_NUMBER_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seances']['columns'];
        $v = $tables['reports_visitor_info']['columns'];

        $this->addSelectField('COUNT(DISTINCT('.$c['visitor_id'].'))', 'visitor_number');

        $this->WhereField($v['visitor_id'], DB_EQ, $c['visitor_id']);
        $this->WhereAND();
        $this->WhereValue($v['visitor_type'], DB_EQ, "C");

        if ($from != null && $to != null)
        {
            $this->WhereAND();
        }

        parent::initQuery($discontinuity, $from, $to, $c['creation_time']);
    }
}


class SELECT_FIRST_TIME_VISITOR_NUMBER_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];

        $tables = Reports::getTables();
        $c = $tables['reports_visitor_info']['columns'];

        $this->addSelectField('COUNT('.$c['visitor_id'].')', 'first_time_visitor_number');

        $this->WhereValue($c['visitor_type'], DB_EQ, "C");

        if ($from != null && $to != null)
        {
            $this->WhereAND();
        }

        parent::initQuery($discontinuity, $from, $to, $c['visitor_register_time']);
    }
}


class SELECT_SEANCE_INFO_RECORDS extends DB_Select
{
    function initQuery($params)
    {
        $limit = $params['limits'];
        $visitor_type = $params['visitor_type'];

        $tables = Reports::getTables();
        $c_seance_info = $tables['reports_visitor_info']['columns'];
        $c_seances = $tables['reports_visitor_seances']['columns'];

        $this->addSelectField($c_seance_info['visitor_os'],         'visitor_os');
        $this->addSelectField($c_seance_info['visitor_browser'],    'visitor_browser');
        $this->addSelectField($c_seances['visitor_id'],             'visitor_id');
        $this->addSelectField($c_seances['seance_id'],              'seance_id');
        $this->addSelectField($c_seances['prev_seance_id'],         'prev_seance_id');
        $this->addSelectField($c_seances['visit_number'],           'visit_number');
        $this->addSelectField($c_seances['remote_ip'],              'remote_ip');
        $this->addSelectField($c_seances['remote_host'],            'remote_host');
        $this->addSelectField($c_seances['referer'],                'referer');
        $this->addSelectField($c_seances['referer_host'],           'referer_host');
        $this->addSelectField($c_seances['creation_time'],          'creation_time');
        $this->addSelectField($c_seances['entry_page'],             'entry_page');

        $this->WhereField($c_seance_info['visitor_id'], DB_EQ, $c_seances['visitor_id']);

        $this->SelectOrder($c_seances['creation_time'], 'DESC');

        if ($limit !== null and !empty($limit) and isset($limit[0]) and isset($limit[1]))
        {
            list($offset, $number) = $limit;
            $this->SelectLimit($offset, $number);
        }
    }
}

class SELECT_CLICK_PATH_BY_SEANCE_IDs extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_visitor_seance_info']['columns'];
        $p = $tables['reports_page_urls']['columns'];

        $this->addSelectField($c['seance_id'],      'seance_id');
        $this->addSelectField($p['page_url'],       'page_url');
        $this->addSelectField($c['visit_time'],     'visit_time');
        $this->addInnerJoin('reports_page_urls',
                            $p['id'], DB_EQ, $c['page_url']);

        $this->Where($c['seance_id'], DB_IN, "(".implode(",",$params['seance_ids']).")");
        $this->SelectOrder($c['visit_time'],'ASC');
    }
}

class INSERT_ORDERS_STAT_RECORD extends DB_Insert
{
    function INSERT_ORDERS_STAT_RECORD()
    {
        parent::DB_Insert('reports_orders_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_orders_stat']['columns'];

        $this->addInsertValue($params['order_id'],                $c['order_id']);
        $this->addInsertValue($params['order_status_id'],         $c['order_status_id']);
        $this->addInsertValue($params['order_payment_status_id'], $c['order_payment_status_id']);
        $this->addInsertValue($params['order_deleted'],           $c['order_deleted']);
        $this->addInsertValue($params['order_datetime'],          $c['order_datetime']);
        $this->addInsertValue($params['order_total'],             $c['order_total']);
        $this->addInsertValue($params['order_tax_total'],         $c['order_tax_total']);
        $this->addInsertValue($params['order_currency'],          $c['order_currency']);
    }
}

/*
 * Update order statistics record with new statuses
 */
class UPDATE_ORDERS_STAT_RECORD extends DB_Update
{
    function UPDATE_ORDERS_STAT_RECORD()
    {
        parent::DB_Update('reports_orders_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_orders_stat']['columns'];
        $order_id = $params['order_id'];

        if (isset($params['order_total']) && $params['order_total'] != null)
        {
            $this->addUpdateValue($c['order_total'], $params['order_total']);
        }

        if (isset($params['order_tax']) && $params['order_tax'] != null)
        {
            $this->addUpdateValue($c['order_tax_total'], $params['order_tax']);
        }

        if (isset($params['order_status_id']) && isset($params['order_payment_status_id']))
        {
            $this->addUpdateValue($c['order_status_id'],         $params['order_status_id']);
            $this->addUpdateValue($c['order_payment_status_id'], $params['order_payment_status_id']);
        }
        if (!isset($params['order_deleted']))
        {
            $params['order_deleted'] = 0;
        }

        $this->addUpdateValue($c['order_deleted'], $params['order_deleted']);
        $this->WhereValue($c['order_id'], DB_EQ, $order_id);

    }
}

class INSERT_ORDERS_PRODUCTS_STAT_RECORD extends DB_Insert
{
    function INSERT_ORDERS_PRODUCTS_STAT_RECORD()
    {
        parent::DB_Insert('reports_orders_products_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_orders_products_stat']['columns'];

        $this->addInsertValue($params['order_id'],      $c['order_id']);
        $this->addInsertValue($params['product_id'],    $c['product_id']);
        $this->addInsertValue($params['amount'],        $c['amount']);
    }
}

class UPDATE_ORDERS_PRODUCTS_STAT_RECORD extends DB_Update
{
    function UPDATE_ORDERS_PRODUCTS_STAT_RECORD()
    {
        parent::DB_Update('reports_orders_products_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_orders_products_stat']['columns'];

        $this->addUpdateValue($c['order_id'],      $params['order_id']);
        $this->addUpdateValue($c['product_id'],    $params['product_id']);
        $this->addUpdateValue($c['amount'],        $params['amount']);
        $this->Where($c['order_id'], DB_EQ, $params['order_id']);
        $this->WhereAND();
        $this->Where($c['product_id'], DB_EQ, $params['product_id']);
    }
}

class SELECT_CARTS_STAT_RECORD_BY_PK extends DB_Select
{
    function initQuery($params)
    {
        $datetime = $params['datetime'];

        $tables = Reports::getTables();
        $c = $tables['reports_carts_stat']['columns'];

        $this->addSelectField($c['datetime'],           'datetime');
        $this->addSelectField($c['carts_created_qty'],  'carts_created_qty');

        $this->WhereValue($c['datetime'], DB_EQ, $datetime);
    }
}

class REPLACE_CARTS_STAT_RECORD extends DB_Replace
{
    function REPLACE_CARTS_STAT_RECORD()
    {
        parent::DB_Replace('reports_carts_stat');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $c = $tables['reports_carts_stat']['columns'];

        $this->addReplaceValue($params['datetime'],         $c['datetime']);
        $this->addReplaceValue($params['carts_created_qty'],   $c['carts_created_qty']);
    }
}

class SELECT_ORDERS_STAT_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];
        $status_ids = implode(", ",$params['status_ids']);
        $payment_status_ids = implode(", ",$params['payment_status_ids']);
        $show_deleted_orders = $params['show_deleted_orders'];

        $tables = Reports::getTables();
        $c = $tables['reports_orders_stat']['columns'];

        $this->addSelectField($c['order_currency'], 'order_currency');
        $this->addSelectField('SUM('.$c['order_total'].')', 'order_total_sum');
        $this->addSelectField('SUM('.$c['order_tax_total'].')', 'order_tax_sum');
        $this->addSelectField('COUNT('.$c['order_total'].')', 'order_qty');

        parent::initQuery($discontinuity, $from, $to, $c['order_datetime']);

        #
        # Status filters
        #

        $date_filter=false;

        if ($from == null && $to == null)
        {
            $date_filter=true; // if these is no date filtering
        }

        if ($status_ids != null)
        {
            if ($date_filter == false)
            {
                $this->WhereAND();
                $date_filter = true;
            }
            $this->WhereField("order_status_id", DB_IN, "(".$status_ids.")");
        }
        if ($payment_status_ids != null)
        {
            if (($status_ids == null && $date_filter == false) || ($date_filter == true))
            {
                $this->WhereAND();
                $date_filter = true;
            }
            $this->WhereField("order_payment_status_id", DB_IN, "(".$payment_status_ids.")");
        }
        if ($status_ids == null && $payment_status_ids == null)
        {
            if ($date_filter == false)
            {
                $this->WhereAND();
                $date_filter = true;
            }
            $this->WhereValue("order_payment_status_id", DB_EQ, "-1");
        }
        if ($show_deleted_orders == 0)
        {
            if ($date_filter == true)
            {
                $this->WhereAND();
            }
            $this->WhereValue("order_deleted", DB_EQ, $show_deleted_orders);
        }

        $this->SelectGroup('order_currency');
    }
}

class SELECT_CARTS_STAT_BY_DATETIME_PERIOD extends DB_Select_By_Datetime_Period
{
    function initQuery($params)
    {
        $discontinuity = $params['discontinuity'];
        $from = $params['from'];
        $to = $params['to'];

        $tables = Reports::getTables();
        $c = $tables['reports_carts_stat']['columns'];

        $this->addSelectField('SUM('.$c['carts_created_qty'].')',  'carts_created_qty');
        parent::initQuery($discontinuity, $from, $to, $c['datetime']);
    }
}

/*
 * Returns the number of visitors who are browing the store now.
 */
class SELECT_VISITORS_ONLINE extends DB_Select
{
    function isCachable()
    {
        return false;
    }

    function initQuery($params)
    {
        $visit_deadline = $params['visit_deadline'];
        $from  = $params['from'];
        $to  = $params['to'];

        $tables = Reports::getTables();
        $v = $tables['reports_visitor_seance_info']['columns'];

        $this -> addSelectTable('reports_visitor_seance_info');

        $this -> addSelectField('DISTINCT('.$v['seance_id'].')', 'seance_id');
        $this -> addSelectField('COUNT(*)', 'pageviews');

        $where = array();
        if ($visit_deadline != null)
            $where[] = array($v['visit_time'], DB_GTE, $visit_deadline);

        if ($from != null)
            $where[] = array($v["visit_time"], DB_GTE, $from);

        if ($to != null)
            $where[] = array($v['visit_time'], DB_LTE, $to);

        if (!empty($where))
            foreach($where as $k => $v)
            {
                if ($k > 0)
                    $this -> WhereAND();
                $this -> WhereValue($v[0], $v[1], $v[2]);
            }

        $this -> SelectGroup('seance_id');
    }
}

class SELECT_VISITORS_STATISTICS_BY_PERIOD extends DB_Select
{
    function initQuery($params)
    {
        $from  = $params['from'];
        $to  = $params['to'];

        $tables = Reports::getTables();
        $v = $tables['reports_visitor_info']['columns'];
        $s = $tables['reports_visitor_seance_info']['columns'];
        $c = $tables['reports_visitor_seances']['columns'];

        $this->addSelectField("DISTINCT(".$v['visitor_id'].")", 'visitors');
        $this->addSelectField('COUNT(*)', 'number');

        $this->WhereField($s['seance_id'], DB_EQ, $c['seance_id']);
        $this->WhereAND();
        $this->WhereField($c['visitor_id'], DB_EQ, $v['visitor_id']);
        $this->WhereAND();
        $this->WhereValue($v['visitor_type'], DB_EQ, "C");

        if ($from != null)
        {
            $this->WhereAND();
            $this->WhereValue($s["visit_time"], DB_GTE, $from);
        }

        if ($to != null)
        {
            $this->WhereAND();
            $this->WhereValue($s["visit_time"], DB_LTE, $to);
        }
        $this->SelectGroup($v["visitor_id"]);
    }
}

class SELECT_EARLIEST_VISIT_TIME extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports::getTables();
        $v = $tables['reports_visitor_seance_info']['columns'];

        $this->addSelectTable("reports_visitor_seance_info");

        $this->addSelectField('MIN(visit_time)', 'first_visit');
    }
}

class SELECT_ONLINE_VISITORS_BY_PERIOD extends DB_Select
{
    function initQuery($params)
    {
        $from  = $params['from'];
        $to  = $params['to'];

        $tables = Reports::getTables();
        $v = $tables['reports_visitor_seance_info']['columns'];

        $this->addSelectField("MAX(".$v['visitors_online'].")", 'visitors');

        if ($from != null)
        {
            $this->WhereValue($v["visit_time"], DB_GTE, $from);
        }

        if ($to != null)
        {
            $this->WhereAND();
            $this->WhereValue($v["visit_time"], DB_LTE, $to);
        }
    }
}

class SELECT_MAX_ONLINE_VISITORS_EVER extends DB_Select
{

    function initQuery ($params)
    {
        $tables = Reports::getTables();
        $v = $tables['reports_visitor_seance_info']['columns'];

        $this->addSelectField("MAX(".$v['visitors_online'].")", 'visitors');
    }
}

class RESET_REPORT_TABLE_DATA extends DB_Delete
{
    function RESET_REPORT_TABLE_DATA ()
    {
        parent::DB_Delete('table_name'); /*@ it is not a very good idea*/
    }

    function initQuery($params)
    {
        $table = $params['table']; // table name including prefix
        $this->DeleteTable = $table;
    }
}

class SELECT_CRAWLER_VISITS extends DB_Select
{
    function initQuery($params)
    {
        $from = $params['from'];
        $to = $params['to'];
        $crawler = $params['name'];
        $limit = $params['limits'];
        $visitor_id = $params['visitor_id'];

        $tables = Reports::getTables();
        $ci = $tables['reports_crawlers_visits']['columns'];

        $this->addSelectTable('reports_crawlers_visits');

        $this->addSelectField('visitor_id');
        $this->addSelectField('visit_time', 'creation_time');
        $this->addSelectField('name');
        $this->addSelectField('type');
        $this->addSelectField('ip', 'remote_ip');
        $this->addSelectField('host', 'remote_addr');
        $this->addSelectField('referrer');
        $this->addSelectField('entry_page');
        $this->addSelectField('scanned_pages');

        $this->SelectOrder('visit_time', "DESC");

        if ($from != null)
        {
            $this->WhereValue($ci["visit_time"], DB_GTE, $from);
            if ($to != null || $crawler != null || $visitor_id != null) $this->WhereAND();
        }
        if ($to != null)
        {
            $this->WhereValue($ci["visit_time"], DB_LTE, $to);
            if ($crawler != null || $visitor_id != null) $this->WhereAND();
        }
        if ($crawler != null)
        {
            $this->WhereValue($ci['name'], DB_EQ, $crawler);
            if ($visitor_id != null) $this->WhereAND();
        }
        if ($visitor_id != null)
        {
            $this->WhereValue($ci['visitor_id'], DB_EQ, $visitor_id);
        }

        if ($limit !== null and !empty($limit) and isset($limit[0]) and isset($limit[1]))
        {
            list($offset, $number) = $limit;
            $this->SelectLimit($offset, $number);
        }
    }

    function isCachable()
    {
        return false;
    }

}

class REPLACE_CRAWLER_VISIT extends DB_Replace
{
    function REPLACE_CRAWLER_VISIT ()
    {
        parent::DB_Replace('reports_crawlers_visits');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $cv = $tables['reports_crawlers_visits']['columns'];

        if ($params['visitor_id'] != null)
        {
            $this->addReplaceValue($params['visitor_id'], $cv['visitor_id']);
        }
        $this->addReplaceValue($params['visit_time'], $cv['visit_time']);
        $this->addReplaceValue($params['agent_string'], $cv['agent_string']);
        $this->addReplaceValue($params['name'], $cv['name']);
        $this->addReplaceValue($params['type'], $cv['type']);
        $this->addReplaceValue($params['ip'], $cv['ip']);
        $this->addReplaceValue($params['host'], $cv['host']);
        $this->addReplaceValue($params['referrer'], $cv['referrer']);
        if ($params['entry_page'] != null)
        {
            $this->addReplaceValue($params['entry_page'], $cv['entry_page']);
        }
        $this->addReplaceValue($params['scanned_pages'], $cv['scanned_pages']);
    }
}

class INSERT_CRAWLER_RECORD extends DB_Insert
{
    function INSERT_CRAWLER_RECORD()
    {
        parent::DB_Insert('reports_crawlers_info');
    }

    function initQuery($params)
    {
        $tables = Reports::getTables();
        $ci = $tables['reports_crawlers_info']['columns'];

        $this->addInsertValue($params['user_agent'],   $ci['agent_string']);
        $this->addInsertValue($params['name'],         $ci['name']);
        $this->addInsertValue($params['type'],         $ci['type']);
    }
}

class SELECT_REPORT_PAGE_URL_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports :: getTables();
        $pt = $tables['reports_page_urls']['columns'];

        $this -> addSelectField($pt['id'], 'id');
        $this -> WhereValue($pt['page_url'], DB_EQ, $params['page_url']);
    }
}

class SELECT_REPORTS_PAGES_BY_IDS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Reports :: getTables();
        $pt = $tables['reports_page_urls']['columns'];

        $this -> addSelectField($pt['id'], 'id');
        $this -> addSelectField($pt['page_url'], 'page_url');
        $this -> Where($pt['id'], DB_IN,
                       '(\'' . implode('\',\'', $params['ids']) . '\')');
    }
}

class INSERT_REPORT_PAGE_URL extends DB_Insert
{
    function INSERT_REPORT_PAGE_URL()
    {
        parent :: DB_Insert('reports_page_urls');
    }

    function initQuery($params)
    {
        $tables = Reports :: getTables();
        $pt = $tables['reports_page_urls']['columns'];

        $this -> addInsertValue($params['page_url'], $pt['page_url']);
    }
}
?>