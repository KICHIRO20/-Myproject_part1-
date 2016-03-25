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
 * @package Checkout
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataReaderDefault');

class DataReaderTopicsEmailsDB extends DataReaderDefault
{
    function DataReaderTopicsEmailsDB()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();
        $this->_settings = $settings;

        $topics_ids = explode(',', $settings['topics']);
        $this->emails_ids = modApiFunc('Subscriptions', 'getTopicsEmailsIdsByTopicsIds', $topics_ids);
        $this->topics_names = modApiFunc('Subscriptions', 'getTopicsNamesByIds', $topics_ids);

        $this->emails_count = 0;
        foreach(array_keys($this->emails_ids) as $i) {
            $this->emails_count += sizeof($this->emails_ids[$i]);
        }

        $this->_process_info['status'] = 'INITED';
        $this->_process_info['items_count'] = $this->emails_count;
        $this->_process_info['items_processing'] = 0;
    }

    function doWork()
    {
        $this->_process_info['items_count'] = $this->emails_count;
        $this->_process_info['status'] = 'HAVE_MORE_DATA';

        $data = null;
        $topics_ids = array_keys($this->emails_ids);

        if ($this->email_num < $this->emails_count) {
            $email_id = $this->getNextEmailId();
            if (isset($email_id)) {
                $email = modApiFunc("Subscriptions", "getEmailById", $email_id);
                $data = array(
                        'Topic Name' => $this->topics_names[ $topics_ids[$this->topic_num] ],
                        'E-mail' => $email,
                        );
            }
            else {
                $data = array(
                        'Topic Name' => '-',
                        'E-mail' => '-',
                        );
            }
            $this->email_num ++;
        }
        if ($this->email_num >= $this->emails_count) {
            $this->_process_info['status'] = 'NO_MORE_DATA';
        };
        $this->_process_info['items_processing'] = $this->email_num;
        return $data;
    }

    function getNextEmailId()
    {
        $email_id = null;
        $topics_ids = array_keys($this->emails_ids);
        $topics_count = sizeof($topics_ids);
        for (; $this->topic_num <= $topics_count; $this->topic_num++) {
            $emails_ids = & $this->emails_ids[ $topics_ids[$this->topic_num] ];
            if (sizeof($emails_ids) > 0) {
                $email_id = array_shift($emails_ids);
                break;
            }
        }
        return $email_id;
    }

    function finishWork()
    {
        $this->clearWork();
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataReaderEmailsSettings'))
            $this->_settings = modApiFunc('Session','get','DataReaderEmailsSettings');
        if(modApiFunc('Session','is_set','DataReaderTopicNum'))
            $this->topic_num = modApiFunc('Session','get','DataReaderTopicNum');
        if(modApiFunc('Session','is_set','DataReaderEmailNum'))
            $this->email_num = modApiFunc('Session','get','DataReaderEmailNum');
        if(modApiFunc('Session','is_set','DataReaderEmailsCount'))
            $this->emails_count = modApiFunc('Session','get','DataReaderEmailsCount');
        if(modApiFunc('Session','is_set','DataReaderEmailsIDs'))
            $this->emails_ids = modApiFunc('Session','get','DataReaderEmailsIDs');
        if(modApiFunc('Session','is_set','DataReaderTopicsNames'))
            $this->topics_names = modApiFunc('Session','get','DataReaderTopicsNames');
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataReaderEmailsSettings');
        modApiFunc('Session','un_set','DataReaderTopicNum');
        modApiFunc('Session','un_set','DataReaderEmailNum');
        modApiFunc('Session','un_set','DataReaderEmailsCount');
        modApiFunc('Session','un_set','DataReaderEmailsIDs');
        modApiFunc('Session','un_set','DataReaderTopicsNames');

        $this->_settings = null;
        $this->topic_num = 0;
        $this->email_num = 0;
        $this->emails_count = 0;
        $this->emails_ids = array();
        $this->topics_names = array();
    }

    function saveWork()
    {
        modApiFunc('Session', 'set', 'DataReaderEmailsSettings', $this->_settings);
        modApiFunc('Session', 'set', 'DataReaderTopicNum', $this->topic_num);
        modApiFunc('Session', 'set', 'DataReaderEmailNum', $this->email_num);
        modApiFunc('Session', 'set', 'DataReaderEmailsCount', $this->emails_count);
        modApiFunc('Session', 'set', 'DataReaderEmailsIDs', $this->emails_ids);
        modApiFunc('Session', 'set', 'DataReaderTopicsNames', $this->topics_names);
    }

    var $_settings;
    var $topic_num;
    var $email_num;
    var $emails_count;
    var $emails_ids;
    var $topics_names;

}

?>