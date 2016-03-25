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

define('PORTION_MAX_EXPORT_TIME', 1);
define('PORTION_MAX_MESSAGES_NUM', 50);
//define('MESSAGES_PER_MINUTE', 30);

/**
 * @package Newsletter
 * @author Egor Makarov
 */
class Newsletter
{
	/**#@+
     * @access public
     */

    function Newsletter()
    {
    }

    /**
     *                                 ,
     * <code>
     * array (
     *    [0] => array (
     *              'letter_id' => 1
     *             ,'letter_creation_date' => '2007-05-09 ...'
     *             ,'letter_sent_date' => '2007-05-09 ...' or ''
     *             ,'letter_subject' => 'New Special Offers'
     *           ),
     *    ...
     * )
     * </code>
     * @return array description
     */
    function getMessagesList()
    {
        $res = execQuery('NLT_SELECT_LIST_OF_MESSAGES', array());

        $messages = array();

        foreach($res as $msg)
        {
            $messages[] = array (
                'letter_id' => $msg['letter_id']
                ,'letter_creation_date' => $msg['letter_creation_date']
                ,'letter_sent_date' => $msg['letter_sent_date']
                ,'letter_subject' => $msg['letter_subject']
                ,'letter_from_name' => $msg['letter_from_name']
                ,'letter_from_email' => $msg['letter_from_email']
            );
        }

        return $messages;
    }

    /**
     *
     * <code>
     * array(
     *   'letter_id' => 1
     *  ,'letter_sent_date' => '2007-05-09' or ''
     *  ,'letter_subject' => 'New Special Offers'
     *  ,'letter_from_name' => 'John Doe'
     *  ,'letter_from_email' => 'john@sales.example.com'
     *  ,'letter_html' => ' html content '
     *  //,'letter_text' => ' plain text content '
     * )
     * </code>
     * @param number id
     */
    function getMessageInfo($id_message)
    {
        $result = execQuery('NLT_SELECT_MESSAGE_INFO', array('id_message' => $id_message));

        if (count($result) == 0)
        {
            return null;
        }

        $message_info = $result[0];
        return $message_info;
    }

    /**
     *
     * @return number id                   .
     */
    function addMessage($message_info)
    {
    	global $application;
        $message_info['date'] = date('Y-m-d G:i:s');
        execQuery('NLT_INSERT_MESSAGE', $message_info);
        $mysql = &$application -> getInstance('DB_MySQL');
        $result = $mysql -> DB_Insert_Id();
        return $result;
    }

    /**
     *                                                 .
     * @param number $id_message Id                            .
     * @param array $message_info                          .
     */
    function updateMessage($id_message, $message_info)
    {
        $message_info['id_message'] = $id_message;
        return execQuery('NLT_UPDATE_MESSAGE', $message_info);
    }

    /**
     *                        , id                             -         .
     * @param number $id_messages id
     */
    function deleteMessages($id_messages)
    {
    	global $application;

        if (empty($id_messages))
        {
            return;
        }

        if (!is_array($id_messages))
            $id_messages = array($id_messages);

        execQuery('NLT_DELETE_MESSAGE', array('ids' => $id_messages));

        $tables = $this -> getTables();
        $table = 'newsletter_topics';
        $query = new DB_Delete($table);
        $query->Where($tables[$table]['columns']['letter_id'], DB_IN, DBQuery::arrayToIn($id_messages));
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();
    }

    function getUnsubscribeRecord($key_unsubscribe)
    {
        global $application;
        $tables = $this->getTables();
        $table = 'newsletter_unsubscribe';

        $query = new DB_Select($table);
        $query->addSelectField('*');
        $query->WhereValue($tables[$table]['columns']['key_unsubscribe'], DB_EQ, $key_unsubscribe);
        $result = $application->db->getDB_Result($query);
        return $result ? $result[0] : null;
    }

    /**
     *                                          .
     *                  ,                                          .
     *                       :
     * <code>
     * array (
     *   'Errors' => array ( ..        ..)
     *  ,'Warnings' => array ( ..                ..)
     *  ,'TotalCount' =>                ,                             .
     * )
     * </code>
     *
     * @return array                  ,
     */
    function prepareSendMessage($letter_id)
    {
        global $application;

        $this->_currentMessage = $this->getMessageInfo($letter_id);

        //
        //
        //
        $table = 'newsletter_temp';
        $tables = $this->getTables();

        $query = new DB_Delete($table);
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        //
        //
        //
        $emails_list = modApiFunc('Customer_Account','getAllCustomerEmails');

        $this->_totalRecipients = count($emails_list);
        $this->_sentCountTotal = 0;

        $result = array (
                    'Errors' => array()
                   ,'Warnings' => array()
                   ,'TotalCount' => $this->_totalRecipients
                   ,'EmailsList' => $emails_list
                  );

        return $result;
    }

