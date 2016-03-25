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
loadModuleFile('reports/reports_api.php');
loadModuleFile('reports/abstract/report_data_source.php');

/**
 * CVisitorBrowserStatistics
 *
 *                                                              :
 * - browser_name
 * - number
 * - percent
 *
 *            -
 *
 */
class CVisitorBrowserStatistics extends CReportDataSource
{
    function CVisitorBrowserStatistics()
    {
    }

    function run()
    {
        $data = execQuery('SELECT_BROWSERS_STAT', array());
        $this->__data = $this->__computePercentField($data, 'number', 'percent');
    }
}

/**
 * CVisitorOSStatistics
 *
 *                                                                      :
 * - os_name
 * - number
 * - percent
 *
 *            -
 *
 */
class CVisitorOSStatistics extends CReportDataSource
{
    function CVisitorOSStatistics()
    {
    }

    function run()
    {
        $data = execQuery('SELECT_OS_STAT', array());
        $this->__data = $this->__computePercentField($data, 'number', 'percent');
    }
}

/**
 * CVisitorTopRefererStatistics
 *
 *                                                                                   :
 * - referer (                        URL              ,            $this->__params['select_full_referer_url'])
 * - visit_number
 * - percent
 *
 *   -         ,                    20-
 *
 *            -
 *
 */
class CVisitorTopRefererStatistics extends CReportDataSource
{
    function CVisitorTopRefererStatistics()
    {
        $this->__params = array();
        $this->__params['from'] = null;
        $this->__params['to'] = null;
        $this->__params['limits'] = array(0,20);
        $this->__params['select_full_referer_url'] = false;
    }

    function setDatetimePeriod($from, $to)
    {
        $this->__params['to'] = $to;
        $this->__params['from'] = $from;
    }

    function run()
    {
        $data = execQuery('SELECT_REFERER_HOSTS_BY_TIME_PERIOD', $this->__params);
        $this->__data = $this->__computePercentField($data, 'visit_number', 'percent');
    }

    var $__params = null;
}

class CVisitorTopViewedPageStatistics extends CReportDataSource
{
    function CVisitorTopViewedPageStatistics()
    {
        $this->__params = array();
        $this->__params['from'] = null;
        $this->__params['to'] = null;
        $this->__params['limits'] = array(0,20);
    }

    function setDatetimePeriod($from, $to)
    {
        $this->__params['to'] = $to;
        $this->__params['from'] = $from;
    }

    function run()
    {
        $data = execQuery('SELECT_TOP_VIEWED_PAGE_BY_TIME_PERIOD', $this->__params);
        // getting the actual page urls...
        $p_ids = array();
        if (is_array($data))
        {
            foreach($data as $v)
                $p_ids[] = $v['page_url'];

            $pages = modApiFunc('Reports', 'getPagesByIDs', $p_ids);

            foreach($data as $k => $v)
                $data[$k]['page_url'] = @$pages[$v['page_url']];
        }

        $this->__data = $this->__computePercentField($data, 'view_number', 'percent');
    }

    var $__params = null;
}

/**
 * CVisitorStatisticsByDatetimePeriod abstract class
 *
 *                     -                                                    .
 *                                ( . .               )                 :
 * - seance_number
 * - page_number
 * - page_views_per_seance
 * - visitor_number
 * - first_time_visitor_number
 * - repeat_visitor_number
 *
 *            -                          .
 *
 */
class CVisitorStatisticsByDatetimePeriod extends CReportDataSource
{
    function CVisitorStatisticsByDatetimePeriod($discontinuity)
    {
        $this->__params = array();
        $this->__params['discontinuity'] = $discontinuity;
        $this->__params['to'] = null;
        $this->__params['from'] = null;
    }

    function setDatetimePeriod($from, $to)
    {
        $this->__params['to'] = $to;
        $this->__params['from'] = $from;
    }

    function run()
    {
        //          (                                                        )
        $__keys = array('datetime_year', 'datetime_month', 'datetime_day');

        //
        $to_merge = array(
            execQuery('SELECT_VISITOR_NUMBER_BY_DATETIME_PERIOD', $this->__params),
            execQuery('SELECT_FIRST_TIME_VISITOR_NUMBER_BY_DATETIME_PERIOD', $this->__params),
            execQuery('SELECT_SEANCE_STATISTICS_BY_DATETIME_PERIOD', $this->__params)
        );

        //                                     (                              $to_merge)
        $empty_items = array(
            array('visitor_number'=>0),
            array('first_time_visitor_number'=>0),
            array('seance_number'=>0, 'page_number'=>0, 'page_views_per_seance'=>0),
        );

        $this->__data = $this->__margeArrays($to_merge, $empty_items, $__keys);

        foreach ($this->__data as $key=>$value)
        {
            //
            $this->__data[$key]['repeat_visitor_number'] = $this->__data[$key]['visitor_number'] - $this->__data[$key]['first_time_visitor_number'];
        }

        $zero_item = array(
            'seance_number'=>0,
            'page_number'=>0,
            'page_views_per_seance'=>0,
            'visitor_number' => 0,
            'first_time_visitor_number' => 0,
            'repeat_visitor_number' => 0
        );
        $this->__data = $this->__addZeroItems($this->__data, $zero_item);
    }


