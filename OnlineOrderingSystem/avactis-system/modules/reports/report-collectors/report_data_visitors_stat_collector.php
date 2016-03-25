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
loadModuleFile('reports/abstract/report_data_collector.php');

/**
 * CVisitorStatisticCollector class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CVisitorStatisticCollector extends CReportDataCollector
{
    function CVisitorStatisticCollector()
    {
        $this->__max_minutes_last_visit = (int)modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','VISITOR_SESSION_DURATION');
    }

    function getTimestamp()
    {
    	$store_time = new CStoreDatetime();
	    $vd = $store_time->getTimestamp();
	    return $vd;
    }

    function createVisitorInfo($session_id, $os, $browser, $agent, $type)
    {
        $params = array(
            'visitor_session_id' => $session_id,
            'visitor_os' => $os,
            'visitor_browser' => $browser,
            'visitor_register_time' => date('Y-m-d H:i:s', $this->getTimestamp()),
            'visitor_agent' => $agent,
            'visitor_type' => $type
        );
        execQuery('INSERT_VISITOR_INFO', $params);
        global $application;
        return $application->db->DB_Insert_Id();
    }

    function getVisitorIdBySessionId($session_id)
    {
        $params = array(
            'visitor_session_id' => $session_id
        );
        $res = execQuery('SELECT_VISITOR_ID_BY_SESSION_ID', $params);
        if (!empty($res) and isset($res[0]) and isset($res[0]['visitor_id']) and !empty($res[0]['visitor_id']))
        {
            return $res[0]['visitor_id'];
        }
        else
        {
            return null;
        }
    }

    function createSeance($visitor_id, $referer_host, $referer, $remote_ip, $remote_host, $entry_page, $prev_seance_id, $visit_number)
    {
        $seance_id = $this->__getSeanceId();
        $params = array(
            'visitor_id' => $visitor_id,
            'seance_id' => $seance_id,
            'prev_seance_id' => $prev_seance_id,
            'visit_number' => $visit_number,
            'creation_time' => date('Y-m-d H:i:s', $this->getTimestamp()),
            'referer' => $referer,
            'referer_host' => $referer_host,
            'remote_ip' => $remote_ip,
            'remote_host' => $remote_host,
            'entry_page' => $entry_page,
        );
        execQuery('INSERT_VISITOR_SEANCE', $params);
        return $seance_id;
    }

    function __getSeanceId()
    {
        $f = true;
        $seance_id = mt_rand(0, 999999999);
        while($f)
        {
            $res = execQuery('SELECT_SEANCE_BY_ID', array('seance_id'=>$seance_id));
            if (!empty($res) and isset($res[0]) and isset($res[0]['seance_id']))
            {
                $seance_id = mt_rand(0, 999999999);
            }
            else
            {
                $f = false;
            }
        }
        return $seance_id;
    }

    function findCurrentSeanceId($visitor_id)
    {
        $params = array(
            'visitor_id' => $visitor_id,
            'time_period' => $this->__max_minutes_last_visit,
        );

        $res = execQuery('SELECT_CURRENT_SEANCE_LAST_PAGE_BY_VISITOR_ID', $params);
        if (is_array($res) && !empty($res))
            return array($res[0]['seance_id'], $res[0]['page_url']);

        return null;
    }

    function findLastSeanceId($visitor_id)
    {
        $params = array(
            'visitor_id' => $visitor_id,
        );

        $res = execQuery('SELECT_LAST_SEANCE_ID_BY_VISITOR_ID', $params);
        if (is_array($res) && !empty($res))
            return $res[0]['seance_id'];

        return null;
    }

    function addSeanceInfo($seance_id, $page_url)
    {
        $page_id = modApiFunc('Reports', 'getPageURLID', $page_url);
        $params = array(
            'seance_id' => $seance_id,
            'visit_time' => date('Y-m-d H:i:s', $this->getTimestamp()),
            'page_url' => $page_id,
            'visitors_online' => getStatisticsVisitorsOnlineRaw() // Note: statistics for online visitors includes only human visits
        );
        execQuery('INSERT_VISITOR_SEANCE_INFO', $params);
    }

    function setCrawlerVisit($id, $agent, $name, $type, $ip, $host, $referrer, $entry_page, $scanned_pages)
    {
        modApiFunc("Session","set","SupportMode","ASC_S_STATISTICS"); //turn off statistics collection

    	$params = array(
            'visitor_id' => $id,
            'visit_time' => date('Y-m-d H:i:s', $this->getTimestamp()),
            'agent_string' => $agent,
            'name' => $name,
            'type' => $type,
            'ip' => $ip,
            'host' => $host,
            'referrer' => $referrer,
            'entry_page' => $entry_page,
            'scanned_pages' => addslashes($scanned_pages),
        );

        execQuery('REPLACE_CRAWLER_VISIT', $params);
    }

    function __getCurrentURL()
    {
        return getCurrentURL();
    }

    function __getReferer()
    {
        $referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        $current_url = $this->__getCurrentURL();
        $parsed_referer = @parse_url($referer);
        $parsed_current_url = @parse_url($current_url);

        if (    isset($parsed_referer['host']) and
                !empty($parsed_referer['host']) and
                isset($parsed_current_url['host']) and
                $parsed_current_url['host'] !== $parsed_referer['host']
           )
        {
            return array($parsed_referer['host'], $referer);
        }
        else
        {
            return array('','');
        }
    }

    function __getUniqueVisitorMarker()
    {
        $name = 'avactis_visitor_id';

        if (isset($_COOKIE) and isset($_COOKIE[$name]))

        {
            $token = $_COOKIE[$name];
        }
        else
        {
            $token = md5(uniqid(rand(), true));
        }
        $value = $token;
        $path = '/';
        $domain = '';
        $expire = time() + 3600*24*90;
        setcookie($name, $value, $expire, $path);// $domain);

        return $token;
    }

    function __getRemoteHost($ip)
    {
        return getRemoteHostByIP($ip);
    }

    function __getRemoteIP()
    {
        return getVisitorIP();
    }

    /*
     * This fucntions tries to match given user agent with web crawlers database.
     * @param string $agent user-agent
     * @rerutn string "R" or "C". Note: C - customer browser, R - web robot (bot, crawler)
     */
    function __getVisitorTypeByAgent($agent)
    {
        if (empty($agent))
            return array("type" => "C");

        $params = array ('user-agent' => addslashes($agent));
    	$r = execQuery("SELECT_WEB_ROBOT_ID", $params);

    	if ($r == null)
    	{
    		#
    		# Check by keywords list
    		#
    		# keywords that identify crawler
    		$well_known_crawlers = array(
    		    "GoogleBot",
    		    "GoogleBot-image",
    		    "Slurp",
    		    "Baidu",
    		    "MSN",
    		    "MS Search",
    		    "Amazon",
    		    "heritrix",
    		    "Spoch"
    		);
    		$crawler_keywords = array(
    		    "googlebot",
    		    "slurp",
    		    "msn",
    		    "amazon",
    		    "heritrix",
    		    "spoch",
    		    "ms search",
    		    "bot",
    		    "baidu",
    		    "wiki",
    		    "robot",
    		    "spider",
    		    "search",
    		    "find",
    		    "scanner",
    		    "larbin",
    		    "archive"
    		);
    		#keywords that identify human
    		$human_keywords = array(
    		);

    		$p_matches = array();
    		$n_matches = array();
    		$well_known = array();
    		$cwr_name = "";
    		$cm = 0;
    		$hm = 0;
    		$wkm = 0;

    		if (count($crawler_keywords) > 0)
    		{
    		    $pattern = implode("|", $crawler_keywords);

    		    $cm = preg_match_all("/".$pattern."/i", $agent, $p_matches);

    		    $wkm = preg_match("/".implode("|",$well_known_crawlers)."/i",$agent, $well_known);

    		}

    		if (count($human_keywords) > 0)
    		{
    		    $pattern = implode("|", $human_keywords);

    		    $hm = preg_match_all("/".$pattern."/i", $agent, $n_matches);
    		}

    		if ($cm > 0 && $hm == 0 ) // if crawler keywords are present and human keywords are not present
    		{
    			# some unknown web crawler
    			if ($wkm != 0)
    			{
    				$cwr_name = $well_known[0];
    			}
    			else
    			{
    			    $cwr_name = "Unknown Web crawler";
    			}
    			# should add new record to database
    			$params = array('user_agent' => $agent, 'name'=> $cwr_name, 'type' => "R");
    			$r = execQuery('INSERT_CRAWLER_RECORD',$params);
    			return array("type" => "R", "crawler_name" => $cwr_name);
    		}
    		else
    		{
    		    return array("type" => "C"); //customers' browser, so it is a men
    		}
    	}
    	else
    	{
    		return array("type" => $r[0]['type'], "crawler_name" => $r[0]['name']); //web crawler. eg. Google or Yahoo!
    	}
    }

    function onApplicationStarted()
    {
        if ($this->isStatisticsEnable() == false)
        {
            return;
        }

        global $zone;
        if($zone == 'CustomerZone')
        {
            loadCoreFile('phpSniff.class.php');
            $client = new phpSniff();

            $session_id = $this->__getUniqueVisitorMarker();
            $os = trim($client->property('platform').' '.$client->property('os'));
            $browser = trim($client->property('long_name').' '.$client->property('version'));
            $agent = @$_SERVER['HTTP_USER_AGENT']; // user-agent param can be empty. So that is why @ silencer sign is added
            $r= $this->__getVisitorTypeByAgent($agent); //C - customer browser; R - robot; ...

            list($referer_host, $referer) = $this->__getReferer();
            $remote_ip = $this->__getRemoteIP();
            $remote_host = $this->__getRemoteHost($remote_ip);
            $page_url = $this->__getCurrentURL();

            if ($r['type'] != "C") # crawler visit
            {
                $browser = $r['crawler_name'];
                $type = $r['type'];

                //$visit_time = date('Y-m-d H:i:s', $this->getTimestamp());
                $pages = array();

                # check if this bot crawled store before today
                $from = date("Y-m-d H:i:s", mktime(0,0,0,date("m"), date("d"), date("Y")));
                $to = date("Y-m-d H:i:s", mktime(23,23,59,date("m"), date("d"), date("Y")));
                $params = array ('from' => $from, 'to' => $to, 'name' =>$r['crawler_name'], 'visitor_id' => null, 'limits' => null);
                $cvisits = execQuery('SELECT_CRAWLER_VISITS', $params);

                if (count($cvisits) != 0) # replace record
                {
                	$pages = unserialize(stripslashes($cvisits[0]['scanned_pages']));
                	$pages[] = array("visit_time" => $this->getTimestamp(),"url" => $page_url);
                	$scanned_pages = serialize($pages);
                	$this->setCrawlerVisit($cvisits[0]['visitor_id'], $agent, $browser, $type, $remote_ip, $remote_host, $referer, $pages[0]['url'], $scanned_pages);
                }
                else # new record
                {
                	$pages[] = array("visit_time" => $this->getTimestamp(), "url" => $page_url);
                	$scanned_pages = serialize($pages);
                	$this->setCrawlerVisit(null, $agent, $browser, $type, $remote_ip, $remote_host, $referer, $page_url, $scanned_pages);
                }
            }
            else # human visitor
            {
                $type = "C";

                //      visitor_id             session_id
                $visitor_id = $this->getVisitorIdBySessionId($session_id);
                if ($visitor_id == null)
                {

                    $visitor_id = $this->createVisitorInfo($session_id, $os, $browser, $agent, $type);
                }

                //
                $seance_data = $this->findCurrentSeanceId($visitor_id);
                if ($seance_data == null)
                {
                    //                        ,
                    $last_seance_id = $this->findLastSeanceId($visitor_id);
                    if ($last_seance_id === null)
                    {
                        //
                        $prev_seance_id = 0; //      -                   .
                        $visit_number = 1;
                        $seance_id = $this->createSeance($visitor_id, $referer_host, $referer, $remote_ip, $remote_host, $page_url, $prev_seance_id, $visit_number);
                    }
                    else
                    {
                        $prev_seance_id = $last_seance_id;
                        $seance_info = execQuery('SELECT_SEANCE_BY_ID', array('seance_id'=>$prev_seance_id));
                        if (!empty($seance_info) && isset($seance_info[0]) && isset($seance_info[0]['visit_number']))
                        {
                            $visit_number = $seance_info[0]['visit_number']+1;
                        }
                        else
                        {
                            $visit_number = 1;
                        }
                        $seance_id = $this->createSeance($visitor_id, $referer_host, $referer, $remote_ip, $remote_host, $page_url, $prev_seance_id, $visit_number);
                    }
                    //
                    $this->addSeanceInfo($seance_id, $page_url);
                }
                else
                {
                    //
                    list($seance_id, $last_page_url) = $seance_data;
                    if ($last_page_url !== $page_url)
                    {
                        //
                        $this->addSeanceInfo($seance_id, $page_url);
                    }
                }
            }
        }
    }

}


?>