    /**
     *                                          .
     *                  ,                                          .
     *                       :
     * <code>
     * array (
     *   'Errors' => array ( ..        ..)
     *  ,'Warnings' => array ( ..                ..)
     *  ,'TotalCount' =>                ,                             .
     * )
     * </code>
     *
     * @return array                  ,
     */
    function prepareSendMessage2($letter_id, $NewSelectedModules)
    {
        global $application;

        $this->_currentMessage = $this->getMessageInfo($letter_id);

        //
        //
        //
        $table = 'newsletter_temp';
        $tables = $this->getTables();

        $query = new DB_Delete($table);
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        //
        //
        //
        $emails_list = modApiFunc('Customer_Account','getAllCustomerEmails');

        $selected_list = array_intersect($NewSelectedModules, $emails_list);

        $counter = 0;
        foreach ($selected_list as $email)
        {
            //                                  .
            $query = new DB_Insert($table);
            $query->addInsertValue($email, $tables[$table]['columns']['recipient_value']);
            $query->addInsertValue(++$counter, $tables[$table]['columns']['recipient_num']);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        }

        $this->_totalRecipients = count($selected_list);
        $this->_sentCountTotal = 0;

        $result = array (
                    'Errors' => array()
                   ,'Warnings' => array()
                   ,'TotalCount' => $this->_totalRecipients
                   ,'EmailsList' => $selected_list
                  );

        return $result;
    }

    function getNextNum()
    {
        global $application;

        $tables = $this->getTables();

        $table = 'newsletter_unsubscribe';
        $columns = $tables[$table]['columns'];

        $query = new DB_Select($table);
        $query->addSelectField(DB_Select::fMax($columns['delivery_num']), 'num');
        $res = $application->db->getDB_Result($query);
        return $res[0]['num'] + 1;
    }

    function countTempEmails($num)
    {
        global $application;

        $ntables = $this->getTables();
        $itable = 'newsletter_temp';
        $icolumns = $ntables[$itable]['columns'];

        $squery = new DB_Select($itable);
        $squery->addSelectField(DB_Select::fCount($icolumns['recipient_value']), 'emails_count');
        $squery->WhereValue($icolumns['recipient_num'], DB_EQ, $num);
        $res = $application->db->getDB_Result($squery);

        return $res[0]['emails_count'];
    }

    function createUnsubscribeKeys($letter_id, $delivery_num, $topics_ids)
    {
        global $application;

        $str_topics_ids = implode(',', $topics_ids);

        // copy emails to unsubscribe table
        $ntables = $this->getTables();
        $stables = modApiFunc('Subscriptions', 'getTables');

        $utable = 'newsletter_unsubscribe';
        $ucolumns = $ntables[$utable]['columns'];

        $ltable = 'newsletter_topics';
        $lcolumns = $ntables[$ltable]['columns'];

        $etable = 'subscription_email';
        $ecolumns = $stables[$etable]['columns'];

        $atable = 'email_address';
        $acolumns = $stables[$atable]['columns'];

        $iquery = new DB_Insert_Select($utable);
        $iquery->setModifiers(DB_IGNORE);
        $iquery->setInsertFields(array('key_unsubscribe', 'delivery_num', 'letter_id', 'email_id', 'topics_ids'));

        $squery = new DB_Select($etable);
        $squery->addSelectField('MD5(CONCAT("'.$delivery_num.'", "'.$letter_id.'", "'.$str_topics_ids.
                '", NOW(), '.$ecolumns['email_id'].', '.$acolumns['email'].'))');
        $squery->addSelectField($delivery_num.'-0', 'delivery_num');
        $squery->addSelectField($letter_id.'+0', 'letter_id');
        $squery->addSelectField($ecolumns['email_id']);
        $squery->addSelectField(DBQuery::quoteValue($str_topics_ids));

        $squery->addInnerJoin($atable, $ecolumns['email_id'], DB_EQ, $acolumns['email_id']);
        $squery->Where($ecolumns['topic_id'], DB_IN, DBQuery::arrayToIn($topics_ids));
        $squery->SelectGroup($ecolumns['email_id']);
        $squery->SelectOrder($ecolumns['email_id'], 'ASC');

        $iquery->setSelectQuery($squery);

        $application->db->getDB_Result($iquery);

        $this->_totalRecipients = $this->countTempEmails($delivery_num);
        $this->_sentCountTotal = 0;

        $result = array (
                'Errors' => array($application->db->_getSQL($iquery)),
                'Warnings' => array(),
                'TotalCount' => $this->_totalRecipients,
                'Num' => $delivery_num,
                );

        return $result;

    }

