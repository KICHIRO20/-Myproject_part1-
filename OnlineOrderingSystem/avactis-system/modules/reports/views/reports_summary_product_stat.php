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

class ReportProductSummaryStatisticsByDays extends CReportView
{
    function ReportProductSummaryStatisticsByDays()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CProductStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SUMMARY_PRODUCT_STAT_DAYS');
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'date',
                                                    'product_views',
                                                    'items_sold',
                                                    'product_added_to_cart_qty',
                                                    'product_deleted_from_cart_qty',
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','DATE'),
                                                    'product_views' => getMsg('RPTS','PRODUCT_VIEWS'),
                                                    'items_sold' => getMsg('RPTS','ITEMS_SOLD'),
                                                    'product_added_to_cart_qty' => getMsg('RPTS','PRODUCT_ADDED_TO_CART_QTY'),
                                                    'product_deleted_from_cart_qty' => getMsg('RPTS','PRODUCT_REMOVED_TO_CART_QTY'),
        ));

        $this->__render_settings->setColumnTotalList( array(
                                                    'date' => 'Total:',
                                                    'product_views' => modApiFunc("Localization", "num_format",$this->__total_views),
                                                    'items_sold' => modApiFunc("Localization", "num_format",$this->__total_items_sold),
                                                    'product_added_to_cart_qty' => modApiFunc("Localization", "num_format",$this->__total_added_to_cart_qty),
                                                    'product_deleted_from_cart_qty' => modApiFunc("Localization", "num_format",$this->__total_deleted_from_cart_qty),
        ));
    }

    function setColumnStyles()
    {
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__total_views += $row['product_views'];
                $this->__total_items_sold += $row['items_sold'];
                $this->__total_added_to_cart_times += $row['product_added_to_cart_times'];
                $this->__total_added_to_cart_qty += $row['product_added_to_cart_qty'];
                $this->__total_deleted_from_cart_qty += $row['product_deleted_from_cart_qty'];
                $this->__total_deleted_from_cart_times += $row['product_deleted_from_cart_times'];

                $row['product_views'] = modApiFunc("Localization", "num_format", $row['product_views']);
                $row['items_sold'] = modApiFunc("Localization", "num_format", $row['items_sold']);
                $row['product_added_to_cart_times'] = modApiFunc("Localization", "num_format", $row['product_added_to_cart_times']);
                $row['product_deleted_from_cart_times'] = modApiFunc("Localization", "num_format", $row['product_deleted_from_cart_times']);
                $row['product_added_to_cart_qty'] = modApiFunc("Localization", "num_format", $row['product_added_to_cart_qty']);
                $row['product_deleted_from_cart_qty'] = modApiFunc("Localization", "num_format", $row['product_deleted_from_cart_qty']);
                break;

            default: // simple html table, binary excel or chart
                // Let's stay as is.
                $this->__total_views += $row['product_views'];
                $this->__total_items_sold += $row['items_sold'];
                $this->__total_added_to_cart_times += $row['product_added_to_cart_times'];
                $this->__total_added_to_cart_qty += $row['product_added_to_cart_qty'];
                $this->__total_deleted_from_cart_qty += $row['product_deleted_from_cart_qty'];
                $this->__total_deleted_from_cart_times += $row['product_deleted_from_cart_times'];
                break;
        }
        return $row;
    }

    function prepareData()
    {
        $this->__total_views = 0;
        $this->__total_items_sold = 0;
        $this->__total_added_to_cart_times = 0;
        $this->__total_added_to_cart_qty = 0;
        $this->__total_deleted_from_cart_qty = 0;
        $this->__total_deleted_from_cart_times = 0;
        parent::prepareData();
    }

    function __prepareDateToDisplay($row)
    {
        $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
        return modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
    }

    var $__total_views = 0;
    var $__total_items_sold = 0;
    var $__total_added_to_cart_times = 0;
    var $__total_added_to_cart_qty = 0;
    var $__total_deleted_from_cart_qty = 0;
    var $__total_deleted_from_cart_times = 0;

}

class ReportProductSummaryStatisticsByMonths extends ReportProductSummaryStatisticsByDays
{
    function ReportProductSummaryStatisticsByMonths()
    {
        parent::ReportProductSummaryStatisticsByDays();
        $this->__source_class_name = 'CProductStatisticsByMonths';
    }

    function setColumns()
    {
        parent::setColumns();
        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','MONTH'),
                                                    'product_views' => getMsg('RPTS','PRODUCT_VIEWS'),
                                                    'items_sold' => getMsg('RPTS','ITEMS_SOLD'),
                                                    'product_added_to_cart_qty' => getMsg('RPTS','PRODUCT_ADDED_TO_CART_QTY'),
                                                    'product_deleted_from_cart_qty' => getMsg('RPTS','PRODUCT_REMOVED_TO_CART_QTY'),
                ));
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SUMMARY_PRODUCT_STAT_MONTHS');
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_MONTH;
    }

    function __prepareDateToDisplay($row)
    {
        return getMsg('SYS','GENERAL_MONTH_'.sprintf("%02d",$row['datetime_month'])).', '.$row['datetime_year'];
    }

}


class ReportProductSummaryStatisticsByYears extends ReportProductSummaryStatisticsByDays
{
    function ReportProductSummaryStatisticsByYears()
    {
        parent::ReportProductSummaryStatisticsByDays();
        $this->__source_class_name = 'CProductStatisticsByYears';
    }

    function setColumns()
    {
        parent::setColumns();
        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','YEAR'),
                                                    'product_views' => getMsg('RPTS','PRODUCT_VIEWS'),
                                                    'items_sold' => getMsg('RPTS','ITEMS_SOLD'),
                                                    'product_added_to_cart_qty' => getMsg('RPTS','PRODUCT_ADDED_TO_CART_QTY'),
                                                    'product_deleted_from_cart_qty' => getMsg('RPTS','PRODUCT_REMOVED_TO_CART_QTY'),
                ));
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SUMMARY_PRODUCT_STAT_YEARS');
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_YEAR;
    }

    function __prepareDateToDisplay($row)
    {
        return $row['datetime_year'];
    }
}

?>