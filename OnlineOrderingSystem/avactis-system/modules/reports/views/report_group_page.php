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
 * ReportGroupPage view class
 *
 * Display all reports from specified group
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */

class ReportGroupPage
{
    function ReportGroupPage()
    {
    }

    function output()
    {
        $group_id = modApiFunc('Request','getValueByKey','report_group_id');
        $reports = modApiFunc('Reports','getReportGroups');
        if ($group_id !== null and isset($reports[$group_id]))
        {
            $reports = $reports[$group_id]['REPORTS'];
        }
        else
        {
            reset($reports);
            $current_group_id = array_keys($reports);
            $reports = $reports[$current_group_id[0]]['REPORTS'];
        }

        $TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-group-page/');

        $html = '';
        $prev_placeholder = -1;
        foreach ($reports as $report_tag => $placeholder_type)
        {
            $report_tag = 'get'.$report_tag;
            if ($prev_placeholder === REPORT_PLACEHOLDER_HALF)
            {
                if ($placeholder_type === REPORT_PLACEHOLDER_HALF)
                {
                    $report_right = $report_tag();
                    $html .= $TmplFiller->fill("", "row-half.tpl.html", array('ReportLeft' => $report_left, 'ReportRight'=>$report_right));
                    $prev_placeholder = -1;
                    $placeholder_type = -1;
                    continue;
                }
                else
                {
                    $report_right = '&nbsp;';
                    $html .= $TmplFiller->fill("", "row-half.tpl.html", array('ReportLeft' => $report_left, 'ReportRight'=>$report_right));
                    $prev_placeholder = -1;
                }
            }

            if ($placeholder_type === REPORT_PLACEHOLDER_FULL)
            {
                $html .= $TmplFiller->fill("", "row-full.tpl.html", array('Report' => $report_tag()));
                $prev_placeholder = REPORT_PLACEHOLDER_FULL;
            }

            if ($placeholder_type === REPORT_PLACEHOLDER_HALF)
            {
                $report_left = $report_tag();
                $prev_placeholder = REPORT_PLACEHOLDER_HALF;
            }

        }
        if ($placeholder_type === REPORT_PLACEHOLDER_HALF)
        {
            $report_right = '&nbsp;';
            $html .= $TmplFiller->fill("", "row-half.tpl.html", array('ReportLeft' => $report_left, 'ReportRight'=>$report_right));
        }
        return $html;
    }
}

?>