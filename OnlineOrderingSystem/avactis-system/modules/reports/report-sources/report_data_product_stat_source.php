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
loadModuleFile('reports/abstract/report_data_source.php');

/**
 * CProductSalesByDatetimePeriod abstract class
 *
 *                     -                                                    .
 *                                ( . .               )                             :
 * - items_sold
 * - product_views
 * - product_added_to_cart_qty
 * - product_added_to_cart_times
 * - product_deleted_from_cart_times
 * - product_deleted_from_cart_qty
 *
 *            -                          .
 *
 */
class CProductStatisticsByDatetimePeriod extends CReportDataSource
{
    function CProductStatisticsByDatetimePeriod($discontinuity)
    {
        $this->__params = array();
        $this->__params['discontinuity'] = $discontinuity;
        $this->__params['to'] = null;
        $this->__params['from'] = null;
    }

    function setDatetimePeriod($from, $to)
    {
        $this->__params['to'] = $to;
        $this->__params['from'] = $from;
    }

    function run()
    {
    	$this->__data = execQuery('PRODUCT_STAT_SELECT_STATISTICS_BY_DATETIME_PERIOD', $this->__params);

        $zero_item = array(
            'items_sold'=>0,
            'product_views'=>0,
            'product_added_to_cart_qty'=>0,
            'product_added_to_cart_times'=>0,
            'product_deleted_from_cart_times'=>0,
            'product_deleted_from_cart_qty'=>0,
        );
        $this->__data = $this->__addZeroItems($this->__data, $zero_item);
    }


    var $__params = null;
}

/**
 * CProductStatisticsByDays class
 *
 *                                                       :
 * - items_sold
 * - product_views
 * - product_added_to_cart_qty
 * - product_added_to_cart_times
 * - product_deleted_from_cart_times
 * - product_deleted_from_cart_qty
 * - datetime_year
 * - datetime_month
 * - datetime_day
 *
 */
class CProductStatisticsByDays extends CProductStatisticsByDatetimePeriod
{
    function CProductStatisticsByDays()
    {
        parent::CProductStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_DAY);
    }
}

/**
 * CProductStatisticsByMonths class
 *
 *                                                       :
 * - items_sold
 * - product_views
 * - product_added_to_cart_qty
 * - product_added_to_cart_times
 * - product_deleted_from_cart_times
 * - product_deleted_from_cart_qty
 * - datetime_year
 * - datetime_month
 *
 */
class CProductStatisticsByMonths extends CProductStatisticsByDatetimePeriod
{
    function CProductStatisticsByMonths()
    {
        parent::CProductStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_MONTH);
    }
}

/**
 * CProductStatisticsByYears class
 *
 *                                                       :
 * - items_sold
 * - product_views
 * - product_added_to_cart_qty
 * - product_added_to_cart_times
 * - product_deleted_from_cart_times
 * - product_deleted_from_cart_qty
 * - datetime_year
 *
 */
class CProductStatisticsByYears extends CProductStatisticsByDatetimePeriod
{
    function CProductStatisticsByYears()
    {
        parent::CProductStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_YEAR);
    }
}

/*****These datasources are based on a new statistics collection scheme.*******/
/*****Statistics are based on stored order products data instead of mixed views and order products statistics data *****/

class CProductSoldStatisticsByDatetimePeriod extends CProductStatisticsByDatetimePeriod
{
	function CProductSoldStatisticsByDatetimePeriod($discontinuity)
	{
		parent::CProductStatisticsByDatetimePeriod($discontinuity);
		$this->applyReportFilteredStatuses();
	}

	function run()
	{
		$this->__data = execQuery('SELECT_SOLD_ITEMS_STATISTICS_BY_DATETIME_PERIOD', $this->__params);

        $zero_item = array(
            'items_sold'=>0
        );
        $this->__data = $this->__addZeroItems($this->__data, $zero_item);
	}
}

/**
 * CProductSoldStatisticsByDays class
 *
 *                                                       :
 * - items_sold
 * - datetime_year
 * - datetime_month
 * - datetime_day
 *
 */
class CProductSoldStatisticsByDays extends CProductSoldStatisticsByDatetimePeriod
{
    function CProductSoldStatisticsByDays()
    {
        parent::CProductSoldStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_DAY);
    }
}

/**
 * CProductSoldStatisticsByMonths class
 *
 *                                                       :
 * - items_sold
 * - datetime_year
 * - datetime_month
 *
 */
class CProductSoldStatisticsByMonths extends CProductSoldStatisticsByDatetimePeriod
{
    function CProductSoldStatisticsByMonths()
    {
        parent::CProductSoldStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_MONTH);
    }
}

/**
 * CProductSoldStatisticsByYears class
 *
 *                                                       :
 * - items_sold
 * - datetime_year
 *
 */
class CProductSoldStatisticsByYears extends CProductSoldStatisticsByDatetimePeriod
{
    function CProductSoldStatisticsByYears()
    {
        parent::CProductSoldStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_YEAR);
    }
}

/*
 * It is datasource for "ReportProductAddedCartVsSalesByDay" report.
 */
class CProductSoldExtendedStatisticsByDays extends CProductSoldStatisticsByDays
{
	function CProductSoldExtendedStatisticsByDatetimePeriod()
	{
		parent::CProductSoldStatisticsByDays();
	}

