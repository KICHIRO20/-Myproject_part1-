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
loadModuleFile('reports/abstract/report_view.php');

class ChartOrdersByDay extends CReportView
{
    function ChartOrdersByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderChart';
        //$this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'COrderTotalStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
        $this->__currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_TOTAL_DAYS').', '.$this->__currency;
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            //'order_qty',
                                                            'order_total_sum',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            //'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'order_total_sum' => getMsg('RPTS','ORDERS_TOTAL_SUM'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default: // creportrenderflattable, excel ...
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'order_total_sum',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'order_total_sum' => getMsg('RPTS','ORDERS_TOTAL_SUM'),
                ));
                break;

        }
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['order_qty'] = modApiFunc("Localization", "num_format", $row['order_qty']);
                $row['order_total_sum'] = modApiFunc("Localization", "currency_format", round($row['order_total_sum'],2));
                break;
            case 'creportrenderchart':
                $row['order_total_sum'] = round($row['order_total_sum'],2);
                break;
        }
        return $row;
    }

    function __prepareDateToDisplay($row)
    {
        $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
        return modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
    }

    var $__currency = '';
}

class ChartOrdersByMonth extends ChartOrdersByDay
{
    function ChartOrdersByMonth()
    {
        parent::ChartOrdersByDay();
        $this->__source_class_name = 'COrderTotalStatisticsByMonths';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_MONTH;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_TOTAL_MONTHS').', '.$this->__currency;
    }

    function __prepareDateToDisplay($row)
    {
        return getMsg('SYS','GENERAL_MONTH_'.sprintf("%02d",$row['datetime_month'])).', '.$row['datetime_year'];
    }
}

class ChartOrdersByYear extends ChartOrdersByDay
{
    function ChartOrdersByYear()
    {
        parent::ChartOrdersByDay();
        $this->__source_class_name = 'COrderTotalStatisticsByYears';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_MONTH;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_TOTAL_YEARS').', '.$this->__currency;
    }

    function __prepareDateToDisplay($row)
    {
        return $row['datetime_year'];
    }
}

class ChartOrdersByDayLast10Days extends ChartOrdersByDay
{
    function ChartOrdersByDayLast10Days()
    {
        parent::ChartOrdersByDay();
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_TOTAL_LAST_10_DAYS').', '.$this->__currency;
    }
    function isExportToExcelApplicable()
    {
        return false;
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return false;
    }

    function initSource()
    {
        parent::initSource();
        $period = modApiFunc('Reports','getTimestampPeriodByDatetimeLabel',DATETIME_PERIOD_DAY_LAST_10);
        if ($period !== null)
        {
            list($from, $to) = $period;
            $from = toMySQLDatetime($from);
            $to = toMySQLDatetime($to);
            $this->__source->setDatetimePeriod($from, $to);
        }
    }
}

class ChartOrdersByDayLast10Months extends ChartOrdersByMonth
{
    function ChartOrdersByDayLast10Months()
    {
        parent::ChartOrdersByMonth();
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_TOTAL_LAST_10_MONTHS').', '.$this->__currency;
    }

    function initSource()
    {
        parent::initSource();
        $period = modApiFunc('Reports','getTimestampPeriodByDatetimeLabel',DATETIME_PERIOD_MONTH_LAST_10);
        if ($period !== null)
        {
            list($from, $to) = $period;
            $from = toMySQLDatetime($from);
            $to = toMySQLDatetime($to);
            $this->__source->setDatetimePeriod($from, $to);
        }
    }
}
?>