    function prepareSendMessage3($letter_id)
    {
        global $application;

        $delivery_num = $this->getNextNum();
        $topics_ids = modApiFunc('Subscriptions', 'getLetterTopics', $letter_id);
        if (! $topics_ids) {
            return;
        }

        $this->createUnsubscribeKeys($letter_id, $delivery_num, $topics_ids);

        $this->_currentMessage = $this->getMessageInfo($letter_id);

        // copy emails to temp table
        $ntables = $this->getTables();
        $stables = modApiFunc('Subscriptions', 'getTables');

        $itable = 'newsletter_temp';
        $icolumns = $ntables[$itable]['columns'];

        $utable = 'newsletter_unsubscribe';
        $ucolumns = $ntables[$utable]['columns'];

        $atable = 'email_address';
        $acolumns = $stables[$atable]['columns'];

        $iquery = new DB_Insert_Select($itable);
        $iquery->setModifiers(DB_IGNORE);
        $iquery->setInsertFields(array('recipient_num', 'recipient_value', 'key_unsubscribe', 'lng'));

        $squery = new DB_Select($atable);
        $squery->addSelectField($delivery_num);
        $squery->addSelectField($acolumns['email']);
        $squery->addSelectField($ucolumns['key_unsubscribe']);
        $squery->addSelectField($acolumns['lng']);

        $squery->addInnerJoin($atable, $acolumns['email_id'], DB_EQ, $ucolumns['email_id']);
        $squery->WhereValue($ucolumns['delivery_num'], DB_EQ, $delivery_num);
        $squery->SelectOrder($ucolumns['email_id'], 'ASC');

        $iquery->setSelectQuery($squery);

        $application->db->getDB_Result($iquery);

        $this->_totalRecipients = $this->countTempEmails($delivery_num);
        $this->_sentCountTotal = 0;

        $result = array (
                'Errors' => array($application->db->_getSQL($iquery)),
                'Warnings' => array(),
                'TotalCount' => $this->_totalRecipients,
                'Num' => $delivery_num,
                );

        return $result;
    }

