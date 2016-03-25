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

class subscribe extends AjaxAction
{
    function subscribe()
    {

    }

    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');

        $SessionPost = array();

        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;
        $nErrors = 0;

        $this->getEmails();

        if ($this->_valid_emails_count == 0) {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_FILL_EMAILS_TO_SUBSCRIBE';
            // @                       -               ?
            $SessionPost['ViewState']['ErrorFields'][] = 'emails_subscribe';
            $nErrors ++;
        }
        else {
            $SessionPost['action_key'] = $this->_action_key;
            $SessionPost['ViewState']['stage'] = 'confirm';
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('topics', modApiFunc('Request', 'getValueByKey', 'topics'));
        if (isset($this->_action_key)) {
            $request->setKey('action_key', $this->_action_key);
            $request->setKey('stage', 'confirm');
        }
        $application->redirect($request);
    }

    function getEmails()
    {
        $this->_action_key = modApiFunc('Subscriptions', 'getActionKey');
        $this->_valid_emails_count = 0;
        $this->_invalid_emails_count = 0;

        switch(modApiFunc('Request', 'getValueByKey', 'emails_from'))
        {
            case 'typed':
                $this->getPostEmails();
                break;
            case 'plain':
            case 'csv':
            	$this->importPlainFile();
                break;
            case 'db':
                $this->importCustomerDB();
        }
        if ($this->_valid_emails_count > 0) {
            modApiFunc('Subscriptions', 'linkTempEmails', $this->_action_key);
        }
        else {
            modApiFunc('Subscriptions', 'cleanTempEmails', $this->_action_key);
            unset($this->_action_key);
        }
    }

    function extractEmails($s)
    {
        return preg_split('/[\s\(\)<>,;:\\\"\[\]|]+/', $s);
    }

    function getPostEmails()
    {
        $emails = modApiFunc('Request', 'getValueByKey', 'typed_emails');
        $emails = $this->extractEmails($emails);
        $this->pushEmails($emails);
    }

    function importPlainFile()
    {
        // @ process upload errors
        if (isset($_FILES['emails_plain_file'])) {
            $this->pushEmails($this->extractEmails(
            		file_get_contents($_FILES['emails_plain_file']['tmp_name'])));
        }
        else {
            // @ internal error
        }
    }

    function pushEmails(&$emails)
    {
        global $application;
        $subscr = & $application->getInstance('Subscriptions');

        $valid_emails = array();
        $invalid_emails = array();
        foreach ($emails as $email) {
            $email = _ml_strtolower(trim($email));
            if (modApiFunc('Users', 'isValidEmail', $email)) {
                $valid_emails[] = $email;
            }
            elseif(! empty($email)) {
                $invalid_emails[] = $email;
                $this->_invalid_emails_count ++;
            }
            if (sizeof($valid_emails) >= MAX_EMAILS_AT_ONCE) {
                $subscr->addTempEmails($this->_action_key, $valid_emails);
                $this->_valid_emails_count += sizeof($valid_emails);
                $valid_emails = array();
            }
        }
        if (sizeof($valid_emails) > 0) {
            $subscr->addTempEmails($this->_action_key, $valid_emails);
            $this->_valid_emails_count += sizeof($valid_emails);
        }
    }

    function importCustomerDB()
    {
        execQuery('SUBSCR_IMPORT_ORDERS_EMAILS', array('key' => $this->_action_key));
        execQuery('SUBSCR_IMPORT_CUSTOMERS_EMAILS', array('key' => $this->_action_key));
        $this->_valid_emails_count = modApifunc('Subscriptions', 'countTempEmails', $this->_action_key);
    }

}
?>