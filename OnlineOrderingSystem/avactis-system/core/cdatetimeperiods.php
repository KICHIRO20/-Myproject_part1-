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
 * CDatetimePeriods class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Core
 */

loadCoreFile('cdatetime.php');

define('CDATETIMEPERIODS_INCLUDING_CURRENT_ONE','CDATETIMEPERIODS_INCLUDING_CURRENT_ONE');
define('CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE','CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE');

/**
 * CDatetimePeriods class
 *
 */
class CDatetimePeriods
{
    function CDatetimePeriods($datetime_obj = null)
    {
        if ($datetime_obj == null)
        {
            $this->__datetime = new CDatetime();
        }
        else
        {
            $this->__datetime = $datetime_obj;
        }
    }

    /**
     *                   X (   )               ,                             .
     *
     * @param unknown_type $days_number
     */
    function getLastDays($days_number, $include_current = CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE)
    {
        list($offset, $length) = $this->__getOffsetAndLength($days_number, $include_current);
        return $this->getPeriod($offset, CDATETIME_DAY, $length, CDATETIME_DAY);
    }

    /**
     *                   X (   )                 ,                        .
     *
     * @param unknown_type $weeks_number
     * @param unknown_type $week_mode
     * @param unknown_type $include_current
     * @return unknown
     */
    function getLastWeeks($weeks_number, $week_mode = CDATETIME_WEEK_FIRST_DAY_MONDAY, $include_current = CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE)
    {
        list($week_offset, $week_length) = $this->__getOffsetAndLength($weeks_number, $include_current);

        //                    :
        // 1.                ,                                             ,
        //                               .                 ,
        //            .                                           ,                      .
        // 2.                                                      .               ,
        //                                                   .
        // 3.                                            :      $from                               ,
        //         $to -                        .
        // 4.                         .

        //      ,                                                         .
        list($from_timestamp, $to_timestamp) = $this->getPeriod($week_offset, CDATETIME_WEEK, $week_length, CDATETIME_WEEK);

        $obj_from = new CDatetime($from_timestamp);
        $from = $obj_from->getTimestampFirstWeekDay($week_mode);

        $obj_to = new CDatetime($to_timestamp);
        $to = $obj_to->getTimestampLastWeekDay($week_mode);

        return array($from, $to);
    }

    function getLastMonths($months_number, $include_current = CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE)
    {
        list($months_offset, $months_length) = $this->__getOffsetAndLength($months_number, $include_current);
        list($from_timestamp, $to_timestamp) = $this->getPeriod($months_offset, CDATETIME_MONTH, $months_length, CDATETIME_MONTH);

        $obj_from = new CDatetime($from_timestamp);
        $from = $obj_from->getTimestampFirstMonthDay();

        $obj_to = new CDatetime($to_timestamp);
        $to = $obj_to->getTimestampLastMonthDay();

        return array($from, $to);
    }

    function getLastYears($number, $include_current = CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE)
    {
        list($offset, $length) = $this->__getOffsetAndLength($number, $include_current);
        list($from_timestamp, $to_timestamp) = $this->getPeriod($offset*12, CDATETIME_MONTH, $length*12, CDATETIME_MONTH);

        $obj_from = new CDatetime($from_timestamp);
        $from_year = $obj_from->getYear();
        $from = mktime(0,0,0,1,1,$from_year);

        $obj_to = new CDatetime($to_timestamp);
        $to_year = $obj_to->getYear();
        $to = mktime(23, 59, 59, 12, 31, $to_year);

        return array($from, $to);
    }

    function getPeriod($offset, $offset_unit, $length, $length_unit)
    {
        $from = clone($this->__datetime);
        $from->addValue($offset, $offset_unit);

        $to = clone($from);
        $to->addValue($length, $length_unit);

        $from->floorTime();
        $to->ceilTime();

        return array($from->getTimestamp(), $to->getTimestamp());
    }

    function __getOffsetAndLength($number, $include_current)
    {
        if ($number <= 0)
        {
            $number = 1;
        }
        $length = $number - 1;
        if ($include_current == CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE)
        {
            $offset = -$number;
        }
        else
        {
            $offset = -($number - 1);
        }
        return array($offset, $length);
    }



    var $__datetime = null;
}

?>