    function sendMessagesPortion3($num)
    {
        global $application;

        loadCoreFile('ascHtmlMimeMail.php');
        $mailer = new ascHtmlMimeMail();

        $tables = $this->getTables();
        $table = 'newsletter_temp';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Select($table);
        $query->addSelectField($columns['recipient_value']);
        $query->addSelectField($columns['key_unsubscribe']);
        $query->addSelectField($columns['lng']);
        $query->WhereValue($columns['recipient_num'], DB_EQ, $num);
        $query->SelectOrder($columns['recipient_id'], 'ASC');
        $query->SelectLimit(0, PORTION_MAX_MESSAGES_NUM);

        $res = $application->db->getDB_Result($query);

        $addr_num = count($res);

        $start_time = $this->microtime_float();
        $sent_count = 0;

        // getting the default language
        $default_language = modApiFunc('MultiLang', 'getDefaultLanguage');
        // saving the current language
        $current_language = modApiFunc('MultiLang', 'getLanguage');

        // storing the current letter_id
        $letter_id = $this->_currentMessage['letter_id'];

        while ($this->microtime_float() - $start_time < PORTION_MAX_EXPORT_TIME && $sent_count < $addr_num)
        {
            //
            //
            //

            // setting the language
            if (!$res[$sent_count]['lng'])
                $res[$sent_count]['lng'] = $default_language;
            modApiFunc('MultiLang', 'setLanguage', $res[$sent_count]['lng']);

            // reading the newsletter for the language
            $this -> _currentMessage = $this -> getMessageInfo($letter_id);

            $from = $this->_currentMessage['letter_from_name']. ' <' . $this->_currentMessage['letter_from_email'] . '>';
            $mailer->setFrom($from);
            $mailer->setSubject($this->_currentMessage['letter_subject']);

            $html_tmpl = "<html><head><title>{$this->_currentMessage['letter_subject']}</title></head><body>{$this->_currentMessage['letter_html']}</body></html>";
            $html_log = str_replace('%KEY_UNSUBSCRIBE%', $res[$sent_count]['key_unsubscribe'], $this->_currentMessage['letter_html']);

            $mailer->setHtml(str_replace('%KEY_UNSUBSCRIBE%', $res[$sent_count]['key_unsubscribe'], $html_tmpl));
            $result = $mailer->send(array($res[$sent_count]['recipient_value']));
            $mailer->resetMessageBuilt();
            $this->addNewsletterToTimeline($res[$sent_count]['recipient_value'],
                    $this->_currentMessage['letter_subject'], $html_log, $result);
            $sent_count++;
            // :
            /*debug*/ //usleep(200000);
        }

        // restoring the current language
        modApiFunc('MultiLang', 'setLanguage', $current_language);

        if ($sent_count) {
            $this->_sentCountTotal += $sent_count;
            $this->removeEmails($num, $sent_count);
        }
        if ($this->_sentCountTotal < $this->_totalRecipients) {
            $sending_status = 'PROCESSING';
        }
        else {
            $sending_status = 'COMPLETED';
            $this->_sentCountTotal = $this->_totalRecipients;
            $this->updateMessage($this->_currentMessage['letter_id'], array('letter_sent_date' => date('Y-m-d G:i:s')));
        }

        return array (
            'errors' => '',
            'warnings' => '',
            'sent_total' => $this->_sentCountTotal,
            'sending_status' => $sending_status,
        );

    }

    function removeEmails($num, $emails_count)
    {
        global $application;
        $tables = $this->getTables();
        $table = 'newsletter_temp';

        $query = new DB_Delete($table);
        $query->WhereValue($tables[$table]['columns']['recipient_num'], DB_EQ, $num);
        $query->DeleteOrder($tables[$table]['columns']['recipient_id'], 'ASC');
        $query->DeleteLimit($emails_count);
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();
    }

    /**
     *                             .                                  PORTION_MAX_EXPORT_TIME     .
     */
    function sendMessagesPortion()
    {
        global $application;

        loadCoreFile('ascHtmlMimeMail.php');
        $mailer = new ascHtmlMimeMail();
        $from = $this->_currentMessage['letter_from_name']. ' <' .
            $this->_currentMessage['letter_from_email'] . '>';
        $mailer->setFrom($from);
        $mailer->setSubject($this->_currentMessage['letter_subject']);

        $html = "<html><head><title>{$this->_currentMessage['letter_subject']}</title></head><body>{$this->_currentMessage['letter_html']}</body></html>";
        $mailer->setHtml($html/*, $this->_currentMessage['body_text']*/);

        $start_time = $this->microtime_float();
        $sent_count = 0;

        //
        //                           $max_to_send
        //
        $table = 'newsletter_temp';
        $tables = $this->getTables();

        //          PORTION_MAX_MESSAGES_NUM          ,                  (_sentCountTotal + 1)
        //                                 ,     PORTION_MAX_MESSAGES_NUM
        $query = new DB_Select();
        $query->addSelectTable($table);
        $query->addSelectField($tables[$table]['columns']['recipient_value']);
        $query->addWhereOpenSection();
        $query->WhereValue($tables[$table]['columns']['recipient_num'], DB_GTE, $this->_sentCountTotal + 1);
        $query->WhereAND();
        $query->WhereValue($tables[$table]['columns']['recipient_num'], DB_LTE, $this->_sentCountTotal + PORTION_MAX_MESSAGES_NUM);
        $query->addWhereCloseSection();
        $res = $application->db->getDB_Result($query);
        $addr_num = count($res);

        while ($this->microtime_float() - $start_time < PORTION_MAX_EXPORT_TIME && $sent_count < $addr_num)
        {
            //
            //
            //
            $mailer->send(array($res[$sent_count]['recipient_value']));
            $sent_count++;
            // :
            /*debug*/ //usleep(200000);
        }

        $this->_sentCountTotal += $sent_count;
        $sending_status = 'PROCESSING';

        if ($this->_sentCountTotal >= $this->_totalRecipients)
        {
        	$this->_sentCountTotal = $this->_totalRecipients;
        	//
            //
            //
            $table = 'newsletter_temp';
            $tables = $this->getTables();

            $query = new DB_Delete($table);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();

            $this->updateMessage($this->_currentMessage['letter_id'], array('letter_sent_date' => date('Y-m-d G:i:s')));
            $sending_status = 'COMPLETED';
        }


        return array (
            'errors' => ''
           ,'warnings' => ''
           ,'sent_total' => $this->_sentCountTotal
           ,'sending_status' => $sending_status
        );
    }

