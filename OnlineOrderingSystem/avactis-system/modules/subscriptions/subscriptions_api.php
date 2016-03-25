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

class Subscriptions
{

    function Subscriptions()
    {

    }

    function install()
    {
        $query = new DB_Table_Create(Subscriptions::getTables());
        modApiFunc('EventsManager', 'addEventHandler', 'CustomerRegistered', 'Subscriptions', 'onCustomerRegistered');
        modApiFunc('EventsManager', 'addEventHandler', 'OrderCreated', 'Subscriptions', 'onOrderCreated');

        // Subscriptions advanced configuration
        $group_info = array('GROUP_NAME'        => 'SUBSCRIPTIONS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('SUBSCR', 'SUBSCR_GROUP_NAME'),
                                                            'DESCRIPTION'   => array('SUBSCR', 'SUBSCR_GROUP_DESCR')),
                            'GROUP_VISIBILITY'  => 'SHOW');
        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                'GROUP_NAME'        => $group_info['GROUP_NAME'],
                'PARAM_NAME'        => 'USERS_CAN_UNSUBSCRIBE',
                'PARAM_DESCRIPTION' => array(
                        'NAME'        => array('SUBSCR', 'USERS_CAN_UNSUBSCRIBE_NAME'),
                        'DESCRIPTION' => array('SUBSCR', 'USERS_CAN_UNSUBSCRIBE_DESCR') ),
                'PARAM_TYPE'          => PARAM_TYPE_LIST,
                'PARAM_VALUE_LIST'    => array(
                        array( 'VALUE' => 'Yes', 'VALUE_DESCRIPTION' => array(
                                'NAME'        => array('SUBSCR', 'USERS_CAN_UNSUBSCRIBE_YES_NAME'),
                                'DESCRIPTION' => array('SUBSCR', 'USERS_CAN_UNSUBSCRIBE_YES_NAME') ),
                        ),
                        array( 'VALUE' => 'No', 'VALUE_DESCRIPTION' => array(
                                'NAME'        => array('SUBSCR', 'USERS_CAN_UNSUBSCRIBE_NO_NAME'),
                                'DESCRIPTION' => array('SUBSCR', 'USERS_CAN_UNSUBSCRIBE_NO_NAME') ),
                        ),
                ),
                'PARAM_CURRENT_VALUE' => 'Yes',
                'PARAM_DEFAULT_VALUE' => 'Yes',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                'GROUP_NAME'        => $group_info['GROUP_NAME'],
                'PARAM_NAME'        => 'CHECKOUT_SUBSCRIBE_MODE',
                'PARAM_DESCRIPTION' => array(
                        'NAME'        => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE'),
                        'DESCRIPTION' => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_DESCR') ),
                'PARAM_TYPE'          => PARAM_TYPE_LIST,
                'PARAM_VALUE_LIST'    => array(
                        array( 'VALUE' => 'AUTO', 'VALUE_DESCRIPTION' => array(
                                'NAME'        => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_AUTO'),
                                'DESCRIPTION' => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_AUTO_DESCR') ),
                        ),
                        array( 'VALUE' => 'MANUAL', 'VALUE_DESCRIPTION' => array(
                                'NAME'        => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_MANUAL'),
                                'DESCRIPTION' => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_MANUAL_DESCR') ),
                        ),
                        array( 'VALUE' => 'NONE', 'VALUE_DESCRIPTION' => array(
                                'NAME'        => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_NONE'),
                                'DESCRIPTION' => array('SUBSCR', 'CHECKOUT_SUBSCRIBE_MODE_NONE_DESCR') ),
                        ),
                ),
                'PARAM_CURRENT_VALUE' => 'MANUAL',
                'PARAM_DEFAULT_VALUE' => 'MANUAL',
        );
        modApiFunc('Settings','createParam', $param_info);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Subscriptions::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        // ALTER TABLE `email_address` CHANGE `person_id` `customer_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0'
        $table = 'email_address';
        $tables[$table] = array(
            'columns'   => array(
                'email_id'      => $table.'.email_id',
                'customer_id'   => $table.'.customer_id',
                'email'         => $table.'.email',
                'lng'           => $table.'.lng'
             ),
            'types'     => array(
                'email_id'      => DBQUERY_FIELD_TYPE_INT.' NOT NULL auto_increment',
                'customer_id'   => DBQUERY_FIELD_TYPE_INT.' NOT NULL default 0',
                'email'         => DBQUERY_FIELD_TYPE_CHAR50,
                'lng'           => DBQUERY_FIELD_TYPE_CHAR2
             ),
            'primary'   => array(
                'email_id',
             ),
            'indexes'   => array(
                'IDX_customer'    => 'customer_id',
                'UNIQUE KEY UNQ_email'     => 'email',
             )
        );

        $table = 'subscription_topic';
        $tables[$table] = array(
            'columns'   => array(
                'topic_id'      => $table.'.topic_id',
                'topic_name'    => $table.'.topic_name',
                'sort_order'    => $table.'.sort_order',
                'topic_status'  => $table.'.topic_status',
                'topic_access'  => $table.'.topic_access',
                'topic_auto'    => $table.'.topic_auto',
            ),
            'types'     => array(
                'topic_id'      => DBQUERY_FIELD_TYPE_INT.' NOT NULL auto_increment',
                'topic_name'    => DBQUERY_FIELD_TYPE_CHAR255,
                'sort_order'    => DBQUERY_FIELD_TYPE_INT.' NOT NULL default 0',
                'topic_status'  => DBQUERY_FIELD_TYPE_CHAR1,
                'topic_access'  => DBQUERY_FIELD_TYPE_CHAR1,
                'topic_auto'    => DBQUERY_FIELD_TYPE_CHAR1,
            ),
            'primary'   => array(
                'topic_id',
             ),
            'indexes'   => array(
                'IDX_order' => 'sort_order',
             )
        );

        $table = 'subscription_email';
        $tables[$table] = array(
            'columns'   => array(
                'topic_id'      => $table.'.topic_id',
                'email_id'      => $table.'.email_id',
             ),
            'types'     => array(
                'topic_id'      => DBQUERY_FIELD_TYPE_INT.' NOT NULL default 0',
                'email_id'      => DBQUERY_FIELD_TYPE_INT.' NOT NULL default 0',
             ),
            'primary'   => array(
             ),
            'indexes'   => array(
                'UNIQUE KEY UNQ_topic'     => 'topic_id, email_id',
                'IDX_email'     => 'email_id',
             )
        );

        $table = 'subscription_temp';
        $tables[$table] = array(
            'columns'   => array(
                'action_key'    => $table.'.action_key',
                'email'         => $table.'.email',
                'email_id'      => $table.'.email_id',
                'state'         => $table.'.state',
             ),
            'types'     => array(
                'action_key'    => DBQUERY_FIELD_TYPE_INT.' NOT NULL',
                'email'         => DBQUERY_FIELD_TYPE_CHAR50,
                'email_id'      => DBQUERY_FIELD_TYPE_INT.' NOT NULL default 0',
                'state'         => DBQUERY_FIELD_TYPE_CHAR1.' default \'U\'',
             ),
            'primary'   => array(
             ),
            'indexes'   => array(
                'UNIQUE KEY UNQ_email' => 'action_key, email',
                'IDX_state'    => 'action_key, state',
                'IDX_email_id' => 'action_key, email_id',
             )
        );

        $table = 'subscription_guest';
        $tables[$table] = array(
            'columns'   => array(
                'subscription_key'=> $table.'.subscription_key',
                'email'         => $table.'.email',
                'updated'       => $table.'.updated',
             ),
            'types'     => array(
                'subscription_key'    => SUBSCRIPTION_KEY_FIELD_TYPE.' NOT NULL',
                'email'         => DBQUERY_FIELD_TYPE_CHAR50,
                'updated'       => DBQUERY_FIELD_TYPE_INT.' NOT NULL default 0',
             ),
            'primary'   => array(
             ),
            'indexes'   => array(
                'UNIQUE KEY UNK_key'    => 'subscription_key',
                'UNIQUE KEY UNK_email'  => 'email',
             )
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    //

    function &getMessageResources()
    {
        global $application;
        static $_messageResources;
        if (! isset($_messageResources)) {
            $lang = _ml_strtolower($application->getAppIni('LANGUAGE'));
            $_messageResources = new MessageResources(dirname(__FILE__).'/resources/subscriptions-messages-'.$lang.'.ini', 'AdminZone');
        }
        return $_messageResources;
    }

    function getTopicStatusesNames()
    {
        $_messageResources = & Subscriptions::getMessageResources();
        return array(
                SUBSCRIPTION_TOPIC_ACTIVE => $_messageResources->getMessage('TOPIC_STATUS_ACTIVE'),
                SUBSCRIPTION_TOPIC_CANNOT_SUBSCRIBE => $_messageResources->getMessage('TOPIC_STATUS_CANNOT_SUBSCRIBE'),
                SUBSCRIPTION_TOPIC_INACTIVE => $_messageResources->getMessage('TOPIC_STATUS_INACTIVE'),
                );
    }

    function getTopicStatusName($topic_status)
    {
        $names = Subscriptions::getTopicStatusesNames();
        $_messageResources = & Subscriptions::getMessageResources();
        return isset($names[$topic_status]) ? $names[$topic_status] : $_messageResources->getMessage('TOPIC_STATUS_UNKNOWN');
    }

    function getTopicAccessesNames()
    {
        $_messageResources = & Subscriptions::getMessageResources();
        return array(
                SUBSCRIPTION_TOPIC_FULL_ACCESS => $_messageResources->getMessage('TOPIC_ACCESS_FULL'),
                SUBSCRIPTION_TOPIC_REGISTERED_ONLY => $_messageResources->getMessage('TOPIC_ACCESS_REGISTERED'),
                );
    }

    function getTopicAccessName($topic_access)
    {
        $_messageResources = & Subscriptions::getMessageResources();
        $names = Subscriptions::getTopicAccessesNames();
        return isset($names[$topic_access]) ? $names[$topic_access] : $_messageResources->getMessage('TOPIC_ACCESS_UNKNOWN');
    }

    function getTopicAutoSubscribeNames()
    {
        $_messageResources = & Subscriptions::getMessageResources();
        return array(
                SUBSCRIPTION_TOPIC_AUTOSUBSCRIBE_YES => $_messageResources->getMessage('TOPIC_AUTOSUBSCRIBE_YES'),
                SUBSCRIPTION_TOPIC_AUTOSUBSCRIBE_NO => $_messageResources->getMessage('TOPIC_AUTOSUBSCRIBE_NO'),
                );
    }

    function getTopicAutoSubscribeName($topic_auto)
    {
        $_messageResources = & Subscriptions::getMessageResources();
        $names = Subscriptions::getTopicAutoSubscribeNames();
        return isset($names[$topic_auto]) ? $names[$topic_auto] : $_messageResources->getMessage('TOPIC_AUTOSUBSCRIBE_UNKNOWN');
    }

    //

    function canClientUnsubscribe()
    {
        return modApiFunc('Settings', 'getParamValue', 'SUBSCRIPTIONS', 'USERS_CAN_UNSUBSCRIBE') === 'Yes';
    }

    //

    function getTopic($topic_id)
    {
        $res = execQuery('SUBSCR_SELECT_TOPIC_INFO', array('topic_id' => $topic_id));
        return reset($res);
    }

    function getTopicEmails($topic_id, $search_email = null)
    {
        global $application;

        $tables = $this->getTables();

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $query = new DB_Select($atable);
        $query->addSelectField($acolumns['email_id']);
        $query->addSelectField($acolumns['customer_id']);
        $query->addSelectField($acolumns['email']);
        $query->addSelectField($acolumns['lng']);
        $query->addInnerJoin($etable, $ecolumns['email_id'], DB_EQ, $acolumns['email_id']);
        $query->WhereValue($ecolumns['topic_id'], DB_EQ, $topic_id);
        if (! empty($search_email)) {
            $query->WhereAND();
            $query->WhereValue($acolumns['email'], DB_LIKE, '%'.$search_email.'%');
        }
        $query->SelectOrder($acolumns['email'], 'ASC');
        $query = modApiFunc('paginator', 'setQuery', $query);
        $res = $application->db->getDB_Result($query);
        return $res;
    }

    function getTopicsEmailsIdsByTopicsIds($topics_ids)
    {
        global $application;
        if (empty($topics_ids)) {
            return array();
        }

        $tables = $this->getTables();

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $query = new DB_Select($etable);
        $query->addSelectField($ecolumns['topic_id']);
        $query->addSelectField($ecolumns['email_id']);
        $query->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $res = $application->db->getDB_Result($query);

        $emails_ids = array();
        foreach(array_keys($res) as $i) {
            $emails_ids[ $res[$i]['topic_id'] ][] = $res[$i]['email_id'];
        }
        return $emails_ids;
    }

    function getTopicsEmailsByTopicsIds($topics_ids)
    {
        global $application;
        if (empty($topics_ids)) {
            return array();
        }

        $tables = $this->getTables();

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $query = new DB_Select($etable);
        $query->addSelectField($ecolumns['topic_id']);
        $query->addSelectField($acolumns['email']);
        $query->addSelectField($acolumns['lng']);
        $query->addInnerJoin($atable, $acolumns['email_id'], DB_EQ, $ecolumns['email_id']);
        $query->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $res = $application->db->getDB_Result($query);

        return $res;
    }

    function getEmailsIdsByTopicsIds($topics_ids)
    {
        global $application;
        if (empty($topics_ids)) {
            return array();
        }

        $tables = $this->getTables();

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $query = new DB_Select($etable);
        $query->addSelectField($ecolumns['email_id']);
        $query->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $query->SelectGroup($ecolumns['email_id']);
        $res = $application->db->getDB_Result($query);

        foreach(array_keys($res) as $i) {
            $res[$i] = $res[$i]['email_id'];
        }
        return $res;
    }

    function getEmailsByTopicsIds($topics_ids)
    {
        global $application;
        if (empty($topics_ids)) {
            return array();
        }

        $tables = $this->getTables();

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $query = new DB_Select($etable);
        $query->addSelectField($acolumns['email']);
        $query->addSelectField($acolumns['lng']);
        $query->addInnerJoin($atable, $acolumns['email_id'], DB_EQ, $ecolumns['email_id']);
        $query->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $query->SelectGroup($ecolumns['email_id']);
        $res = $application->db->getDB_Result($query);

        foreach(array_keys($res) as $i) {
            $res[$i] = $res[$i]['email'];
        }
        return $res;
    }

    function getEmailById($email_id)
    {
        global $application;

        $tables = $this->getTables();

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $query = new DB_Select($atable);
        $query->addSelectField($acolumns['email']);
        $query->WhereValue($acolumns['email_id'], DB_EQ, $email_id);
        $res = $application->db->getDB_Result($query);
        return $res[0]['email'];
    }

    function getTopicsList($for_emails = null)
    {
        return execQuery('SUBSCR_SELECT_TOPIC_LIST_BY_EMAIL', array('for_emails' => $for_emails));
    }

    function getTopicsByIds($topics_ids)
    {
        if (empty($topics_ids)) {
            return array();
        }

        if (!is_array($topics_ids))
            $topics_ids = array($topics_ids);

        return execQuery('SUBSCR_SELECT_TOPIC_LIST_BY_ID', array('topics_ids' => $topics_ids));
    }

    function getTopicsNamesByIds($topics_ids)
    {
        $topics = $this->getTopicsByIds($topics_ids);
        $names = array();
        foreach(array_keys($topics) as $i) {
            $names[ $topics[$i]['topic_id'] ] = $topics[$i]['topic_name'];
        }
        return $names;
    }

    function getNextTopicOrder()
    {
        global $application;

        $table = 'subscription_topic';

        $query = new DB_Select();
        $query->addSelectTable($table);
        $query->addSelectField(DB_Select::fMax('sort_order'), 'max_sort_order');
        $res = $application->db->getDB_Result($query);

        return $res[0]['max_sort_order'] + 1;
    }

    function updateOrder($topic_id, $new_order)
    {
        global $application;
        $tables = Subscriptions::getTables();
        $table = 'subscription_topic';
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['sort_order'], $new_order);
        $query->WhereValue($columns['topic_id'], DB_EQ, $topic_id);

        return $application->db->getDB_Result($query);
    }

    function setTopicsSortOrder($topics_ids)
    {
        $o = 1;
        foreach($topics_ids as $topic_id) {
            $this->updateOrder($topic_id, $o++);
        }
    }

    function createTopic($topic_name, $sort_order, $topic_status, $topic_access, $topic_auto)
    {
        $statuses = Subscriptions::getTopicStatusesNames();
        if (! isset($statuses[$topic_status])) {
            $topic_status = SUBSCRIPTION_TOPIC_CANNOT_SUBSCRIBE;
        }

        return execQuery('SUBSCR_INSERT_NEW_TOPIC', array('topic_name' => $topic_name,
                                                          'sort_order' => $sort_order,
                                                          'topic_status' => $topic_status,
                                                          'topic_access' => $topic_access,
                                                          'topic_auto' => $topic_auto));
    }

    function updateTopic($topic_id, $topic_name, $topic_status, $topic_access, $topic_auto)
    {
        $statuses = Subscriptions::getTopicStatusesNames();
        if (! isset($statuses[$topic_status])) {
            $topic_status = SUBSCRIPTION_TOPIC_CANNOT_SUBSCRIBE;
        }

        return execQuery('SUBSCR_UPDATE_TOPIC', array('topic_name' => $topic_name,
                                                      'topic_id' => $topic_id,
                                                      'topic_status' => $topic_status,
                                                      'topic_access' => $topic_access,
                                                      'topic_auto' => $topic_auto));
    }

    function deleteTopics($topics_ids)
    {
        global $application;
        if (empty($topics_ids)) {
            return array();
        }

        $tables = Subscriptions::getTables();

        // delete topics self
        execQuery('SUBSCR_DELETE_TOPIC_BY_ID', array('topics_ids' => $topics_ids));

        // delete subscriptions
        $table = 'subscription_email';
        $columns = & $tables[$table]['columns'];
        $equery = new DB_Delete($table);
        $equery->Where($columns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $application->db->getDB_Result($equery);

        // delete newsletters links
        $tables = modApiFunc('Newsletter', 'getTables');
        $table = 'newsletter_topics';
        $columns = & $tables[$table]['columns'];
        $nquery = new DB_Delete($table);
        $nquery->Where($columns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $application->db->getDB_Result($nquery);
    }

    function deleteEmails($topics_ids, $emails_ids)
    {
        global $application;
        if (empty($topics_ids) || empty($emails_ids)) {
            return;
        }
        $tables = Subscriptions::getTables();
        $table = 'subscription_email';
        $columns = $tables[$table]['columns'];

        $query = new DB_Delete($table);
        $query->Where($columns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $query->WhereAND();
        $query->Where($columns['email_id'], DB_IN, DBQuery::arrayToIn($emails_ids));
        $application->db->getDB_Result($query);
    }

    function changeSubscriptions($email, $topics, $signed_in = null)
    {
        $ViewState = array();

        $subscribed = modApiFunc('Subscriptions', 'getSubscribedTopics', $email, $signed_in, true);
        $unsubscribe = array_diff($subscribed, $topics);
        $subscribe = array_diff($topics, $subscribed);

        if (empty($subscribe) && empty($unsubscribe)) {
            $ViewState['Messages'][] = getMsg('SUBSCR', 'MSG_SUBSCR_NOT_CHANGED');
        }
        else {
            modApiFunc('Subscriptions', 'unsubscribeEmails', $unsubscribe, $email);
            modApiFunc('Subscriptions',   'subscribeEmails',   $subscribe, $email);
            $ViewState['Messages'][] = getMsg('SUBSCR', 'MSG_SUBSCR_CHANGED_SUCCESS');
        }

        return $ViewState;
    }

    function subscribeEmails($topics_ids, $emails)
    {
        if (!is_array($emails)) {
            $emails = array($emails);
        }
        $n = sizeof($emails);
        for ($i = 0; $i < $n; $i += MAX_EMAILS_AT_ONCE) {
            $_emails = array_slice($emails, $i, MAX_EMAILS_AT_ONCE);
            Subscriptions::_subscribeEmails($topics_ids, $_emails);
        }
    }

    function _subscribeEmails($topics_ids, $emails)
    {
        global $application;
        if (empty($topics_ids) || empty($emails)) {
            return;
        }

        $tables = Subscriptions::getTables();
        $table = 'email_address';
        $columns = $tables[$table]['columns'];

        $query = new DB_Multiple_Insert($table);
        $query->setModifiers(DB_IGNORE);
        $query->setInsertFields(array($columns['email'], $columns['lng']));
        $default_lng = modApiFunc('MultiLang', 'getLanguage');
        foreach ($emails as $email) {
            $query->addInsertValuesArray(array($default_lng, $email));
        }

        $application->db->getDB_Result($query);

        $itable = 'subscription_email';
        $icolumns = $tables[$itable]['columns'];

        foreach($topics_ids as $topic_id) {
            unset($squery);
            $squery = new DB_Select($table);
            $squery->addSelectField($topic_id);
            $squery->addSelectField($columns['email_id']);
            $squery->Where($columns['email'], DB_IN, DBQuery::arrayToIn($emails));

            unset($iquery);
            $iquery = new DB_Insert_Select($itable);
            $iquery->setModifiers(DB_IGNORE);
            $iquery->setInsertFields(array('topic_id', 'email_id'));
            $iquery->setSelectQuery($squery);

            $application->db->getDB_Result($iquery);
        }
    }

    function unsubscribeEmails($topics_ids, $emails)
    {
        if (!is_array($emails)) {
            $emails = array($emails);
        }
        $n = sizeof($emails);
        for ($i = 0; $i < $n; $i += MAX_EMAILS_AT_ONCE) {
            $_emails = array_slice($emails, $i, MAX_EMAILS_AT_ONCE);
            Subscriptions::_unsubscribeEmails($topics_ids, $_emails);
        }
    }

    function _unsubscribeEmails($topics_ids, $emails)
    {
        global $application;
        if (empty($topics_ids) || empty($emails)) {
            return array();
        }

        $tables = Subscriptions::getTables();
        $table = 'subscription_email';
        $columns = & $tables[$table]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        foreach ($topics_ids as $topic_id) {
            $dquery = new DB_Delete($table);
            $dquery->addUsingTable($table);
            $dquery->addUsingTable($atable);
            $dquery->WhereValue($columns['topic_id'], DB_EQ, $topic_id);
            $dquery->WhereAND();
            $dquery->WhereField($columns['email_id'], DB_EQ, $acolumns['email_id']);
            $dquery->WhereAND();
            $dquery->Where($acolumns['email'], DB_IN, DBQuery::arrayToIn($emails));
            $application->db->getDB_Result($dquery);
        }
    }


    // emails temp table

    function getActionKey()
    {
        global $application;

        $tables = Subscriptions::getTables();
        $table = 'subscription_temp';
        $columns = & $tables[$table]['columns'];

        while(1) {
            $key = mt_rand(1, 0x7fffffff);
            $query = new DB_Select($table);
            $query->addSelectField('action_key');
            $query->WhereValue($columns['action_key'], DB_EQ, $key);
            $query->SelectLimit(0, 1);

            $result = $application->db->getDB_Result($query);
            if (sizeof($result) == 0) {
                break;
            }
        }
        return $key;
    }

    function addTempEmails($key, &$emails)
    {
        global $application;

        $tables = Subscriptions::getTables();
        $table = 'subscription_temp';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Multiple_Insert($table);
        $query->setModifiers(DB_IGNORE);
        $query->setInsertFields(array('action_key', 'email'));
        foreach($emails as $email) {
            $query->addInsertValuesArray(array('action_key' => $key, 'email' => $email));
        }
        $application->db->getDB_Result($query);
    }

    function linkTempEmails($key)
    {
        global $application;

        $tables = Subscriptions::getTables();

        $table = 'subscription_temp';
        $columns = & $tables[$table]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateTable($atable);
        $query->addUpdateExpression($columns['email_id'], $acolumns['email_id']);
        $query->addUpdateValue($columns['state'], SUBSCRIPTION_TEMP_EXISTS);
        $query->WhereValue($columns['action_key'], DB_EQ, $key);
        $query->WhereAND();
        $query->WhereField($columns['email'], DB_EQ, $acolumns['email']);
        $application->db->getDB_Result($query);

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['state'], SUBSCRIPTION_TEMP_DONT_EXISTS);
        $query->WhereValue($columns['action_key'], DB_EQ, $key);
        $query->WhereAND();
        $query->WhereValue($columns['state'], DB_EQ, SUBSCRIPTION_TEMP_UNKNOWN);
        $application->db->getDB_Result($query);

    }

    function getTopicsByIdsTemp($key, $topics_ids)
    {
        if (empty($topics_ids)) {
            return array();
        }

        return execQuery('SUBSCR_SELECT_TOPIC_BY_KEY', array('key' => $key, 'topics_ids' => $topics_ids));
    }

    function countTempEmails($key, $state = null)
    {
        global $application;

        $tables = $this->getTables();
        $table = 'subscription_temp';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Select($table);
        $query->addSelectField(DB_Select::fCount($columns['email_id']), 'emails_count');
        $query->WhereValue($columns['action_key'], DB_EQ, $key);
        if (isset($state)) {
            $query->WhereAND();
            $query->WhereValue($columns['state'], DB_EQ, $state);
        }
        $result = $application->db->getDB_Result($query);
        return $result[0]['emails_count'];
    }

    function copyTempEmails($key)
    {
        global $application;

        $tables = Subscriptions::getTables();
        $stable = 'subscription_temp';
        $itable = 'email_address';

        $iquery = new DB_Insert_Select($itable);
        $iquery->setModifiers(DB_IGNORE);
        $iquery->setInsertFields(array($tables[$itable]['columns']['email'], $tables[$itable]['columns']['lng']));

        $squery = new DB_Select($stable);
        $squery->addSelectField($tables[$stable]['columns']['email']);
        $squery->addSelectValue('\'' . modApiFunc('MultiLang', 'getLanguage') . '\'', 'lng');
        $squery->Where($tables[$stable]['columns']['action_key'], DB_EQ, DBQuery::quoteValue($key));

        $iquery->setSelectQuery($squery);

        $application->db->getDB_Result($iquery);
    }

    function subscribeTempEmails($key, $topics_ids)
    {
        global $application;

        $tables = Subscriptions::getTables();
        $stable = 'subscription_temp';
        $scolumns = & $tables[$stable]['columns'];

        $itable = 'subscription_email';
        $icolumns = & $tables[$itable]['columns'];

        foreach($topics_ids as $topic_id) {
            unset($squery);
            $squery = new DB_Select($stable);
            $squery->addSelectField($topic_id);
            $squery->addSelectField($scolumns['email_id']);
            $squery->Where($scolumns['action_key'], DB_EQ, DBQuery::quoteValue($key));

            unset($iquery);
            $iquery = new DB_Insert_Select($itable);
            $iquery->setModifiers(DB_IGNORE);
            $iquery->setInsertFields(array('topic_id', 'email_id'));
            $iquery->setSelectQuery($squery);

            $application->db->getDB_Result($iquery);
        }
    }

    function unsubscribeTempEmails($key, $topics_ids)
    {
        global $application;
        if (empty($topics_ids)) {
            return;
        }

        $tables = Subscriptions::getTables();
        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $ttable = 'subscription_temp';
        $tcolumns = & $tables[$ttable]['columns'];

        $dquery = new DB_Delete($etable);
        $dquery->addUsingTable($etable);
        $dquery->addUsingTable($ttable);

        $dquery->WhereValue($tcolumns['action_key'], DB_EQ, $key);
        $dquery->WhereAND();
        $dquery->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $dquery->WhereAND();
        $dquery->WhereField($ecolumns['email_id'], DB_EQ, $tcolumns['email_id']);
        $application->db->getDB_Result($dquery);
    }

    function cleanTempEmails($key)
    {
        global $application;

        $tables = Subscriptions::getTables();
        $table = 'subscription_temp';
        $columns = & $tables[$table]['columns'];

        $dquery = new DB_Delete($table);
        $dquery->WhereValue($columns['action_key'], DB_EQ, $key);
        $application->db->getDB_Result($dquery);
    }

    //

    function getSubscriptionKey()
    {
        /*global $application;

        $tables = Subscriptions::getTables();
        $table = 'subscription_guest';
        $columns = & $tables[$table]['columns'];*/

        while(1) {
            $key = _ml_substr(md5(time().mt_rand(1, 0x7fffffff)), 0, SUBSCRIPTION_KEY_SIZE);
            /*$query = new DB_Select($table);
            $query->addSelectField('subscription_key');
            $query->WhereValue($columns['subscription_key'], DB_EQ, $key);
            $query->SelectLimit(0, 1);

            $result = $application->db->getDB_Result($query);*/
            $result = execQuery("SUBSCR_GET_SUBSCRIPTION_EMAIL_BY_KEY", array("key" => $key, "limit" => "yes"));
            if (sizeof($result) == 0) {
                break;
            }
        }
        return $key;

    }

    function setSubscriptionKey($key, $email)
    {
        global $application;

        $tables = Subscriptions::getTables();
        $table = 'subscription_guest';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Replace($table);
        $query->addReplaceValue($key, $columns['subscription_key']);
        $query->addReplaceValue($email, $columns['email']);
        $query->addReplaceValue(time(), $columns['updated']);
        $application->db->getDB_Result($query);
    }

    function getKeyByEmail($email)
    {
        global $application;

        $tables = $this->getTables();
        $table = 'subscription_guest';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Select($table);
        $query->addSelectField($columns['subscription_key']);
        $query->WhereValue($columns['email'], DB_EQ, $email);
        $result = $application->db->getDB_Result($query);

        if ($result) {
            $key = $result[0]['subscription_key'];
        }
        else {
            $key = $this->getSubscriptionKey();
        }

        return $key;
    }

    function getEmailByKey($key)
    {
        $result = execQuery("SUBSCR_GET_SUBSCRIPTION_EMAIL_BY_KEY", array("key" => $key));
        return $result ? $result[0]['email'] : null;
    }

    function setCustomerSubscribedEmail($email)
    {
        if (isset($_COOKIE[SUBSCRIBE_COOKIE])) {
            $key = $_COOKIE[SUBSCRIBE_COOKIE];
        }
        else {
            $key = $this->getKeyByEmail($email);
            setcookie(SUBSCRIBE_COOKIE, $key, time()+SUBSCRIBE_COOKIE_LIVE);
        }
        $this->setSubscriptionKey($key, $email);
    }

    function getCustomerSubscriptionEmails($account)
    {
    	$emails = array();
        if (! empty($account)) {
        	$res = execQuery('SUBSCR_GET_SUBSCRIPTION_EMAILS', array('account' => $account));
        	foreach ($res as $r) {
        		$emails[] = $r['email'];
        	}
        }
    	return $emails;
    }

    function getCustomerSubscribedEmail()
    {
        $email = null;
        $customer = modApiFunc('Customer_Account', 'getCurrentSignedCustomer');
        if (! empty($customer)) {
            $res = execQuery('SUBSCR_GET_SUBSCRIPTION_EMAIL_BY_CUSTOMER', array('customer' => $customer));
            if (! empty($res)) {
                $email = $res[0]['email'];
            }
        }
        if (empty($email) && isset($_COOKIE[SUBSCRIBE_COOKIE])) {
            $email = $this->getEmailByKey($_COOKIE[SUBSCRIBE_COOKIE]);
        }
        return $email;
    }

    function linkSubscriptionToCustomer()
    {
        global $application;
        $email = $this->getCustomerSubscribedEmail();
        $account = modApiFunc('Customer_Account', 'getCurrentSignedCustomer');
        if (empty($email) || empty($account)) {
            return;
        }
        $params = array(
                'account' => $account,
                'email' => $email,
                );
        execQuery('SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER', $params);
    }

    //

    function getCustomerTopics($signed_id)
    {
        $access = array(SUBSCRIPTION_TOPIC_FULL_ACCESS,
                $signed_id ? SUBSCRIPTION_TOPIC_REGISTERED_ONLY : SUBSCRIPTION_TOPIC_GUEST_ONLY);

        return execQuery('SUBSCR_SELECT_CUSTOMER_TOPICS', array('status' => SUBSCRIPTION_TOPIC_ACTIVE, 'access' => $access));
    }

    /*
    function getEmailId($email)
    {
        global $application;

        $tables = $this->getTables();
        $table = 'email_address';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Select($table);
        $query->addSelectField($columns['email_id']);
        $query->WhereValue($columns['email'], DB_EQ, $email);

        $res = $application->db->getDB_Result($query);

        return $res ? $res[0]['email_id'] : null;
    }
    */

    function getSubscribedTopics($email, $signed_id = null, $active_only = false)
    {
        global $application;

        $tables = $this->getTables();

        $ttable = 'subscription_topic';
        $tcolumns = & $tables[$ttable]['columns'];

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $atable = 'email_address';
        $acolumns = & $tables[$atable]['columns'];

        $query = new DB_Select();
        $query->addSelectTable($ttable);
        $query->addSelectField($tcolumns['topic_id']);
        $query->SelectGroup($tcolumns['topic_id']);

        $query->addInnerJoin($etable, $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        if (is_numeric($email)) {
            $query->WhereValue($ecolumns['email_id'], DB_EQ, $email);
        }
        else {
            $query->addInnerJoin($atable, $acolumns['email_id'], DB_EQ, $ecolumns['email_id']);
            $query->WhereValue($acolumns['email'], DB_EQ, $email);
        }
        if (isset($signed_in)) {
            $query->WhereAND();
            $access = array(SUBSCRIPTION_TOPIC_FULL_ACCESS,
                    $signed_id ? SUBSCRIPTION_TOPIC_REGISTERED_ONLY : SUBSCRIPTION_TOPIC_GUEST_ONLY);
            $query->Where($tcolumns['topic_access'], DB_IN, DBQuery::arrayToIn($access));
        }
        if ($active_only) {
            $query->WhereAND();
            $query->WhereValue($tcolumns['topic_status'], DB_EQ, SUBSCRIPTION_TOPIC_ACTIVE);
        }

        $res = $application->db->getDB_Result($query);

        $ids = array();
        foreach($res as $r) {
            $ids[ $r['topic_id'] ] = $r['topic_id'];
        }

        return $ids;
    }

    function getSendTopics()
    {
        return execQuery('SUBSCR_SELECT_SEND_TOPICS', array('access' => array(SUBSCRIPTION_TOPIC_ACTIVE, SUBSCRIPTION_TOPIC_CANNOT_SUBSCRIBE)));
    }

    //

    function setLetterTopics($letter_id, $topics_ids)
    {
        global $application;
        if (! is_array($topics_ids)) {
            $topics_ids = array($topics_ids);
        }

        $tables = $this->getTables();
        $ntables = modApiFunc('Newsletter', 'getTables');

        $ltable = 'newsletter_topics';
        $lcolumns = & $ntables[$ltable]['columns'];

        $dquery = new DB_Delete($ltable);
        $dquery->WhereValue($lcolumns['letter_id'], DB_EQ, $letter_id);
        $application->db->getDB_Result($dquery);

        $iquery = new DB_Multiple_Insert($ltable);
        $iquery->setModifiers(DB_IGNORE);
        $iquery->setInsertFields(array('letter_id', 'topic_id'));
        foreach($topics_ids as $topic_id) {
            $iquery->addInsertValuesArray(array('letter_id' => $letter_id, 'topic_id' => $topic_id));
        }
        $application->db->getDB_Result($iquery);
    }

    function getLetterTopics($letter_id)
    {
        global $application;

        $tables = $this->getTables();
        $ntables = modApiFunc('Newsletter', 'getTables');

        $ltable = 'newsletter_topics';
        $lcolumns = & $ntables[$ltable]['columns'];

        $query = new DB_Select($ltable);
        $query->addSelectField($lcolumns['topic_id']);
        $query->WhereValue($lcolumns['letter_id'], DB_EQ, $letter_id);
        $res = $application->db->getDB_Result($query);

        $ids = array();
        foreach($res as $r) {
            $ids[ $r['topic_id'] ] = $r['topic_id'];
        }

        return $ids;
    }

    function getLettersTopicsNames($letters_ids)
    {
        if (empty($letters_ids)) {
            return array();
        }

        $res = execQuery('SUBSCR_SELECT_TOPIC_BY_LETTERS', array('letters_ids' => $letters_ids));

        $names = array();
        foreach (array_keys($res) as $i) {
            $r =& $res[$i];
            $names[ $r['letter_id'] ][ $r['topic_id'] ] = $r['topic_name'];
        }
        return $names;
    }

    function getLettersTopicsToSend($letters_ids)
    {
        if (empty($letters_ids)) {
            return array();
        }

        $res = execQuery('SUBSCR_SELECT_TOPIC_BY_LETTERS_TO_SEND', array('letters_ids' => $letters_ids));

        $names = array();
        foreach (array_keys($res) as $i) {
            $r =& $res[$i];
            $names[ $r['letter_id'] ][ $r['topic_id'] ] = $r;
        }
        return $names;
    }

    function getLettersEmailsCount($letters_ids)
    {
        global $application;
        if (empty($letters_ids)) {
            return array();
        }

        $tables = $this->getTables();
        $ntables = modApiFunc('Newsletter', 'getTables');

        $ltable = 'newsletter_topics';
        $lcolumns = & $ntables[$ltable]['columns'];

        $ttable = 'subscription_topic';
        $tcolumns = & $tables[$ttable]['columns'];

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $query = new DB_Select($ltable);
        $query->addSelectField($lcolumns['letter_id']);
        $query->addSelectField($ecolumns['email_id']);
        $query->addInnerJoin($ttable, $lcolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $query->addSelectField(DB_Select::fCountDistinct($ecolumns['email_id']), 'topic_emails');
        $query->addLeftJoin($etable, $ecolumns['topic_id'], DB_EQ, $tcolumns['topic_id']);
        $query->Where($lcolumns['letter_id'], DB_IN, DBQuery::arrayToIn($letters_ids));
        $query->SelectGroup($lcolumns['letter_id']);
        $res = $application->db->getDB_Result($query);

        $counts = array();
        foreach (array_keys($res) as $i) {
            $r =& $res[$i];
            $counts[ $r['letter_id'] ] = $r['topic_emails'];
        }
        return $counts;
    }

    function getTopicsEmailsCount($topics_ids, $unique = true)
    {
        global $application;
        if (empty($topics_ids)) {
            return array();
        }

        $tables = $this->getTables();

        $etable = 'subscription_email';
        $ecolumns = & $tables[$etable]['columns'];

        $query = new DB_Select($etable);
        if ($unique) {
            $query->addSelectField(DB_Select::fCountDistinct($ecolumns['email_id']), 'email_count');
        }
        else {
            $query->addSelectField(DB_Select::fCount($ecolumns['email_id']), 'email_count');
        }
        $query->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $res = $application->db->getDB_Result($query);

        return $res[0]['email_count'];
    }

    // auto subscribe

    function onCustomerRegistered($reg_data)
    {
    	// link subscription address to the customer account
        $email = $reg_data['info']['Email'];
        if (! modApiFunc('Users', 'isValidEmail', $email)) {
            return;
        }
        $params = array(
                'account' => $reg_data['account'],
                'email' => $email,
                );
        execQuery('SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER', $params);

        // subscribe to auto-subscription topics
        $topics_ids = execQuery('SUBSCR_GET_AUTO_SUBSCRIBE_TOPICS', null);
        if (empty($topics_ids)) {
            return;
        }
        foreach (array_keys($topics_ids) as $i) {
            $topics_ids[$i] = $topics_ids[$i]['topic_id'];
        }
        modApifunc('Subscriptions', 'subscribeEmails', $topics_ids, $email);
    }

    function onOrderCreated($order_id)
    {
        $email = modApiFunc('Checkout', 'getCustomerAttributeValue', $order_id, 'billinginfo', 'default', 'Email');
        if (! modApiFunc('Users', 'isValidEmail', $email)) {
            return;
        }

        Subscriptions::setCustomerSubscribedEmail($email);

        $mode = modApiFunc('Settings', 'getParamValue', 'SUBSCRIPTIONS', 'CHECKOUT_SUBSCRIBE_MODE');
        if ($mode == 'AUTO') {
            $signed_id = modApiFunc('Customer_Account', 'getCurrentSignedCustomer') !== null;
            $access = array(SUBSCRIPTION_TOPIC_FULL_ACCESS,
                    $signed_id ? SUBSCRIPTION_TOPIC_REGISTERED_ONLY : SUBSCRIPTION_TOPIC_GUEST_ONLY);

            $topics_ids = execQuery('SUBSCR_GET_AUTO_SUBSCRIBE_TOPICS', array('access' => $access));
            if (! empty($topics_ids)) {
                foreach (array_keys($topics_ids) as $i) {
                    $topics_ids[$i] = $topics_ids[$i]['topic_id'];
                }
                Subscriptions::subscribeEmails($topics_ids, $email);
            }
        }
        elseif ($mode == 'MANUAL') {
            $prerequisiteValidationResults = modApiFunc('Checkout', 'getPrerequisiteValidationResults', 'subscriptionTopics');
            $ids = @$prerequisiteValidationResults['validatedData']['Topics']['value'];
            $topics_ids = empty($ids) ? array() : explode(',', $ids);
            if (! empty($topics_ids)) {
                Subscriptions::subscribeEmails($topics_ids, $email);
            }
        }

        $order = execQuery('SELECT_BASE_ORDER_INFO', array('order_id' => $order_id));
        if (! empty($order)) {
            $params = array(
                'customer_id' => $order[0]['person_id'],
                'email' => $email,
            );
            execQuery('SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER', $params);
        }
    }

}

?>