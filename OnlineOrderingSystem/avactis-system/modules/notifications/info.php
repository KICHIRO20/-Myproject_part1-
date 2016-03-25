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
 * Notifications module meta info.
 *
 * @package Notifications
 * @author Alexey Florinsky
 */

$moduleInfo = array
    (
        'name'         => 'Notifications', # this is also a main class name
        'shortName'    => 'NTFCTN',
        'groups'       => 'Main',
        'description'  => 'Notifications module',
        'version'      => '0.1.47700',
        'author'       => 'Alexey Florinsky',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'notifications_api.php',
        'resFile'      => 'notifications-messages',
        'extraAPIFiles' => array(
            'Notifications_Installer' => 'includes/notifications_installer.php'
        ),

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
           'AdminZone' => array(
               'DeleteNotification' => 'delete_notification_action.php'
              ,'SaveNotification' => 'save_notification_action.php'
              ,'AddNotification' => 'add_notification_action.php'
              ,'IncludeTemplate' => 'include_template_action.php'
            ),
            'SetCurrentNotification' => 'set_current_notification_action.php'
        ),

        'hooks' => array
        (
            'NewOrder' => array ( 'onAction'  => 'ConfirmOrder',
                                  'Hook_File' => 'new_order_hook.php')
           /*,'OrderStatusChanged' => array ( 'onAction'  => 'UpdateOrderAction',
                                            'Hook_File' => 'order_status_changed_hook.php')*/
           /*,'PaymentStatusChanged' => array ( 'onAction'  => 'UpdateOrderAction,UpdatePaymentStatus',
                                              'Hook_File' => 'payment_status_changed_hook.php')*/
           ,'LowLevel' => array ( 'onAction'  => 'ConfirmOrder',
                                  'Hook_File' => 'low_level_in_stock_hook.php')
           /*,'DownloadableProducts' => array ( 'onAction'  => 'UpdateOrderAction,UpdatePaymentStatus',
                                              'Hook_File' => 'downloadable_products_hook.php')*/
           ,'CustomerReviewPosted' => array(
                'onAction'  => 'post_review',
                'Hook_File' => 'customer_review_posted_hook.php'
            )
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'NotificationsList'                    => 'notifications_az.php'
               ,'MailInfo'                             => 'mail_info_az.php'
            ),
            'CustomerZone' => array
            (
            )
        )
    );
?>