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

class DataReaderEmailsDB extends DataReaderDefault
{
    function DataReaderEmailsDB()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();
        $this->_settings = $settings;

        $topics_ids = explode(',', $settings['topics']);
        $this->emails_ids = modApiFunc('Subscriptions', 'getEmailsIdsByTopicsIds', $topics_ids);

        $this->emails_count = sizeof($this->emails_ids);

        $this->_process_info['status'] = 'INITED';
        $this->_process_info['items_count'] = $this->emails_count;
        $this->_process_info['items_processing'] = 0;
    }

    function doWork()
    {
        $this->_process_info['items_count'] = $this->emails_count;
        $this->_process_info['status'] = 'HAVE_MORE_DATA';

        $data = null;

        if ($this->email_num < $this->emails_count) {
            $email_id = $this->getNextEmailId();
            if (isset($email_id)) {
                $data = array(
                        'email' => modApiFunc("Subscriptions", "getEmailById", $email_id),
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
        $email_id = array_shift($this->emails_ids);
        return $email_id;
    }

    function finishWork()
    {
        $this->clearWork();
    }

    function loadWork()
    {
        if(modApiFunc('Session', 'is_set', 'DataReaderEmailsSettings'))
            $this->_settings = modApiFunc('Session', 'get', 'DataReaderEmailsSettings');
        if(modApiFunc('Session', 'is_set', 'DataReaderEmailNum'))
            $this->email_num = modApiFunc('Session', 'get', 'DataReaderEmailNum');
        if(modApiFunc('Session', 'is_set', 'DataReaderEmailsIDs'))
            $this->emails_ids = modApiFunc('Session', 'get', 'DataReaderEmailsIDs');
        if(modApiFunc('Session', 'is_set', 'DataReaderEmailsCount'))
            $this->emails_count = modApiFunc('Session', 'get', 'DataReaderEmailsCount');
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataReaderEmailsSettings');
        modApiFunc('Session', 'un_set', 'DataReaderEmailNum');
        modApiFunc('Session', 'un_set', 'DataReaderEmailsIDs');
        modApiFunc('Session', 'un_set', 'DataReaderEmailsCount');

        $this->email_num = 0;
        $this->emails_ids = array();
        $this->emails_count = 0;
        $this->_settings = null;
    }

    function saveWork()
    {
        modApiFunc('Session', 'set', 'DataReaderEmailsSettings', $this->_settings);
        modApiFunc('Session', 'set', 'DataReaderEmailNum', $this->email_num);
        modApiFunc('Session', 'set', 'DataReaderEmailsIDs', $this->emails_ids);
        modApiFunc('Session', 'set', 'DataReaderEmailsCount', $this->emails_count);
    }

    var $emails_ids;
    var $emails_count;
    var $email_num;
    var $_settings;

}

?>