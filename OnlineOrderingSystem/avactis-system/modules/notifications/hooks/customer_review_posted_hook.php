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
 * @author Sergey Kulitsky
 */
class CustomerReviewPosted
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor
     */
    function CustomerReviewPosted()
    {
    }

    /**
     *
     */
    function onHook($data_obj)
    {
        $cr_id = $data_obj -> _cr_id_posted;
        if ($cr_id > 0)
        {
            $notifications = modApiFunc('Notifications',
                                        'getNotificationsList',
                                        15); // CustomerReviewPosted
            foreach ($notifications as $info)
            {
                $notification = new NotificationContent(
                    array('notification_id' => $info['Id'],
                          'cr_id' => $cr_id,
                          'action_id' => 15)
                );
                $notification -> send();
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