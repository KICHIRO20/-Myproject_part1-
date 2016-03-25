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
 *
 * @package Notifications
 * @author Alexander Girin
 */
class SetCurrentNotification extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function SetCurrentNotification()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');
        $NotificationId = $request->getValueByKey('n_id');
        modApiFunc("Notifications", "setCurrentNotificationId", $NotificationId);
        modApiFunc("Notifications", "saveState");
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>