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



class StatisticsSalesTotalToday
{
    function output()
    {
        loadClass('COrderTotalStatisticsByDays');
        $source = new COrderTotalStatisticsByDays();

        $period = modApiFunc('Reports', 'getTimestampPeriodByDatetimeLabel', DATETIME_PERIOD_DAY_THIS);
        if ($period !== null)
        {
            list($from, $to) = $period;
            $source->setDatetimePeriod(toMySQLDatetime($from), toMySQLDatetime($to));
        }

        $source->run();
        $row = $source->fetchRecord();

        if (isset($row['order_total_sum']))
        {
            return modApiFunc("Localization", "currency_format", round($row['order_total_sum'],2));
        }
        else
        {
            return '';
        }
    }
}

class StatisticsOrdersNumberToday
{
    function output()
    {
        loadClass('COrderTotalStatisticsByDays');
        $source = new COrderTotalStatisticsByDays();

        $period = modApiFunc('Reports', 'getTimestampPeriodByDatetimeLabel', DATETIME_PERIOD_DAY_THIS);
        if ($period !== null)
        {
            list($from, $to) = $period;
            $source->setDatetimePeriod(toMySQLDatetime($from), toMySQLDatetime($to));
        }

        $source->run();
        $row = $source->fetchRecord();

        if (isset($row['order_qty']))
        {
            return modApiFunc("Localization", "num_format", $row['order_qty']);
        }
        else
        {
            return '';
        }
    }
}



/*abstract*/class StatisticsTag
{
    function output($const_period, $sql_class_name, $field_name)
    {
        $params = array(
            'from' => null,
            'to' => null,
        );

        if ($const_period !== null)
        {
            $period = modApiFunc('Reports', 'getTimestampPeriodByDatetimeLabel', $const_period);
            if ($period !== null)
            {
                list($from, $to) = $period;
                $params['from'] = toMySQLDatetime($from);
                $params['to'] = toMySQLDatetime($to);
            }
        }

        $res = execQuery($sql_class_name, $params);
        if (!empty($res) and is_array($res) and isset($res[0]) and isset($res[0][$field_name]))
        {
            return modApiFunc("Localization", "num_format", $res[0][$field_name]);
        }
        else
        {
            return '';
        }
    }
}



///////////////////////////////////////////////////////////////////////////////
//
//                      STATISTICS PAGE VIEWS
//
///////////////////////////////////////////////////////////////////////////////


class StatisticsPageViewsTotal extends StatisticsTag
{
    function output()
    {
        return parent::output(null, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsThisDay extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_DAY_THIS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsLastDay extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_DAY_PREVIOUS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsThisWeek extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_WEEK_THIS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsLastWeek extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_WEEK_PREVIOUS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsThisMonth extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_MONTH_THIS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsLastMonth extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_MONTH_PREVIOUS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsThisYear extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_YEAR_THIS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsPageViewsLastYear extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_YEAR_PREVIOUS, 'SELECT_PAGE_VIEWS_TOTAL_NUMBER', 'number');
    }
}




///////////////////////////////////////////////////////////////////////////////
//
//                      STATISTICS VISITS (visitor seances)
//
///////////////////////////////////////////////////////////////////////////////

class StatisticsVisitsTotal extends StatisticsTag
{
    function output()
    {
        return parent::output(null, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsThisDay extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_DAY_THIS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsLastDay extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_DAY_PREVIOUS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsThisWeek extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_WEEK_THIS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsLastWeek extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_WEEK_PREVIOUS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsThisMonth extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_MONTH_THIS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsLastMonth extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_MONTH_PREVIOUS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsThisYear extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_YEAR_THIS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitsLastYear extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_YEAR_PREVIOUS, 'SELECT_VISITOR_SEANCES_TOTAL_NUMBER', 'number');
    }
}

///////////////////////////////////////////////////////////////////////////////
//
//                      STATISTICS UNIQUE VISITORS
//
///////////////////////////////////////////////////////////////////////////////

