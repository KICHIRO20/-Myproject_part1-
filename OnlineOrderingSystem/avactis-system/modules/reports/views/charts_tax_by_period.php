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

class ChartTaxByDay extends CReportView
{
    function ChartTaxByDay()
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
        return getMsg('RPTS','REPORT_TAXES_DAYS').', '.$this->__currency;
    }

    function setColumns()
    {
        switch(strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_tax_sum',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_tax_sum' => getMsg('RPTS','ORDERS_TAX_SUM'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default: // creportrenderflattable, excel ...
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_tax_sum',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_tax_sum' => getMsg('RPTS','ORDERS_TAX_SUM'),
                ));
                break;

        }
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);
        switch(strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['order_tax_sum'] = modApiFunc("Localization", "currency_format", round($row['order_tax_sum'],2));
                break;
            case 'creportrenderchart':
                $row['order_tax_sum'] = round($row['order_tax_sum'],2);
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

class ChartTaxByMonth extends ChartTaxByDay
{
    function ChartTaxByMonth()
    {
        parent::ChartTaxByDay();
        $this->__source_class_name = 'COrderTotalStatisticsByMonths';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_MONTH;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TAXES_MONTHS').', '.$this->__currency;
    }

    function __prepareDateToDisplay($row)
    {
        return getMsg('SYS','GENERAL_MONTH_'.sprintf("%02d",$row['datetime_month'])).', '.$row['datetime_year'];
    }
}

class ChartTaxByYear extends ChartTaxByDay
{
    function ChartTaxByYear()
    {
        parent::ChartTaxByDay();
        $this->__source_class_name = 'COrderTotalStatisticsByYears';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_YEAR;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TAXES_YEARS').', '.$this->__currency;
    }

    function __prepareDateToDisplay($row)
    {
        return $row['datetime_year'];
    }
}

?>