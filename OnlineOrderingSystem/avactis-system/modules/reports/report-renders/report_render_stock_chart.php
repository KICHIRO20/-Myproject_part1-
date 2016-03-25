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
/**
 * CReportRenderStockChart class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CReportRenderStockChart
{

    function CReportRenderStockChart()
    {
    }

    function output($report_settings)
    {


//        $res =  modApiFunc( "Charts",
//        "getStockChartAmchart",
//        'http://localhost/~af/trunk/avactis-system/admin/amcharts/stock/examples/data_at_irregular_intervals/data.csv',
//        array('title 1', 'title 2'),
//        460 // width
//            );

// $this->__render_settings->getReportID();

// http://localhost/~af/trunk/avactis-system/admin/reports.php?asc_action=getReportContent&type=Excel&reportName=ChartOrdersByDay&format=csv



    	global $application;

        $this->__report_settings = $report_settings;

        //                 ,                ,      -
        $report_rows = $this->__report_settings->getReportData();

        //                            ,
        $report_columns = $this->__report_settings->getColumnList();

        //                     ,          -                             ,          -                           .
        $report_column_headers = $this->__report_settings->getColumnHeaders();
        $report_column_units = $this->__report_settings->getColumnUnits();

        if (count($report_columns) < 2)
        {
            return "<br><br><br><br>".getMsg('RPTS','CHART_ERROR')."<br><br><br><br>";
        }

        if (empty($report_rows))
        {
            return "<br><br><br><br>".getMsg('RPTS','EMPTY_REPORT')."<br><br><br><br>";
        }

        $report_id = $this->__report_settings->getReportID();

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('asc_action', 'getReportContent');
        $request->setKey('type', 'Excel');
        $request->setKey('format', 'csv');
        $request->setKey('csv-header', 'false');
        $request->setKey('reportName', $report_id);

        $url_csv = $request->getURL();
        $chart_width = $this->__report_settings->getReportPlaceholderWidth()-20;


        $series = array();
        $units = array();
        //                             ,              ,
        for ($i=1; $i<count($report_columns); $i++)
        {
            $series[] = $report_column_headers[$report_columns[$i]];
            $units[] = isset($report_column_units[$report_columns[$i]]) ? $report_column_units[$report_columns[$i]] : '';
        }

        $res =  modApiFunc( "Charts", "getStockChartAmchart", $url_csv, $series, $chart_width, 450, $units);
        return $res['html'];

//        echo $url_csv;
//        echo "<br>";
//        _print($series);
//        echo $chart_width;
//        return '';
    }

    var $__report_settings = null;
}

?>