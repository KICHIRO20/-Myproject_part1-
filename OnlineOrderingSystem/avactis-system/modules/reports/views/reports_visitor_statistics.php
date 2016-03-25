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

class ReportVisitorStatisticsByDay extends CReportView
{
    function ReportVisitorStatisticsByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'CVisitorStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_VISITORS_STAT');
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'date',
                                                    'visitor_number',
                                                    'first_time_visitor_number',
                                                    'repeat_visitor_number',
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','DATE'),
                                                    'visitor_number' => getMsg('RPTS','VISITORS_NUMBER'),
                                                    'repeat_visitor_number' => getMsg('RPTS','REPEAT_VISITORS_NUMBER'),
                                                    'first_time_visitor_number' => getMsg('RPTS','FIRST_TIME_VISITORS_NUMBER'),
        ));
        $this->__render_settings->setColumnUnits( array(
                                                    'date' => '',
                                                    'visitor_number' => getMsg('RPTS','VISITORS_NUMBER_UNIT'),
                                                    'repeat_visitor_number' => getMsg('RPTS','REPEAT_VISITORS_NUMBER_UNIT'),
                                                    'first_time_visitor_number' => getMsg('RPTS','FIRST_TIME_VISITORS_NUMBER_UNIT'),
        ));
    }

    function __formatRow($row)
    {
        switch (_ml_strtolower($this->__render_class_name))
        {
            case 'creportrendercsv':
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;

            default:
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
                break;
        }
        return $row;
    }
}


class ReportVisitsStatisticsByDay extends CReportView
{
    function ReportVisitsStatisticsByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'CVisitorStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_VISITS_STAT');
    }

    function setColumns()
    {
        $this->__render_settings->setColumnList( array(
                                                    'date',
                                                    'seance_number',
                                                    'page_number',
                                                    'page_views_per_seance',
        ));

        $this->__render_settings->setColumnHeaders( array(
                                                    'date' => getMsg('RPTS','DATE'),
                                                    'seance_number' => getMsg('RPTS','VISITOR_SESSIONS'),
                                                    'page_number' => getMsg('RPTS','PAGE_VIEWS'),
                                                    'page_views_per_seance' => getMsg('RPTS','RATE_PAGE_VIEWS'),
        ));

        $this->__render_settings->setColumnUnits( array(
                                                    'date' => '',
                                                    'seance_number' => getMsg('RPTS','VISITOR_SESSIONS_UNIT'),
                                                    'page_number' => getMsg('RPTS','PAGE_VIEWS_UNIT'),
                                                    'page_views_per_seance' => getMsg('RPTS','RATE_PAGE_VIEWS_UNIT'),
        ));
    }

    function __formatRow($row)
    {
        switch (_ml_strtolower($this->__render_class_name))
        {
            case 'creportrendercsv':
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;

            default:
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
                break;
        }
        return $row;
    }
}


?>