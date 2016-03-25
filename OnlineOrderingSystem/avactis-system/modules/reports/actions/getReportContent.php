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

class getReportContent extends AjaxAction
{
    function onAction()
    {
        global $application;
        global $zone;
        if ($zone == 'AdminZone')
        {
            $application->_exit(); //             ,                                                         .

            $report_name = modApiFunc('Request','getValueByKey','reportName');

            //                  ?
            $type = modApiFunc('Request','getValueByKey','type');
            if ($type !== null and $type == "Excel" and $report_name !== null and function_exists($report_name))
            {
                loadViewClass($report_name);
                $r = new $report_name();

                $format = modApiFunc('Request','getValueByKey','format');
                switch ($format)
                {
                    case 'csv':
                        $format = 'CSV';
                        break;

                    case 'binary_excel':
                        $format = 'BINARY_EXCEL';
                        break;

                    case 'html':
                        $format = 'HTML_TABLE';
                        break;

                    default:
                        $format = modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','DOWNLOAD_METHOD');
                        break;
                }

                $extension = '.xls';
                if ($format === 'BINARY_EXCEL')
                {
                    $r->__render_class_name = 'CReportRenderBinaryExcel'; //
                    $extension = '.xls';
                }
                elseif ($format === 'HTML_TABLE')
                {
                    $r->__render_class_name = 'CReportRenderSimpleHTMLTable';
                    $extension = '.html';
                }
                else
                {
                    $r->__render_class_name = 'CReportRenderCSV';
                    $extension = '.csv';
                }

                // change current report period with requested one
                $current_report_period = modApiFunc('Reports', 'getReportPeriodLabel', $report_name);
                $report_period = modApiFunc('Request','getValueByKey','reportPeriod');
                if ($report_period !==null && !empty($report_period))
                {
                	modApiFunc('Reports', 'setReportPeriodLabel', $report_name, $report_period);
                }


                $content =  $r->output(REPORT_OUTPUT_CONTENT);

                // restore report period
                modApiFunc('Reports', 'setReportPeriodLabel', $report_name, $current_report_period);


                header ("Pragma: no-cache");
                header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
                header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                if ($format != 'HTML_TABLE')
                    header ("Content-Type: application/vnd.ms-excel");
                else
                    header ("Content-Type: text/html");
                //header ("Content-Length: ".sizeof($content));
                header ("Content-Disposition: attachment; filename=${report_name}_".date("Y-m-d_H-i-s").$extension);
                return $content; //                           .
            }

            $report_placeholder_width = modApiFunc('Request','getValueByKey','reportPlaceholderWidth');
            $report_period = modApiFunc('Request','getValueByKey','reportPeriod');
            if ($report_name !== null and function_exists($report_name) and $report_placeholder_width !== null)
            {
                if ($report_period !==null && !empty($report_period))
                {
                    modApiFunc('Reports', 'setReportPeriodLabel', $report_name, $report_period);
                }
                return $report_name(REPORT_OUTPUT_CONTENT, $report_placeholder_width);
            }
            else
            {
                return "ERROR! ".__CLASS__.'::'.__FUNCTION__;
            }
        }
    }
}

?>