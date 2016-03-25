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

loadModuleFile('notifications/abstract/notification_content.php');

/**
 *
 * @package Notifications
 * @author Alexander Girin
 */
class LowLevel
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * NewOrder constructor.
     */
    function LowLevel()
    {
    }

    /**
     *
     */
    function onHook()
    {
        $order_id = modApiFunc("Checkout", "getLastPlacedOrderID");
        if ($order_id)
        {
            $notifications = modApiFunc("Notifications", "getNotificationsList", 4); // ProductLowLevelInStock
            foreach ($notifications as $notificationInfo)
            {
                $notification = new NotificationContent(array('notification_id' => $notificationInfo['Id'], 'order_id' => $order_id, 'action_id' => 4));
                $notification->send();
            }
        }
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