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
class DownloadableProducts
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DownloadableProducts constructor.
     */
    function DownloadableProducts()
    {
    }

    /**
     *
     */
    function onHook($actionObj)
    {
        if (isset($actionObj->result['payment_status']) && sizeof($actionObj->result['payment_status']))
        {
            foreach ($actionObj->result['payment_status'] as $order_id => $statuses)
            {
                if (($statuses['new_status'] != 2) or ($statuses['new_status'] == $statuses['old_status']))
                {
                    continue;
                }

                $order_id = intval($order_id, 10);
                $notifications = modApiFunc("Notifications", "getNotificationsList", 5);
                foreach ($notifications as $notificationInfo)
                {
                    $notification = new NotificationContent(array('notification_id' => $notificationInfo['Id'], 'order_id' => $order_id, 'action_id' => 5));
                    $notification->send();
                }
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