    var $__params = null;
}

class CVisitorStatisticsByDays extends CVisitorStatisticsByDatetimePeriod
{
    function CVisitorStatisticsByDays()
    {
        parent::CVisitorStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_DAY);
    }
}

class CVisitorStatisticsByMonths extends CVisitorStatisticsByDatetimePeriod
{
    function CVisitorStatisticsByMonths()
    {
        parent::CVisitorStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_MONTH);
    }
}

class CVisitorStatisticsByYears extends CVisitorStatisticsByDatetimePeriod
{
    function CVisitorStatisticsByYears()
    {
        parent::CVisitorStatisticsByDatetimePeriod(DATETIME_PERIOD_DISCONTINUITY_YEAR);
    }
}

/**
 * CVisitorRecentVisitorStatistics data source
 *
 *                                                      (100)            :
 * - reverse_number (                                          )
 * - visitor_os
 * - visitor_browser
 * - visitor_id
 * - seance_id
 * - remote_ip
 * - remote_host
 * - referer
 * - referer_host
 * - creation_time (                      )
 * - entry_page
 * - visit_number (                        )
 * - visit_previous_time
 * - visit_first_time
 * - visit_depth
 * - click_path (                                        )
 * - online_status (true     false)
 *
 */
class CTopTenCustomerStatistics extends CReportDataSource
{
    var $session_duration = 0;

    function CTopTenCustomerStatistics()
    {
        parent::CReportDataSource();
        $this->session_duration = (int)modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','VISITOR_SESSION_DURATION');
        $this->__params['limits_offset'] = 0;
        $this->__params['limits_number'] = 10;
    }

    function run()
    {
        $this->seance_info_list = execQuery('SELECT_SEANCE_INFO_RECORDS', array('limits'=>array($this->__params['limits_offset'],$this->__params['limits_number']), "visitor_type" => "C"));
        if (empty($this->seance_info_list))
        {
            return;
        }

        $seance_ids = $this->__getSeanceIdList($this->seance_info_list);
        $this->click_path_by_seance_id_list = execQuery('SELECT_CLICK_PATH_BY_SEANCE_IDs', array('seance_ids'=>$seance_ids));

        $reverse_number = count($this->seance_info_list);
        foreach ($this->seance_info_list as $key=>$info)
        {
            $this->seance_info_list[$key]['reverse_number'] = $reverse_number--;

            $visit_previous_time = $this->__getPrevVisitTime($info['prev_seance_id']);
            if ($visit_previous_time === null)
            {
                $this->seance_info_list[$key]['visit_previous_time'] = 0;
            }
            else
            {
                $this->seance_info_list[$key]['visit_previous_time'] = $visit_previous_time;
            }

            list($depth, $path) = $this->__getClickPathInfo($info['seance_id']);
            $this->seance_info_list[$key]['visit_depth'] = $depth;
            $this->seance_info_list[$key]['click_path'] = $path;

            $this->seance_info_list[$key]['online_status'] = $this->__getOnlineStatus($info['seance_id']);
        }
        $this->__data = $this->seance_info_list;
    }

    function __getPrevVisitTime($prev_seance_id)
    {
        if ($prev_seance_id == 0)
        {
            return null;
        }

        //
        foreach ($this->seance_info_list as $item)
        {
            if ($item['seance_id'] == $prev_seance_id)
            {
                return $item['creation_time'];
            }
        }

        //              ,                  .
        //                  ,       prev_seance_id                                 .
        $params = array(
            'seance_id' => $prev_seance_id,
        );
        $res = execQuery('SELECT_SEANCE_BY_ID', $params);
        if (!empty($res) && isset($res[0]) && isset($res[0]['creation_time']))
        {
            return $res[0]['creation_time'];
        }
        return null;
    }

