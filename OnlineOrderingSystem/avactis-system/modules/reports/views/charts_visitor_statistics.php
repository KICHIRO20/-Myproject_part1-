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

/*abstract*/class ChartVisitorStatisticsByDay extends CReportView
{
    var $__field = null;
    var $__field_name = null;
    var $__report_name = null;

    function ChartVisitorStatisticsByDay($report_name, $field, $field_name)
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderChart';
        $this->__source_class_name = 'CVisitorStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
        $this->__field = $field;
        $this->__field_name = $field_name;
        $this->__report_name = $report_name;
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return $this->__report_name;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'date',
                                                    $this->__field,
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','DATE'),
                                                    $this->__field => $this->__field_name,
        ));
    }

    function prepareData()
    {
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
        return modApiFunc("Localization", "date_format", $date);
    }
}

class ChartVisitorsByDay extends ChartVisitorStatisticsByDay
{
    function ChartVisitorsByDay()
    {
        parent::ChartVisitorStatisticsByDay(getMsg('RPTS','REPORT_VISITORS'), 'visitor_number', getMsg('RPTS','REPORT_VISITORS'));
    }
}

class ChartFirstTimeVisitorsByDay extends ChartVisitorStatisticsByDay
{
    function ChartFirstTimeVisitorsByDay()
    {
        parent::ChartVisitorStatisticsByDay(getMsg('RPTS','REPORT_FIRST_TIME_VISITORS'), 'first_time_visitor_number', getMsg('RPTS','REPORT_FIRST_TIME_VISITORS'));
    }
}


class ChartRepeatVisitorsByDay extends ChartVisitorStatisticsByDay
{
    function ChartRepeatVisitorsByDay()
    {
        parent::ChartVisitorStatisticsByDay(getMsg('RPTS','REPORT_REPEAT_VISITORS'), 'repeat_visitor_number', getMsg('RPTS','REPORT_REPEAT_VISITORS'));
    }
}

class ChartSeancesByDay extends ChartVisitorStatisticsByDay
{
    function ChartSeancesByDay()
    {
        parent::ChartVisitorStatisticsByDay(getMsg('RPTS','REPORT_VISITOR_SEANCES'), 'seance_number', getMsg('RPTS','REPORT_VISITOR_SEANCES'));
    }
}

class ChartPageViewsByDay extends ChartVisitorStatisticsByDay
{
    function ChartPageViewsByDay()
    {
        parent::ChartVisitorStatisticsByDay(getMsg('RPTS','REPORT_PAGE_VIEWS'), 'page_number', getMsg('RPTS','REPORT_PAGE_VIEWS'));
    }
}

?>