    function loadState()
    {
        global $application;
        if (modApiFunc('Session', 'is_set', 'newsletter_current'))
        {
            $this->_currentMessage = modApiFunc('Session', 'get', 'newsletter_current');
        }
        else
        {
            $this->_currentMessage = null;
        }

        if (modApiFunc('Session', 'is_set', 'newsletter_sent'))
        {
        	$this->_sentCountTotal = modApiFunc('Session', 'get', 'newsletter_sent');
        }
        else
        {
        	$this->_sentCountTotal = 0;
        }

        if (modApiFunc('Session', 'is_set', 'newsletter_total'))
        {
        	$this->_totalRecipients = modApiFunc('Session', 'get', 'newsletter_total');
        }
        else
        {
        	$this->_totalRecipients = 0;
        }
    }

    function saveState()
    {
        global $application;
        if ($this->_currentMessage == null)
        {
            if (modApiFunc('Session', 'is_set', 'newsletter_current'))
            {
                modApiFunc('Session', 'un_set', 'newsletter_current');
            }
        }
        else
        {
            modApiFunc('Session', 'set', 'newsletter_current', $this->_currentMessage);
        }

        if ($this->_sentCountTotal == null)
        {
        	if (modApiFunc('Session', 'is_set', 'newsletter_sent'))
            {
            	modApiFunc('Session', 'un_set', 'newsletter_sent');
            }
        }
        else
        {
        	modApiFunc('Session', 'set', 'newsletter_sent', $this->_sentCountTotal);
        }

        if ($this->_totalRecipients == null)
        {
        	if (modApiFunc('Session', 'is_set', 'newsletter_total'))
            {
            	modApiFunc('Session', 'un_set', 'newsletter_total');
            }
        }
        else
        {
        	modApiFunc('Session', 'set', 'newsletter_total', $this->_totalRecipients);
        }
    }

