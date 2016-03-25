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

class ReportRecentVisitorStatistics extends CReportView
{
    function ReportRecentVisitorStatistics()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CVisitorRecentVisitorStatistics';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_LAST_100_VISITORS');
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return false;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnHeaders( array(
                                                    'reverse_number' => getMsg('RPTS','NUMBER'),
                                                    'seance_time' => getMsg('RPTS','TIMESTAMP'),
                                                    'repeat_visitor' => getMsg('RPTS','REPEAT_VISITOR'),
                                                    'referer' => getMsg('RPTS','REFERER'),
                                                    'entry_page' => getMsg('RPTS','ENTRY_PAGE'),
                                                    'depth_of_visit' => getMsg('RPTS','CLICK_PATH'),
                                                    'visitor_ip' => getMsg('RPTS','REMOTE_ADDR'),
                                                    'visitor_os_browser' => getMsg('RPTS','BROWSER_OS'),
                                                    'visit_number' => getMsg('RPTS','VISIT_NUMBER'),
                                                    'previous_visit_date' => getMsg('RPTS','PREV_VISIT_DATE'),
                                                    'remote_ip' => getMsg('RPTS','VISITOR_IP'),
                                                    'remote_addr' => getMsg('RPTS','VISITOR_SERVER_NAME'),
                                                    'visitor_os' => getMsg('RPTS','VISITOR_OS'),
                                                    'visitor_browser' => getMsg('RPTS','VISITOR_BROWSER'),
                                                    'visitor_ip' => getMsg('RPTS','REMOTE_ADDR'),
                                                    'visitor_os_browser' => getMsg('RPTS','BROWSER_OS'),
                                                    'online_flag' => getMsg('RPTS','VISITOR_STATUS'),
        ));
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__render_settings->setColumnList( array(
                                                            'reverse_number',
                                                            'seance_time',
                                                            'repeat_visitor',
                                                            'depth_of_visit',
                                                            'referer',
                                                            'entry_page',
                                                            'visitor_ip',
                                                            'visitor_os_browser',
                                                            'online_flag',
                ));
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'reverse_number',
                                                            'seance_time',
                                                            'repeat_visitor',
                                                            'visit_number',
                                                            'previous_visit_date',
                                                            'depth_of_visit',
                                                            'referer',
                                                            'entry_page',
                                                            'remote_ip',
                                                            'remote_addr',
                                                            'visitor_os',
                                                            'visitor_browser',
                ));
                break;
        }
    }

    function setColumnStyles()
    {
        $this->__render_settings->setColumnStyles(array(
                                                    'reverse_number'    => 'font-weight: normal; color: black;',
                                                    'seance_time' => 'font-weight: normal; color: black;',
                                                    'repeat_visitor' => 'font-weight: normal; color: black;',
                                                    'referer' => 'font-weight: normal; color: black;',
                                                    'entry_page' => 'font-weight: normal; color: black;',
                                                    'depth_of_visit' => 'font-weight: normal; color: black;',
                                                    'visitor_ip' => 'font-weight: normal; color: black;',
                                                    'visitor_os_browser'  => 'font-weight: normal; color: black;',
        ));
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row = $this->__formatAsVisibleRow($row);
                break;
            default: // simple html table, binary excel or chart
                $row = $this->__formatAsRawData($row);
                break;

        }
        return $row;
    }

    function __formatAsRawData($row)
    {
        # SEANCE_TIME
        $row['seance_time'] = modApiFunc("Localization", "SQL_date_format", $row['creation_time']).' '.modApiFunc("Localization", "SQL_time_format", $row['creation_time']);

        # REPEAT_VISITOR
        if ($row['visit_number'] <= 1)
        {
            $row['repeat_visitor'] = getMsg('RPTS','l_NO');
            $row['previous_visit_date'] = '';
            $row['visit_number'] = '1';
        }
        else
        {
            $row['repeat_visitor'] = getMsg('RPTS','l_YES');
            $row['previous_visit_date'] =   modApiFunc("Localization", "SQL_date_format", $row['visit_previous_time']).
                                            ' '.
                                            modApiFunc("Localization", "SQL_time_format", $row['visit_previous_time']);
        }

        # REFERER
        if (empty($row['referer']))
        {
            $row['referer'] = getMsg('RPTS','DIRECT_ACCESS');
        }
        else
        {
            // as is
        }

        # ENTRY_PAGE
        // $row['entry_page']

        # DEPTH_OF_VISIT
        $row['depth_of_visit'] = count($row['click_path']);

        # VISITOR_IP
        //$row['remote_ip'];
        //$row['remote_addr'] = 'b';

        # VISITOR_OS_BROWSER
        //$row['visitor_os'], $row['visitor_browser'];
        return $row;
    }

    function __formatAsVisibleRow($row)
    {
        # REVERSE_NUMBER
        $row['reverse_number'] .= '.';
        $row['reverse_number'] = $this->__colorizeValue($row['reverse_number'], $row['online_status']);

        # SEANCE_TIME
        $row['seance_time'] = modApiFunc("Localization", "date_format", $row['creation_time']).'<br>'.modApiFunc("Localization", "SQL_time_format", $row['creation_time']);
        $row['seance_time'] = $this->__colorizeValue($row['seance_time'], $row['online_status']);

        # REPEAT_VISITOR
        if ($row['visit_number'] <= 1)
        {
            $row['repeat_visitor'] = getMsg('RPTS','l_NO');
            $row['repeat_visitor'] = $this->__colorizeValue($row['repeat_visitor'], $row['online_status']);
        }
        else
        {
            $row['repeat_visitor'] = getMsg('RPTS','l_YES').'<br>'.modApiFunc("Localization", "num_format", $row['visit_number']);

            if (substr($row['visit_number'], "-1") == 2)
            {
                $row['repeat_visitor'] .= getMsg('RPTS','ND_VISIT');
            }
            elseif (substr($row['visit_number'], "-1") == 3)
            {
                $row['repeat_visitor'] .= getMsg('RPTS','RD_VISIT');
            }
            else
            {
                $row['repeat_visitor'] .= getMsg('RPTS','TH_VISIT');
            }

            if ($row['visit_previous_time'] !== 0)
            {
                $duration = (strtotime($row['creation_time']) - strtotime($row['visit_previous_time']));

                $msg = '<nobr>'.getMsg('RPTS','PREV_VISIT').' ' .
//                       modApiFunc("Localization", "SQL_date_format", $row['visit_previous_time']).
//                       ' '.
//                       modApiFunc("Localization", "SQL_time_format", $row['visit_previous_time']).
                         modApiFunc("Localization", "formatTimeDuration", $duration) .
                         getMsg('RPTS','PREV_VISIT_BEFORE') .
                       '</nobr>';
                $hint = '<span onmouseover="return overlib(\''.$msg.'\');" onmouseout="return nd();">';
                $row['repeat_visitor'] = $this->__colorizeValue($row['repeat_visitor'], $row['online_status']);
                $row['repeat_visitor'] = $hint.$row['repeat_visitor'].'</span>';
            }
            else
            {
                $row['repeat_visitor'] = $this->__colorizeValue($row['repeat_visitor'], $row['online_status']);
            }
        }

        # REFERER
        if (empty($row['referer']))
        {
            $row['referer'] = getMsg('RPTS','DIRECT_ACCESS');
            $row['referer'] = $this->__colorizeValue($row['referer'], $row['online_status']);
        }
        else
        {
            $hint = '<span onmouseover="return overlib(\''.$row['referer'].'\');" onmouseout="return nd();">';
            $row['referer_host'] = $this->__colorizeValue(getMsg('RPTS','GO').' '.$row['referer_host'], $row['online_status']);
            $row['referer'] = $hint.'<A target="_blank" style="text-decoration: none;" href="'.$row['referer'].'">'.$row['referer_host'].'</A></span>';
        }

        # ENTRY_PAGE
        $parsed = @parse_url($row['entry_page']);
        if (isset($parsed['path']) and !empty($parsed['path']))
        {
            $row['entry_page'] = $parsed['path'];
        }
        //        ,
        $cut_entry_page = str_rev_pad($row['entry_page'], 40);
        if ($cut_entry_page != $row['entry_page'])
        {
            //              ,
            $hint = '<span onmouseover="return overlib(\'<nobr>'.$row['entry_page'].'</nobr>\');" onmouseout="return nd();">';
            $row['entry_page'] = $this->__colorizeValue($cut_entry_page, $row['online_status']);
            $row['entry_page'] = $hint.$row['entry_page'].'</span>';
        }
        else
        {
            $row['entry_page'] = $this->__colorizeValue($row['entry_page'], $row['online_status']);
        }

        # DEPTH_OF_VISIT
        $c = 1;
        $restr = intval(modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','VIEWED_PAGES_TO_SHOW'));

        $cr = count($row['click_path'])-1;
        if ($cr > $restr)
        {
            for ($ii = $restr;$ii<=$cr;$ii++)
            {
        	    unset($row['click_path'][$ii]);
            }
        }

        foreach ($row['click_path'] as $k=>$v)
        {
        	$row['click_path'][$k] = '<nobr><b>'.$c++.':</b> '.$v.'</nobr>';
        }

        $msg = implode('<br>', $row['click_path']);
        if ($cr > $restr)
        {
        	$msg.= "<br>...";
        }
        $p = $row['visit_depth'] > 1 ? getMsg('RPTS','PAGES') : getMsg('RPTS','PAGE');
        $hint = '<span onmouseover="return overlib(\''.$msg.'\');" onmouseout="return nd();">';
        $row['visit_depth'] = $this->__colorizeValue(getMsg('RPTS','GO').' '.$row['visit_depth'].' '.$p, $row['online_status']);
        $href = 'javascript:void(0)';
        $onclick = "javascript:openURLinNewWindow('reports_click_path.php?sid=".$row['seance_id']."', 'ReportsClickPath');";
        $row['depth_of_visit'] = $hint.'<A onclick="'.$onclick.'" href="'.$href.'"  style="text-decoration: none;">'.$row['visit_depth'].'</A></span>';

        # VISITOR_IP
        $row['visitor_ip'] = $row['remote_ip'].'<br>'.$row['remote_host'];
        $row['visitor_ip'] = $this->__colorizeValue($row['visitor_ip'], $row['online_status']);

        # VISITOR_OS_BROWSER
        $row['visitor_os_browser'] = $row['visitor_os'].'<br>'.$row['visitor_browser'];
        $row['visitor_os_browser'] = $this->__colorizeValue($row['visitor_os_browser'], $row['online_status']);

        # ONLINE_FLAG
        if ($row['online_status'] == true)
        {
             $online = getMsg('RPTS','VISITOR_STATUS_ONLINE');
             $row['online_flag'] = "<span class='label label-sm label-success'>".$online."</span>";

        }
        else
        {
            $row['online_flag'] = getMsg('RPTS','VISITOR_STATUS_OFFLINE');
        }
        $row['online_flag'] = $this->__colorizeValue($row['online_flag'], $row['online_status']);


        return $row;
    }

    function __colorizeValue($value, $flag, $style="font-weight: normal;")
    {
        if ($flag === true)
        {
            return '<span style="'.$style.' color: green;">'.$value.'</span>';
        }
        else
        {
            return $value;
        }
    }

}

