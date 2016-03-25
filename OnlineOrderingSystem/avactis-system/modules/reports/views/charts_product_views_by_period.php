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

class ChartProductViewsByDay extends CReportView
{
    function ChartProductViewsByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderChart';
        $this->__source_class_name = 'CProductStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_VIEWS_DAYS');
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'date',
                                                    'product_views',
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','DATE'),
                                                    'product_views' => getMsg('RPTS','PRODUCT_VIEWS'),
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

class ChartProductViewsByMonth extends ChartProductViewsByDay
{
    function ChartProductViewsByMonth()
    {
        parent::ChartProductViewsByDay();
        $this->__source_class_name = 'CProductStatisticsByMonths';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_MONTH;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_VIEWS_MONTHS');
    }

    function __prepareDateToDisplay($row)
    {
        return getMsg('SYS','GENERAL_MONTH_'.sprintf("%02d",$row['datetime_month'])).', '.$row['datetime_year'];
    }
}

class ChartProductViewsByYear extends ChartProductViewsByDay
{
    function ChartProductViewsByYear()
    {
        parent::ChartProductViewsByDay();
        $this->__source_class_name = 'CProductStatisticsByYears';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_YEAR;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_VIEWS_YEARS');
    }

    function __prepareDateToDisplay($row)
    {
        return $row['datetime_year'];
    }
}


?>