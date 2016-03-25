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
 * @package Subscriptions
 * @author Sergey Galanin
 *
 */
$moduleInfo = array
    (
        'name'         => 'Subscriptions',
        'shortName'    => 'SUBSCR',
        'groups'       => 'Main',
        'description'  => 'Customers\' subscriptions to newsletters',
        'version'      => '0.0.47700',
        'author'       => 'Sergey Galanin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'subscriptions_api.php',
        'resFile'      => 'subscriptions-messages',
        'extraAPIFiles' => array(
            'DataReaderEmailsDB' => 'abstract/data_reader_emails_db.php',
            'DataReaderTopicsEmailsDB' => 'abstract/data_reader_topics_emails_db.php',
            'DataWriterText' => 'abstract/data_writer_text.php',
        ),

        'actions' => array (
           'AdminZone' => array (
                'update_topics_order' => 'update_topics_order.php',
                'delete_topics' => 'delete_topics.php',
                'delete_emails' => 'delete_emails.php',
                'create_topic'  => 'create_topic.php',
                'update_topic'  => 'update_topic.php',
                'subscribe'     => 'subscribe.php',
                'subscribe_confirm' => 'subscribe_confirm.php',
                'unsubscribe_confirm' => 'unsubscribe_confirm.php',
                'emails_export'   => 'export.php',
                'update_customer_subscriptions' => 'update_customer_subscriptions.php',
                'update_subscr_signature' => 'update_subscr_signature.php',
            ),
            'CustomerZone' => array (
                'customer_subscribe' => 'customer_subscribe.php',
                'unsubscribe_by_link' => 'unsubscribe_by_link.php',
            	'customer_remove_email' => 'customer_remove_email.php',
            )
        ),

        'hooks' => array (
        ),

        'views' => array (
            'AdminZone' => array (
                'Subscriptions_Manage'          => 'manage_az.php',
                'Subscriptions_EditTopic'       => 'edit_topic_az.php',
                'Subscriptions_SortTopics'      => 'sort_topics_az.php',
                'Subscriptions_DeleteTopics'    => 'delete_topics_az.php',
                'Subscriptions_Subscribe'       => 'subscribe_az.php',
                'Subscriptions_Unsubscribe'     => 'unsubscribe_az.php',
                'Subscriptions_Export'          => 'export_az.php',
                'Subscriptions_Topic_Name'      => 'topic_name.php',
                'Subscriptions_Signature'       => 'signature_az.php',
            ),
            'CustomerZone' => array (
                'SubscribeBox' => 'subscribe_box_cz.php',
                'SubscribeFormProfile' => 'subscribe_form_profile_cz.php',
                'UnsubscribeByLink' => 'unsubscribe_by_link_cz.php',
                'SubscribeOnCheckout' => 'subscribe_on_checkout_cz.php',
                'SubscribeOnCheckoutOutput' => 'subscribe_on_checkout_output_cz.php',
            )
        )
    );

?>