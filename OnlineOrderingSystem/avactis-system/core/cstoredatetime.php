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
 * CStoreDatetime class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Core
 */

loadCoreFile('cdatetime.php');

/**
 * CStoreDatetime class
 *
 */
class CStoreDatetime extends CDatetime
{
    function CStoreDatetime($timestamp = null, $time_shift_hours = null)
    {
        parent::CDatetime($timestamp);
        if ($time_shift_hours == null)
        {
            $time_shift_hours = modApiFunc('Configuration','getValue', SYSCONFIG_STORE_TIME_SHIFT);
        }
        $this->addHour($time_shift_hours);
    }
}

?>