class StatisticsVisitorsTotal extends StatisticsTag
{
    function output()
    {
        return parent::output(null, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsThisDay extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_DAY_THIS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsLastDay extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_DAY_PREVIOUS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsThisWeek extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_WEEK_THIS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsLastWeek extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_WEEK_PREVIOUS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsThisMonth extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_MONTH_THIS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsLastMonth extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_MONTH_PREVIOUS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsThisYear extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_YEAR_THIS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsLastYear extends StatisticsTag
{
    function output()
    {
        return parent::output(DATETIME_PERIOD_YEAR_PREVIOUS, 'SELECT_UNIQUE_VISITORS_TOTAL_NUMBER', 'number');
    }
}

class StatisticsVisitorsOnlineRaw
{

	function output()
	{
	    $visit_duration = (int)modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','VISITOR_SESSION_DURATION');
	    $params['from'] = null;
	    $params['to'] = null;

	    #calculation of visit deadline, so it is the maximum date since last visit
	    $store_time = new CStoreDatetime();
	    $vd = $store_time->getTimestamp()-($visit_duration*60);
	    $params['visit_deadline'] = date("Y-m-d H:i:s", $vd);

		$res = execQuery("SELECT_VISITORS_ONLINE", $params);
		if (count($res) != 0)
		{
		    return count($res);
		}
		else
		{
			return 0;
		}
	}
}

class StatisticsVisitorsOnline
{
	function output()
	{
	    $v = getStatisticsVisitorsOnlineRaw();
		if ($v != null)
		{
			return modApiFunc("Localization", "num_format", $v);
		}
		else
		{
			return '0';
		}
	}
}

class StatisticsVisitorsOnlineYesterday
{
	function output()
	{
	    $from_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y"))); // the beginning of yesterday
	    $to_date = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") , date("d") - 1, date("Y"))); // the end of yesterday

	    $params['from'] = $from_date;
	    $params['to'] = $to_date;

	    $res = execQuery("SELECT_ONLINE_VISITORS_BY_PERIOD", $params);
		if ($res[0]['visitors'] != 0)
		{
		    return modApiFunc("Localization", "num_format", $res[0]['visitors']);
		}
		else
		{
			return '0';
		}
	}
}

/*
 * Maximum unique visitors online simultaneously for all time
 */
class StatisticsMaxVisitorsOnline
{
	function output()
	{
	    $params = array('to' => null, 'from' => null);
		$r = execQuery("SELECT_ONLINE_VISITORS_BY_PERIOD", $params);

		return modApiFunc("Localization", "num_format", $r[0]['visitors']);
	}
}

class StatisticsMaxUniqueVisitors
{
    function output()
	{
	    $params['visit_deadline'] = null;

	    $store_time = new CStoreDatetime();
	    $now = $store_time->getTimestamp();

	    # get the earliet visit time
	    $r = execQuery("SELECT_EARLIEST_VISIT_TIME", $params);
	    $fv = $r[0]['first_visit'];
	    if ($fv == null)
	    {
	        return '0';
	    }

	    preg_match("/([0-9]*)-([0-9]*)-([0-9]*) ([0-9]*):([0-9]*):([0-9]*)/",$fv, $m);
	    $earliest_visit = mktime($m[4],$m[5],$m[6],$m[2], $m[3], $m[1]);

	    $mv = 0;

	    while ($earliest_visit <= $now)
	    {
		    $params['from'] = date("Y-m-d H:i:s", mktime(0, 0, 0,    date("m",$earliest_visit) , date("d",$earliest_visit), date("Y",$earliest_visit)));
		    $params['to']   = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m",$earliest_visit) , date("d",$earliest_visit), date("Y",$earliest_visit)));
	    	$res = execQuery("SELECT_VISITORS_STATISTICS_BY_PERIOD", $params);
	    	$c = count($res);
	    	if ($c > $mv)
	    	{
	    	    $mv = $c;
	    	}

		    $earliest_visit += (3600*24); //next day
	    }

		if ($mv != 0)
		{
		    return modApiFunc("Localization", "num_format", $mv);
		}
		else
		{
			return '0';
		}
	}
}

class StatisticsAverageVisitorsPerDay
{
	function output()
	{
	    $params['from'] = null;
		$params['to']   = null;
		$params['visit_deadline'] = null;

		$store_time = new CStoreDatetime();
	    $now = $store_time->getTimestamp();

	    # get the earliet visit time
	    $r = execQuery("SELECT_EARLIEST_VISIT_TIME", $params);
	    $fv = $r[0]['first_visit'];
	    if ($fv == null)
	    {
	        return '0';
	    }

	    # calculation of numbner online visitors per each day since the beginning
	    preg_match("/([0-9]*)-([0-9]*)-([0-9]*) ([0-9]*):([0-9]*):([0-9]*)/",$fv, $m);
	    $earliest_visit = mktime($m[4],$m[5],$m[6],$m[2], $m[3], $m[1]);
	    $visits = array();

	    $days = 0;
	    while ($earliest_visit <= $now)
	    {
	    	$from = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m", $earliest_visit) , date("d", $earliest_visit), date("Y", $earliest_visit)));
	    	$to   = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m", $earliest_visit) , date("d", $earliest_visit), date("Y", $earliest_visit)));

	    	$params = array(
	    	    'from' => $from,
	    	    'to'   => $to
	    	);

	    	$r = execQuery("SELECT_VISITORS_STATISTICS_BY_PERIOD",$params);
	    	$visits[] = array("date" => $earliest_visit, "online" => count($r));
		    $earliest_visit += (3600*24); //next day
		    $days++;
	    }

	    $all = 0;
	    if (count($visits) != 0 )
	    {
	        foreach ($visits as $i => $s)
	        {
	            $all += $s['online'];
	        }
	    }

	    if ($days == 0)
	    {
	        return '0';
	    }

	    $res = $all / $days; //total number or unique visitors by number of days

		if ($res != 0)
		{
		    return modApiFunc("Localization", "num_format", $res);
		}
		else
		{
			return '0';
		}
	}
}
?>