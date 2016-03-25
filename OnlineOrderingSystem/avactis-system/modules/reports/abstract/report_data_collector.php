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
 * CReportDataCollector class
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CReportDataCollector
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function getTimestamp()
    {
        $f = $this->get_timestamp_function_name;
        return $f();
    }

    function isStatisticsEnable()
    {
        return !Configuration::getSupportMode(ASC_S_STATISTICS);
    }

    /**
     *                                ,                            timestamp.
     *  .                 time.
     *                 ,
     *                               .
     */
    function setTimestampFunction($func)
    {
        $this->get_timestamp_function_name = $func;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $get_timestamp_function_name = 'time';
    /**#@-*/

}
?>