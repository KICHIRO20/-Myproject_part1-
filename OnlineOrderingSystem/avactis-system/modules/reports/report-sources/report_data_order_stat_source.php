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
 * COrderTotalStatisticsByDatetimePeriod abstract class
 *
 *                     -                                                    .
 *                                ( . .               )                 :
 * - order_total_sum (         main store currency)
 * - order_qty
 * - datetime_year
 * - datetime_month (                                                 )
 * - datetime_day (                                                 )
 *
 *            -                          .
 *
 */
class COrderTotalStatisticsByDatetimePeriod extends CReportDataSource
{
    function COrderTotalStatisticsByDatetimePeriod($discontinuity)
    {
        $this->__params = array();
        $this->__params['discontinuity'] = $discontinuity;
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
        $orders_stat = execQuery('SELECT_ORDERS_STAT_BY_DATETIME_PERIOD', $this->__params);
        /*
                                             (                         )                  :
                      ,                                                                         .
                                 ,                                                main store currency.
        */
        $this->__data = array();
        $main_store_currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
        foreach($orders_stat as $item)
        {
            $key_part_1 = isset($item['datetime_year']) ? $item['datetime_year'] : '';
            $key_part_2 = isset($item['datetime_month']) ? $item['datetime_month'] : '';
            $key_part_3 = isset($item['datetime_day']) ? $item['datetime_day'] : '';
            $key = $key_part_1.$key_part_2.$key_part_3;

            $order_currency = modApiFunc("Localization", "getCurrencyCodeById", $item['order_currency']);
            $order_total_in_order_currency = $item['order_total_sum'];
            $order_tax_in_order_currency = $item['order_tax_sum'];
            if($order_currency !== $main_store_currency)
            {
                //
                $total = modApiFunc('Currency_Converter','convert', $order_total_in_order_currency, $order_currency, $main_store_currency);
                $tax = modApiFunc('Currency_Converter','convert', $order_tax_in_order_currency, $order_currency, $main_store_currency);
            }
            else
            {
                $total = $order_total_in_order_currency;
                $tax = $order_tax_in_order_currency;
            }

            if (isset($this->__data[$key]))
            {
                $this->__data[$key]['order_total_sum'] += $total;
                $this->__data[$key]['order_tax_sum'] += $tax;
                $this->__data[$key]['order_qty'] += $item['order_qty'];
            }
            else
            {
                $this->__data[$key] = $item;
                $this->__data[$key]['order_total_sum'] = $total;
                $this->__data[$key]['order_tax_sum'] = $tax;
                if (isset($this->__data[$key]['order_currency']))
                {
                    unset($this->__data[$key]['order_currency']);
                }
            }
        }
        $this->__data = array_values($this->__data);
        $zero_item = array(
            'order_total_sum'=>0,
            'order_tax_sum'=>0,
            'order_qty'=>0,
        );
        $this->__data = $this->__addZeroItems($this->__data, $zero_item);
    }


    var $__params = null;
}

class COrderTotalStatisticsByDays extends COrderTotalStatisticsByDatetimePeriod
{
    function COrderTotalStatisticsByDays()
    {
        parent::COrderTotalStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_DAY);
    }
}

class COrderTotalStatisticsByMonths extends COrderTotalStatisticsByDatetimePeriod
{
    function COrderTotalStatisticsByMonths()
    {
        parent::COrderTotalStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_MONTH);
    }
}

class COrderTotalStatisticsByYears extends COrderTotalStatisticsByDatetimePeriod
{
    function COrderTotalStatisticsByYears()
    {
        parent::COrderTotalStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_YEAR);
    }
}

/**
 *
 * datetime_year
 * datetime_month
 * datetime_day
 *
 * order_total_sum
 * order_qty
 * carts_created_qty
 * seance_number
 * page_number
 * page_views_per_seance
 *
 */
class COrdersVisitorsCartsStatisticsByDatetimePeriod extends COrderTotalStatisticsByDatetimePeriod
{
    function COrdersVisitorsCartsStatisticsByDatetimePeriod($discontinuity)
    {
        parent::COrderTotalStatisticsByDatetimePeriod($discontinuity);
    }

    function run()
    {
        //                            $this->__data                            ,
        //                                             ,
        //                   "        "
        parent::run();

        //          (                                                        )
        $__keys = array('datetime_year', 'datetime_month', 'datetime_day');

        //
        $to_merge = array(
            $this->__data,
            execQuery('SELECT_CARTS_STAT_BY_DATETIME_PERIOD', $this->__params),
            execQuery('SELECT_SEANCE_STATISTICS_BY_DATETIME_PERIOD', $this->__params),
            execQuery('SELECT_VISITOR_NUMBER_BY_DATETIME_PERIOD', $this->__params),
        );

        //                                     (                              $to_merge)
        $empty_items = array(
            array('order_total_sum'=>0, 'order_qty'=>0),
            array('carts_created_qty'=>0),
            array('seance_number'=>0, 'page_number'=>0, 'page_views_per_seance'=>0),
            array('visitor_number' => 0),
        );

        $this->__data = $this->__margeArrays($to_merge, $empty_items, $__keys);
    }

}


class COrdersVisitorsCartsStatisticsByDays extends COrdersVisitorsCartsStatisticsByDatetimePeriod
{
    function COrdersVisitorsCartsStatisticsByDays()
    {
        parent::COrdersVisitorsCartsStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_DAY);
    }
}

class COrdersVisitorsCartsStatisticsByMonths extends COrdersVisitorsCartsStatisticsByDatetimePeriod
{
    function COrdersVisitorsCartsStatisticsByMonths()
    {
        parent::COrdersVisitorsCartsStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_MONTH);
    }
}

class COrdersVisitorsCartsStatisticsByYears extends COrdersVisitorsCartsStatisticsByDatetimePeriod
{
    function COrdersVisitorsCartsStatisticsByYears()
    {
        parent::COrdersVisitorsCartsStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_YEAR);
    }
}

?>