	function run()
	{
		$to_merge = array(
		    execQuery('SELECT_SOLD_ITEMS_STATISTICS_BY_DATETIME_PERIOD', $this->__params),
		    execQuery('PRODUCT_STAT_SELECT_STATISTICS_BY_DATETIME_PERIOD', $this->__params)
		);

		$empty_items = array(
            array('items_sold'=>0),
            array('product_added_to_cart_qty'=>0)
        );

        $this->__data = $this->__margeArrays($to_merge, $empty_items, array ('datetime_year', 'datetime_month', 'datetime_day'));

        $zero_item = array(
            'items_sold'=>0,
            'product_added_to_cart_qty'=>0,
            'product_views'=>0
        );

        $this->__data = $this->__addZeroItems($this->__data, $zero_item);
	}
}

/**********************************************************/

class CProductTop10 extends CReportDataSource
{
    function CProductTop10($field_alias, $aggregating_field)
    {
        $this->__params = array();
        $this->__params['to'] = null;
        $this->__params['from'] = null;
        $this->__params['field_alias'] = $field_alias;
        $this->__params['aggregating_field'] = $aggregating_field;
    }

    function setDatetimePeriod($from, $to)
    {
        $this->__params['to'] = $to;
        $this->__params['from'] = $from;
    }

    function run()
    {
        $data = execQuery('PRODUCT_STAT_SELECT_TOP_10', $this->__params);
        $this->__data = $this->__computePercentField($data, $this->__params['field_alias'], 'percent');
    }

    var $__params = null;
}

class CProductTop10SellersByItems extends CProductTop10
{
    function CProductTop10SellersByItems()
    {
        parent::CProductTop10('items_sold','sale_items');
    }
}

class CProductTop10Viewed extends CProductTop10
{
    function CProductTop10Viewed()
    {
        parent::CProductTop10('product_views','views');
    }
}

class CProductTop10AddedToCartTimes extends CProductTop10
{
    function CProductTop10AddedToCartTimes()
    {
        parent::CProductTop10('product_added_to_cart_times','added_to_cart_times');
    }
}

class CProductTop10SellersByItemsFiltered extends CReportDataSource
{
	function CProductTop10SellersByItemsFiltered()
	{
		$this->__params = array();
		$this->__params['to'] = null;
        $this->__params['from'] = null;
		$this->applyReportFilteredStatuses();
	}

    function setDatetimePeriod($from, $to)
    {
        $this->__params['to'] = $to;
        $this->__params['from'] = $from;
    }

	function run()
	{
		$data = execQuery('SELECT_STAT_TOP_10_PRODUCTS_FILTERED', $this->__params);
        $this->__data = $this->__computePercentField($data, 'items_sold', 'percent');
    }
}

class CProductTop10DeletedFromCartTimes extends CProductTop10
{
    function CProductTop10DeletedFromCartTimes()
    {
        parent::CProductTop10('product_deleted_from_cart_times','deleted_from_cart_times');
    }
}

class CProductTop10AddedToCartQuantity extends CProductTop10
{
    function CProductTop10AddedToCartQuantity()
    {
        parent::CProductTop10('product_added_to_cart_qty','added_to_cart_qty');
    }
}

class CProductTop10DeletedFromCartQuantity extends CProductTop10
{
    function CProductTop10DeletedFromCartQuantity()
    {
        parent::CProductTop10('product_deleted_from_cart_qty','deleted_from_cart_qty');
    }
}





//+++++++++++++++++++++++++
class CProductAll extends CReportDataSource
{
	function CProductAll($field_alias, $aggregating_field)
	{
		$this->__params = array();
		$this->__params['to'] = null;
		$this->__params['from'] = null;
		$this->__params['field_alias'] = $field_alias;
		$this->__params['aggregating_field'] = $aggregating_field;
	}

	function setDatetimePeriod($from, $to)
	{
		$this->__params['to'] = $to;
		$this->__params['from'] = $from;
	}

	function run()
	{
		$data = execQuery('PRODUCT_STAT_SELECT', $this->__params);
		$this->__data = $this->__computePercentField($data, $this->__params['field_alias'], 'percent');
	}

	var $__params = null;
}

class CProductAllSellersByItems extends CProductAll
{
	function CProductAllSellersByItems()
	{
		parent::CProductAll('items_sold','sale_items');
	}
}

class CProductAllViewed extends CProductAll
{
	function CProductAllViewed()
	{
		parent::CProductAll('product_views','views');
	}
}

class CProductAllAddedToCartTimes extends CProductAll
{
	function CProductAllAddedToCartTimes()
	{
		parent::CProductAll('product_added_to_cart_times','added_to_cart_times');
	}
}

class CProductAllSellersByItemsFiltered extends CReportDataSource
{
	function CProductAllSellersByItemsFiltered()
	{
		$this->__params = array();
		$this->__params['to'] = null;
		$this->__params['from'] = null;
		$this->applyReportFilteredStatuses();
	}

	function setDatetimePeriod($from, $to)
	{
		$this->__params['to'] = $to;
		$this->__params['from'] = $from;
	}

	function run()
	{
		$data = execQuery('SELECT_STAT_PRODUCTS_FILTERED', $this->__params);
		$this->__data = $this->__computePercentField($data, 'items_sold', 'percent');
	}
}

class CProductAllDeletedFromCartTimes extends CProductAll
{
	function CProductAllDeletedFromCartTimes()
	{
		parent::CProductAll('product_deleted_from_cart_times','deleted_from_cart_times');
	}
}

class CProductAllAddedToCartQuantity extends CProductAll
{
	function CProductAllAddedToCartQuantity()
	{
		parent::CProductAll('product_added_to_cart_qty','added_to_cart_qty');
	}
}

class CProductAllDeletedFromCartQuantity extends CProductAll
{
	function CProductAllDeletedFromCartQuantity()
	{
		parent::CProductAll('product_deleted_from_cart_qty','deleted_from_cart_qty');
	}
}
?>