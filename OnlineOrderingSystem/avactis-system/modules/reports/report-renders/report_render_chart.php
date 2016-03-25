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
 * CReportRenderChart class
 *
 * @author Alexey Florinsky
 * @version $Id: report_render_flat_table.php 5013 2008-04-09 14:04:24Z af $
 * @package Reports
 */
class CReportRenderChart
{

    function CReportRenderChart()
    {
    }

    function output($report_settings)
    {
        global $application;

        $this->__report_settings = $report_settings;

        //                 ,                ,      -
        $report_rows = $this->__report_settings->getReportData();

        //                            ,
        $report_columns = $this->__report_settings->getColumnList();

        //                     ,          -                             ,          -                           .
        $report_column_headers = $this->__report_settings->getColumnHeaders();

        if (count($report_columns) < 2)
        {
            return "<br><br><br><br>".getMsg('RPTS','CHART_ERROR')."<br><br><br><br>"; //
        }

        if (empty($report_rows))
        {
            return "<br><br><br><br>".getMsg('RPTS','EMPTY_REPORT')."<br><br><br><br>";
        }

        $xlabels = array();
        $series_data = array();
        foreach ($report_rows as $row_data)
        {
            //
            $xlabels[] = array('text' => $row_data[$report_columns[0]]);

            //                             ,              ,                      $row_data
            for ($i=1; $i<count($report_columns); $i++)
            {
                $series_data[$report_columns[$i]][] = array('text' => $row_data[$report_columns[$i]]);
            }
        }

        $series = array();
        //                             ,              ,
        for ($i=1; $i<count($report_columns); $i++)
        {
            $series[] = array("title" => $report_column_headers[$report_columns[$i]], "values" => $series_data[$report_columns[$i]]);
        }

        $series_number = count($report_columns) - 1; //


        //                       ,                             :                         .
        //                                         -
        //        ,                          .              -
        //              ,                                     .

        //
        $chart_width = $this->__report_settings->getReportPlaceholderWidth()-20;
        //                      X
        $text_size =  modApiFunc("Charts", "getLineChartAmchartTextSize");
        //                        ,
        //
        $max_appropriate_xlabel_width = 1.5 * $text_size;
        //                           ,
        $column_number = count($report_rows)*$series_number;
        //                    ,
        $xlabels_number = (int)($chart_width / $max_appropriate_xlabel_width);
        //         -             1,
        $frequency =  (int)ceil($column_number / $xlabels_number);

        if ($frequency == 1)
        {
            //                                                      .
            //                            .                       ,                   _     _        .
            //        ,                                                                      render.

            //
            $max_column_width_pixels = 30;
            //
            $computed_column_width_pixels = (int)($chart_width / $column_number);

            if ($computed_column_width_pixels > $max_column_width_pixels )
            {
                $column_width_percent = (int)ceil(((100*$max_column_width_pixels * $column_number) / $chart_width));
            }
            else
            {
                $column_width_percent = 60;
            }
            $res = modApiFunc("Charts", "getBarChartAmchart", $xlabels, $series, $chart_width, 400, ' '.$this->__report_settings->getChartUnit(), 'right', $column_width_percent, $this->__report_settings->getTitle() );
        }
        else
        {
            $res = modApiFunc("Charts", "getLineChartAmchart", $xlabels, $series, $chart_width, 400, ' '.$this->__report_settings->getChartUnit(), 'right', $frequency, $this->__report_settings->getTitle());
        }
        return $res['html'];
    }

    var $__report_settings = null;
}

?>