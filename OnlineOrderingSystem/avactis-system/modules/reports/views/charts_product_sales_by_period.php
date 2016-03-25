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

class ChartProductSalesByDay extends CReportView
{
    function ChartProductSalesByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderChart';
        $this->__source_class_name = 'CProductSoldStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_ITEMS_SOLD_DAYS');
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'date',
                                                    'items_sold',
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','DATE'),
                                                    'items_sold' => getMsg('RPTS','ITEMS_SOLD'),
        ));
    }

    function prepareData()
    {
        loadClass('CProductInfo');

        $this->__source->run();

        $render_data = array();
        while ($row = $this->__source->fetchRecord())
        {
            $row['date'] = $this->__prepareDateToDisplay($row);
            $render_data[] = $row;
        }
        $this->__render_settings->setReportData($render_data);
    }

    function __prepareDateToDisplay($row)
    {
        $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
        return modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
    }
}

class ChartProductSalesByMonth extends ChartProductSalesByDay
{
    function ChartProductSalesByMonth()
    {
        parent::ChartProductSalesByDay();
        $this->__source_class_name = 'CProductSoldStatisticsByMonths';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_MONTH;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_ITEMS_SOLD_MONTHS');
    }

    function __prepareDateToDisplay($row)
    {
        return getMsg('SYS','GENERAL_MONTH_'.sprintf("%02d",$row['datetime_month'])).', '.$row['datetime_year'];
    }
}

class ChartProductSalesByYear extends ChartProductSalesByDay
{
    function ChartProductSalesByYear()
    {
        parent::ChartProductSalesByDay();
        $this->__source_class_name = 'CProductSoldStatisticsByYears';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_YEAR;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_ITEMS_SOLD_YEARS');
    }

    function __prepareDateToDisplay($row)
    {
        return $row['datetime_year'];
    }
}


?>