    function install()
    {
        _use(dirname(__FILE__) . "/includes/install.inc");
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Newsletter::getTables());
    }

    function getSettings()
    {
        global $application;
        $tables = $this->getTables();

        $query = new DB_Select();
        $query->addSelectTable('newsletter_settings');
        $query->addSelectField('*');
        $res=$application->db->getDB_Result($query);

        $settings=array();

        foreach($res as $k => $sval)
            $settings[$sval['setting_key']] = $sval['setting_value'];

        return $settings;
    }

    function updateSettings($settings)
    {
        global $application;
        $tables = $this->getTables();
        $stable = $tables['newsletter_settings']['columns'];

        foreach($settings as $skey => $sval)
        {
            $query = new DB_Update('newsletter_settings');
            $query->addUpdateValue($stable['setting_value'],$sval);
            $query->WhereValue($stable['setting_key'], DB_EQ, $skey);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };
    }

    function getTables()
    {
        global $application;

        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array();

        //
        $table = 'newsletter_settings';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'setting_id'    => $table.'.setting_id'
           ,'setting_key'   => $table.'.setting_key'
           ,'setting_value' => $table.'.setting_value'
        );
        $tables[$table]['types'] = array(
            'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
        );
        $tables[$table]['primary'] = array(
            'setting_id'
        );

        //
        $table = 'newsletter_letters';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'letter_id'    => $table.'.letter_id'
           ,'letter_subject' => $table.'.letter_subject'
           ,'letter_from_name'   => $table.'.letter_from_name'
           ,'letter_from_email' => $table.'.letter_from_email'
           ,'letter_html' => $table.'.letter_html'
           ,'letter_text' => $table.'.letter_text'
           ,'letter_sent_date'  => $table.'.letter_sent_date'
           ,'letter_creation_date' => $table.'.letter_creation_date'
        );
        $tables[$table]['types'] = array(
            'letter_id'    => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
           ,'letter_subject' => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
           ,'letter_from_name'   => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
           ,'letter_from_email' => DBQUERY_FIELD_TYPE_CHAR100 . ' NOT NULL DEFAULT \'\''
           ,'letter_html' => DBQUERY_FIELD_TYPE_LONGTEXT
           ,'letter_text' => DBQUERY_FIELD_TYPE_LONGTEXT
           ,'letter_sent_date'  => DBQUERY_FIELD_TYPE_DATETIME
           ,'letter_creation_date' => DBQUERY_FIELD_TYPE_DATETIME . ' NOT NULL DEFAULT \'0000-00-00 00:00\''
        );
        $tables[$table]['primary'] = array(
            'letter_id'
        );

        //
        $table = 'newsletter_unsubscribe';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'key_unsubscribe'   => $table.'.key_unsubscribe',
            'delivery_num'      => $table.'.delivery_num',
            'letter_id'         => $table.'.letter_id',
            'email_id'          => $table.'.email_id',
            'topics_ids'        => $table.'.topics_ids',
        );
        $tables[$table]['types'] = array(
            'key_unsubscribe'   => DBQUERY_FIELD_TYPE_CHAR32 . ' NOT NULL',
            'delivery_num'      => DBQUERY_FIELD_TYPE_INT . ' NOT NULL',
            'letter_id'         => DBQUERY_FIELD_TYPE_INT . ' NOT NULL',
            'email_id'          => DBQUERY_FIELD_TYPE_INT . ' NOT NULL',
            'topics_ids'        => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL',
        );
        $tables[$table]['primary'] = array(
            'key_unsubscribe'
        );
        $tables[$table]['indexes'] = array(
            'IDX_num'  => 'delivery_num, email_id',
        );

        //
        $table = 'newsletter_temp';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'recipient_id'    => $table.'.recipient_id'
           ,'recipient_num'   => $table.'.recipient_num'
           ,'recipient_value' => $table.'.recipient_value'
           ,'key_unsubscribe' => $table.'.key_unsubscribe'
           ,'lng'             => $table.'.lng'
        );
        $tables[$table]['types'] = array(
            'recipient_id'    => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
           ,'recipient_num'   => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
           ,'recipient_value' => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
           ,'key_unsubscribe' => DBQUERY_FIELD_TYPE_CHAR32 . ' NOT NULL DEFAULT \'\''
           ,'lng'             => DBQUERY_FIELD_TYPE_CHAR2
        );
        $tables[$table]['primary'] = array(
            'recipient_id'
        );
        $tables[$table]['indexes'] = array(
            'IDX_num'   => 'recipient_num, recipient_id',
        );

        //
        $table = 'newsletter_topics';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'letter_id' => $table.'.letter_id',
            'topic_id'  => $table.'.topic_id',
        );
        $tables[$table]['types'] = array(
            'letter_id' => DBQUERY_FIELD_TYPE_INT . ' NOT NULL',
            'topic_id'  => DBQUERY_FIELD_TYPE_INT . ' NOT NULL',
        );
        $tables[$table]['primary'] = array(
        );
        $tables[$table]['indexes'] = array(
            'UNIQUE KEY UNQ_topic'     => 'letter_id, topic_id',
            'IDX_topic' => 'topic_id',
        );

        return $application->addTablePrefix($tables); #add a prefix to table names
    }


    function addNewsletterToTimeline($to, $subject, $html, $result)
    {
        if (modApiFunc('Settings','getParamValue','TIMELINE','LOG_SEND_NEWS') === 'NO') {
            return;
        }

        $tl_type = getMsg('NLT', 'NLT_TL_TYPE');

        $tl_header = strtr(getMsg('NLT', 'NLT_TL_HEADER'), array(
                '{SUBJ}' => prepareHTMLDisplay($subject),
                '{TO}' => prepareHTMLDisplay($to),
                ));

        $tl_header .= getMsg('NLT', $result ? 'NLT_TL_SUCCESS' : 'NLT_TL_FAILED');

        //        timeline                         "        ".
        //                       HTML    .
        //$tl_body = nl2br(prepareHTMLDisplay($html));

        modApiFunc('Timeline', 'addLog', $tl_type, $tl_header, $html);
    }

    /**#@+
     * @access private
     */

	function microtime_float()
	{
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

    var $_tables;

    //
    var $_currentMessage;

    //
    var $_sentCountTotal;

    var $_sendingStatus;

    var $_totalRecipients;
}
?>