/*
 * This reports is fully analogous to "ReportRecentVisitorStatistics" one. but it shows the data for web crawlers only.
 */
class ReportRecentCrawlersStatistics extends CReportView
{
    function ReportRecentCrawlersStatistics()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CVisitorRecentCrawlerStatistics';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_LAST_100_CRAWLERS');
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return false;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnHeaders( array(
                                                    'reverse_number' => getMsg('RPTS','NUMBER'),
                                                    'seance_time' => getMsg('RPTS','CRAWLER_FIRST_VISIT'),
                                                    'name' => getMsg('RPTS','CRAWLER_NAME'),
                                                    'referer' => getMsg('RPTS','REFERER'),
                                                    'entry_page' => getMsg('RPTS','ENTRY_PAGE'),
                                                    'depth_of_visit' => getMsg('RPTS','CRAWLER_PATH'),
                                                    'remote_ip' => getMsg('RPTS','CRAWLER_IP'),
                                                    'remote_addr' => getMsg('RPTS','CRAWLER_HOST')
        ));
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__render_settings->setColumnList( array(
                                                            'reverse_number',
                                                            'seance_time',
                                                            'name',
                                                            'referer',
                                                            'entry_page',
                                                            'depth_of_visit',
                                                            'remote_ip',
                                                            'remote_addr'
                ));
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'reverse_number',
                                                            'seance_time',
                                                            'name',
                                                            'referer',
                                                            'entry_page',
                                                            'depth_of_visit',
                                                            'remote_ip',
                                                            'remote_addr'
                ));
                break;
        }
    }

    function setColumnStyles()
    {
        $this->__render_settings->setColumnStyles(array(
                                                    'reverse_number'    => 'font-weight: normal; color: black;',
                                                    'seance_time' => 'font-weight: normal; color: black;',
                                                    'name' => 'font-weight: normal; color: black;',
                                                    'referer' => 'font-weight: normal; color: black;',
                                                    'entry_page' => 'font-weight: normal; color: black;',
                                                    'depth_of_visit' => 'font-weight: normal; color: black;',
                                                    'remote_ip' => 'font-weight: normal; color: black;',
                                                    'remote_addr'  => 'font-weight: normal; color: black;',
        ));
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row = $this->__formatAsVisibleRow($row);
                break;
            default: // simple html table, binary excel or chart
                $row = $this->__formatAsRawData($row);
                break;

        }
        return $row;
    }

    function __formatAsRawData($row)
    {
        # SEANCE_TIME
        $row['seance_time'] = modApiFunc("Localization", "SQL_date_format", $row['creation_time']).' '.modApiFunc("Localization", "SQL_time_format", $row['creation_time']);

        # REFERER
        if (empty($row['referer']))
        {
            $row['referer'] = getMsg('RPTS','DIRECT_ACCESS');
        }
        else
        {
            // as is
        }

        # ENTRY_PAGE
        // $row['entry_page']

        # DEPTH_OF_VISIT
        $row['depth_of_visit'] = count($row['click_path']);

        # VISITOR_IP
        //$row['remote_ip'];
        //$row['remote_addr'] = 'b';

        # VISITOR_OS_BROWSER
        //$row['visitor_os'], $row['visitor_browser'];
        return $row;
    }

    function __formatAsVisibleRow($row)
    {
    	# REVERSE_NUMBER
        $row['reverse_number'] .= '.';
        $row['reverse_number'] = $this->__colorizeValue($row['reverse_number'], $row['online_status']);

        # SEANCE_TIME
        $row['seance_time'] = modApiFunc("Localization", "SQL_date_format", $row['creation_time']);//.'<br>'.modApiFunc("Localization", "SQL_time_format", $row['creation_time']);
        $row['seance_time'] = $this->__colorizeValue($row['seance_time'], $row['online_status']);

        # REFERER
        //: "referer" field name should be used instead of "referrer".

        if (empty($row['referrer']))
        {
            $row['referer'] = getMsg('RPTS','DIRECT_ACCESS');
            $row['referer'] = $this->__colorizeValue($row['referer'], $row['online_status']);
        }
        else
        {
            $u = @parse_url($row['referrer']);

            if (count($u) == 1)
            {
                $u = @parse_url("http://".$row['referrer']);
                $row['referrer'] = "http://".$row['referrer'];
            }

            $host = $u['host'];
        	$hint = '<span onmouseover="return overlib(\''.$row['referrer'].'\');" onmouseout="return nd();">';
            $row['referer_host'] = $this->__colorizeValue(getMsg('RPTS','GO').' '.$host, $row['online_status']);
            $row['referer'] = $hint.'<A target="_blank" style="text-decoration: none;" href="'.$row['referrer'].'">'.$row['referer_host'].'</A></span>';
        }

        # ENTRY_PAGE
        $parsed = @parse_url($row['entry_page']);
        if (isset($parsed['path']) and !empty($parsed['path']))
        {
            $row['entry_page'] = $parsed['path'];
        }
        //        ,
        $cut_entry_page = str_rev_pad($row['entry_page'], 40);
        if ($cut_entry_page != $row['entry_page'])
        {
            //              ,
            $hint = '<span onmouseover="return overlib(\'<nobr>'.$row['entry_page'].'</nobr>\');" onmouseout="return nd();">';
            $row['entry_page'] = $this->__colorizeValue($cut_entry_page, $row['online_status']);
            $row['entry_page'] = $hint.$row['entry_page'].'</span>';
        }
        else
        {
            $row['entry_page'] = $this->__colorizeValue($row['entry_page'], $row['online_status']);
        }

        # DEPTH_OF_VISIT
        $c = 1;
        $restr = intval(modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','VIEWED_PAGES_TO_SHOW'));

        $cr = count($row['click_path'])-1;

        if ($cr > $restr)
        {
        	for ($ii = $restr;$ii<=$cr;$ii++)
        	{
        		unset($row['click_path'][$ii]);
        	}
        }

        foreach ($row['click_path'] as $k=>$v)
        {
            $row['click_path'][$k] = '<nobr><b>'.$c++.':</b> '.$v.'</nobr>';
        }
        $msg = implode('<br>', $row['click_path']);
        if ($cr > $restr)
        {
        	$msg .= "<br>...";
        }
        $p = $row['visit_depth'] > 1 ? getMsg('RPTS','PAGES') : getMsg('RPTS','PAGE');
        $hint = '<span onmouseover="return overlib(\''.$msg.'\');" onmouseout="return nd();">';
        $row['visit_depth'] = $this->__colorizeValue(getMsg('RPTS','GO').' '.$row['visit_depth'].' '.$p, $row['online_status']);
        $href = 'javascript:void(0)';
        $onclick = "javascript:openURLinNewWindow('reports_click_path.php?type=robot&id=".$row['visitor_id']."', 'ReportsClickPath');";
        $row['depth_of_visit'] = $hint.'<A onclick="'.$onclick.'" href="'.$href.'"  style="text-decoration: none;">'.$row['visit_depth'].'</A></span>';

        # REMOTE_IP && REMOTE_ADDR
        $row['remote_ip'] = $this->__colorizeValue($row['remote_ip'], $row['online_status']);
        $row['remote_addr'] = $this->__colorizeValue($row['remote_addr'], $row['online_status']);

        #CRAWLER_ID
        $row['name'] = $this->__colorizeValue($row['name'], $row['online_status']);

        # VISITOR_OS_BROWSER
        /*$row['visitor_os_browser'] = $row['visitor_browser'];
        $row['visitor_os_browser'] = $this->__colorizeValue($row['visitor_os_browser'], $row['online_status']);

        # ONLINE_FLAG
        if ($row['online_status'] == true)
        {
            $row['online_flag'] = getMsg('RPTS','VISITOR_STATUS_ONLINE');
        }
        else
        {
            $row['online_flag'] = getMsg('RPTS','VISITOR_STATUS_OFFLINE');
        }
        $row['online_flag'] = $this->__colorizeValue($row['online_flag'], $row['online_status']);*/

        return $row;
    }

    function __colorizeValue($value, $flag, $style="font-weight: normal;")
    {
        if ($flag === true)
        {
            return '<span style="'.$style.' color: #45B6AF;">'.$value.'</span>';
        }
        else
        {
            return $value;
        }
    }

}