    function __getOnlineStatus($seance_id)
    {
        $last_time = null;
        foreach ($this->click_path_by_seance_id_list as $key=>$info)
        {
            if ($info['seance_id'] == $seance_id)
            {
                $last_time = $info['visit_time'];
            }
        }
        if ($last_time == null)
        {
            return false;
        }
        else
        {
            $t = strtotime($last_time) + $this->session_duration * 60 -
                 Configuration::getValue(SYSCONFIG_STORE_TIME_SHIFT) * 3600;
            if ($t >= time())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    function __getClickPathInfo($seance_id)
    {
        $depth = 0;
        $path = array();
        foreach ($this->click_path_by_seance_id_list as $key=>$info)
        {
            if ($info['seance_id'] == $seance_id)
            {
                $depth++;
                $path[] = $info['page_url'];
            }
        }
        return array($depth, $path);
    }

    function __getSeanceIdList($seance_info_list)
    {
        $seance_id_list = array();
        foreach ($seance_info_list as $info)
        {
            if (isset($info['seance_id']))
            {
                $seance_id_list[] = $info['seance_id'];
            }
        }
        return $seance_id_list;
    }

    var $click_path_by_seance_id_list;
    var $seance_info_list;
}

class CVisitorRecentCrawlerStatistics extends CReportDataSource
{
    var $session_duration = 0;

    function CVisitorRecentCrawlerStatistics()
    {
        parent::CReportDataSource();
        $this->session_duration = (int)modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','VISITOR_SESSION_DURATION');
        $this->__params['limits_offset'] = 0;
        $this->__params['limits_number'] = 100;
    }

    function run()
    {
    	$this->crawlers = execQuery('SELECT_CRAWLER_VISITS', array('from' => null, 'to' => null,'name' => null, 'visitor_id' => null, 'limits'=>array($this->__params['limits_offset'],$this->__params['limits_number'])));

    	foreach ($this->crawlers as $i => $c)
    	{
    	    $scanned_pages = unserialize(stripslashes($c['scanned_pages']));
    	    list($depth, $path) = $this->__getClickPathInfo($scanned_pages);
    		$this->crawlers[$i]['click_path'] = $path;
    		$this->crawlers[$i]['visit_depth'] = $depth;
    		$this->crawlers[$i]['reverse_number'] = $i+1;
    		$this->crawlers[$i]['online_status'] = $this->__getOnlineStatus($scanned_pages[count($scanned_pages)-1]['visit_time']);
    	    unset($this->crawlers[$i]['scanned_pages']);
    	}

    	$this->__data = $this->crawlers;
    }

    function __getPrevVisitTime($prev_seance_id)
    {
        if ($prev_seance_id == 0)
        {
            return null;
        }

        //
        foreach ($this->seance_info_list as $item)
        {
            if ($item['seance_id'] == $prev_seance_id)
            {
                return $item['creation_time'];
            }
        }

        //              ,                  .
        //                  ,       prev_seance_id                                 .
        $params = array(
            'seance_id' => $prev_seance_id,
        );
        $res = execQuery('SELECT_SEANCE_BY_ID', $params);
        if (!empty($res) && isset($res[0]) && isset($res[0]['creation_time']))
        {
            return $res[0]['creation_time'];
        }
        return null;
    }

    function __getOnlineStatus($visit_time)
    {

        if ($visit_time == null)
        {
            return false;
        }
        else
        {
            $t = $visit_time + $this->session_duration*60;
            if ($t >= time())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    function __getClickPathInfo($scanned_pages)
    {
        $depth = 0;
        $path = array();
        foreach ($scanned_pages as $i=>$p)
        {
            $depth++;
            $path[] = $p['url'];
        }
        return array($depth, $path);
    }

    var $crawler_path;
    var $crawlers;
}

/**
 *                    setParams($key, $value)                 seance_id                            .
 *                           .
 *
 *       :
 * $source->setParams('seance_ids', array(seance_id_1, seance_id_2, ...));
 *
 *                    seance_id                                             :
 * - seance_id
 * - page_url
 * - visit_time
 *
 */
class CSeanceClickPathBySeanceId extends CReportDataSource
{
    function CSeanceClickPathBySeanceId()
    {
    	parent::CReportDataSource();
        $this->__params['seance_ids'] = array();
    }

    function run()
    {
        if (!is_array($this->__params['seance_ids']) or empty($this->__params['seance_ids']))
        {
            $this->__data  = array();
        }
        else
        {
            $this->__data = execQuery('SELECT_CLICK_PATH_BY_SEANCE_IDs', array('seance_ids'=>$this->__params['seance_ids']));
        }
    }

}

class CScannedPagesByCrawler extends CReportDataSource
{
    function CScannedPagesByCrawler()
    {
    	parent::CReportDataSource();
    }

    function run()
    {
    	if (empty($this->__params['id']))
        {
            $this->__data  = array();
        }
        else
        {
            #$d = $this->__params['date'];

        	$data = execQuery('SELECT_CRAWLER_VISITS', array('name'=>null, 'visitor_id' => $this->__params['id'], 'from'=>null, 'to' => null, 'limits' => null));
            $pages = unserialize(stripslashes($data[0]['scanned_pages']));
            foreach ($pages as $i => $p) #rename fileds
            {
            	$pages[$i]['page_url'] = $pages[$i]['url'];
            	$pages[$i]['visit_time'] = date("Y-m-d H:i:s", $pages[$i]['visit_time']);
            	unset($pages[$i]['url']);
            }
            $this->__data = $pages;
        }
    }

}

?>