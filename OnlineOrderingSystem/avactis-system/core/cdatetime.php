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
 * CDatetime class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Core
 */

define('CDATETIME_HOUR',    'CDATETIME_HOUR');
define('CDATETIME_MINUTE',  'CDATETIME_MINUTE');
define('CDATETIME_SECOND',  'CDATETIME_SECOND');
define('CDATETIME_DAY',     'CDATETIME_DAY');
define('CDATETIME_WEEK',    'CDATETIME_WEEK');
define('CDATETIME_MONTH',   'CDATETIME_MONTH');
define('CDATETIME_YEAR',    'CDATETIME_YEAR');

define('CDATETIME_WEEK_FIRST_DAY_MONDAY', 'CDATETIME_WEEK_FIRST_DAY_MONDAY');
define('CDATETIME_WEEK_FIRST_DAY_SUNDAY', 'CDATETIME_WEEK_FIRST_DAY_SUNDAY');

/**
 * CDatetime class
 *
 *                                                  ,
 *         .
 *
 */
class CDatetime
{
    function CDatetime($timestamp = null)
    {
        $this->__timestamp = ($timestamp == null ? time() : $timestamp);
    }

    function getTimestamp()
    {
        return $this->__timestamp;
    }

    function getYear()
    {
        $info = getdate($this->__timestamp);
        return  $info['year']; //A full numeric representation of a year, 4 digits
    }

    function getMonth()
    {
        $info = getdate($this->__timestamp);
        return  $info['mon']; //Numeric representation of a month
    }

    function getMonthDay()
    {
        $info = getdate($this->__timestamp);
        return  $info['mday']; //Numeric representation of the day of the month
    }

    function getWeekDay($mode = CDATETIME_WEEK_FIRST_DAY_MONDAY)
    {
        // 0 (for Sunday) through 6 (for Saturday)
        $info = getdate($this->__timestamp);

        // Apply week day offset, after that we'll have: 0 for Monday through 6 for Sunday
        if ($mode == CDATETIME_WEEK_FIRST_DAY_MONDAY)
        {
            $info['wday'] = ($info['wday'] == 0 ? 6 : $info['wday'] - 1);
        }
        return  $info['wday']; //Numeric representation of the day of the week
    }

    function getHours()
    {
        $info = getdate($this->__timestamp);
        return  $info['hours']; //Numeric representation of hours
    }

    function getMinutes()
    {
        $info = getdate($this->__timestamp);
        return  $info['minutes']; //Numeric representation of minutes
    }

    function getSeconds()
    {
        $info = getdate($this->__timestamp);
        return  $info['seconds']; //Numeric representation of seconds
    }

    function getMonthDaysNumber()
    {
        return date('t', $this->__timestamp); // Number of days in the given month, 28 through 31
    }

    function floorTime()
    {
        $this->__timestamp = mktime(0, 0, 0, date("m", $this->__timestamp), date("d", $this->__timestamp), date("Y", $this->__timestamp));
    }

    function ceilTime()
    {
        $this->__timestamp = mktime(23, 59, 59, date("m", $this->__timestamp), date("d", $this->__timestamp), date("Y", $this->__timestamp));
    }

    function getTimestampFirstWeekDay($mode = CDATETIME_WEEK_FIRST_DAY_MONDAY)
    {
        $week_day = $this->getWeekDay($mode);
        return $this->__addDate(-$week_day, CDATETIME_DAY);
    }

    function getTimestampLastWeekDay($mode = CDATETIME_WEEK_FIRST_DAY_MONDAY)
    {
        $week_day = $this->getWeekDay($mode);
        return $this->__addDate(6-$week_day, CDATETIME_DAY);
    }

    function getTimestampFirstMonthDay()
    {
        $month_day = $this->getMonthDay() - 1;
        return $this->__addDate(-$month_day, CDATETIME_DAY);
    }

    function getTimestampLastMonthDay()
    {
        $month_day = $this->getMonthDay() - 1;
        $days_in_month = $this->getMonthDaysNumber() - 1;
        return $this->__addDate($days_in_month - $month_day, CDATETIME_DAY);
    }

    function addSec($sec)
    {
        $this->__timestamp = $this->__addDate($sec, CDATETIME_SECOND);
    }

    function addMinute($min)
    {
        $this->__timestamp = $this->__addDate($min, CDATETIME_MINUTE);
    }

    function addHour($hours)
    {
        $this->__timestamp = $this->__addDate($hours, CDATETIME_HOUR);
    }

    function addDay($days)
    {
        $this->__timestamp = $this->__addDate($days, CDATETIME_DAY);
    }

    function addWeek($weeks)
    {
        $this->__timestamp = $this->__addDate($weeks, CDATETIME_WEEK);
    }

    function addMonth($months)
    {
        $this->__timestamp = $this->__addDate($months, CDATETIME_MONTH);
    }

    function addYear($years)
    {
        $this->__timestamp = $this->__addDate($years, CDATETIME_YEAR);
    }

    function addValue($value, $unit)
    {
        $this->__timestamp = $this->__addDate($value, $unit);
    }

    function __addDate($value, $unit)
    {
        $date = getdate($this->__timestamp);
        switch ($unit)
        {
            case CDATETIME_SECOND:
                $date['seconds'] += $value;
                break;
            case CDATETIME_MINUTE:
                $date['minutes'] += $value;
                break;
            case CDATETIME_HOUR:
                $date['hours'] += $value;
                break;
            case CDATETIME_DAY:
                $date['mday'] += $value;
                break;
            case CDATETIME_WEEK:
                $date['mday'] += $value*7;
                break;
            case CDATETIME_MONTH:
                $date['mon'] += $value;
                break;
            case CDATETIME_YEAR:
                $date['year'] += $value;
                break;
        }
        return mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year']);
    }

    var $__timestamp = null;
}

?>