class ReportRecentVisitorStatisticsShort extends ReportRecentVisitorStatistics
{
    function ReportRecentVisitorStatisticsShort()
    {
        parent::ReportRecentVisitorStatistics();
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_LAST_100_VISITORS_SHORT');
    }

    function initSource()
    {
        parent::initSource();
        $this->__source->setParams('limits_number', 5);
    }

    function isExportToExcelApplicable()
    {
        return false;
    }

    function getFooter()
    {
        $html = '&nbsp;&nbsp;<A style="color: black;" href="reports.php?report_group_id=200">'.getMsg('RPTS','RECENT_100_VISITORS_LINK').'</A>';
        return $html;
    }

    function setColumns()
    {
        $this->__render_settings->setColumnHeaders( array(
                                                    'reverse_number' => getMsg('RPTS','NUMBER'),
                                                    'seance_time' => getMsg('RPTS','TIMESTAMP'),
                                                    'repeat_visitor' => getMsg('RPTS','REPEAT_VISITOR'),
                                                    'referer' => getMsg('RPTS','REFERER'),
                                                    'entry_page' => getMsg('RPTS','ENTRY_PAGE'),
                                                    'depth_of_visit' => getMsg('RPTS','CLICK_PATH'),
                                                    'visitor_ip' => getMsg('RPTS','REMOTE_ADDR'),
                                                    'visitor_os_browser' => getMsg('RPTS','BROWSER_OS'),
                                                    'visit_number' => getMsg('RPTS','VISIT_NUMBER'),
                                                    'previous_visit_date' => getMsg('RPTS','PREV_VISIT_DATE'),
                                                    'remote_ip' => getMsg('RPTS','VISITOR_IP'),
                                                    'remote_addr' => getMsg('RPTS','VISITOR_SERVER_NAME'),
                                                    'visitor_os' => getMsg('RPTS','VISITOR_OS'),
                                                    'visitor_browser' => getMsg('RPTS','VISITOR_BROWSER'),
                                                    'online_flag' => getMsg('RPTS','VISITOR_STATUS'),
        ));

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__render_settings->setColumnList( array(
                                                            'seance_time',
                                                            'repeat_visitor',
                                                            'visitor_ip',
                                                            'online_flag',
                ));
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'reverse_number',
                                                            'seance_time',
                                                            'repeat_visitor',
                                                            'visit_number',
                                                            'previous_visit_date',
                                                            'depth_of_visit',
                                                            'referer',
                                                            'entry_page',
                                                            'remote_ip',
                                                            'remote_addr',
                                                            'visitor_os',
                                                            'visitor_browser',
                ));
                break;
        }
    }
}

?>