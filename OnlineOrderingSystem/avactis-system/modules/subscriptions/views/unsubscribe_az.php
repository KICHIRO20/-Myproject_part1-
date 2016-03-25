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
 *
 * @package
 * @author
 */
class Subscriptions_Unsubscribe
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function Subscriptions_Unsubscribe()
    {
        global $application;
        $mmObj = & $application->getInstance('Modules_Manager');
        $mmObj->includeAPIFileOnce('Subscriptions');
        $mmObj->includeAPIFileOnce('Request');
        $mmObj->includeAPIFileOnce('Session');
    }

    function output()
    {
        global $application;
        loadCoreFile('html_form.php');

        $this->_messageResources = & Subscriptions::getMessageResources();
        $this->_tmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');

        $this->initFormData();

        $result = '';
        //$action = modApiFunc('Request', 'getValueByKey', 'asc_action');
        $action = @$this->POST['asc_action'];
        $has_errors = sizeof($this->ErrorsArray) > 0;
        $stage = modApiFunc('Request', 'getValueByKey', 'stage');
        if (empty($stage)) {
            $result = $this->outputAskEmails();
        }
        elseif ($stage == 'confirm') {
            $result = $this->outputAskConfirmation();
        }
        elseif ($stage == 'finish') {
            // @ show errors if were
            modApiFunc("application", "closeChild_UpdateParent");
        }
        return $result;
    }

    function initFormData()
    {
        $this->POST = null;
        $this->ErrorsArray = null;
        $this->ErrorFields = null;

        if (modApiFunc("Session", "is_Set", "SessionPost")) {
            $SessionPost = modApiFunc('Session', 'get', 'SessionPost');

            if (isset($SessionPost['ViewState']['ErrorsArray']) &&
                    sizeof($SessionPost['ViewState']['ErrorsArray']) > 0) {

                // have errors, need to restore fields values
                $this->ErrorsArray = $SessionPost['ViewState']['ErrorsArray'];
                unset($SessionPost['ViewState']['ErrorsArray']);

                if (isset($SessionPost['ViewState']['ErrorFields']) &&
                        sizeof($SessionPost['ViewState']['ErrorFields']) > 0) {
                    $this->ErrorFields = $SessionPost['ViewState']['ErrorFields'];
                    unset($SessionPost['ViewState']['ErrorFields']);
                }

            }

            $this->ViewState = $SessionPost['ViewState'];
            unset($SessionPost['ViewState']);
            $this->POST = $SessionPost;
            modApiFunc("Session", "un_Set", "SessionPost");
        }
    }

    function outputAskEmails()
    {
        global $application;
        if (isset($this->POST)) {
            $this->_emails_from = @$this->POST['emails_from'];
            $this->_topics_ids = @$this->POST['topics'];
        }
        else {
            $this->_emails_from = modApiFunc('Request', 'getValueByKey', 'emails_from');
            $this->_topics_ids = modApiFunc('Request', 'getValueByKey', 'topics');
        }
        if (empty($this->_emails_from)) {
            $this->_emails_from = 'typed';
        }

        $this->_templateContents = array(
                'ErrorsBox',
                'TopicsList',
                );
        $application->registerAttributes($this->_templateContents);

        $vars = array(
                'FormAction' => $this->urlSelf(),
                'TopicsIds' => $this->_topics_ids,
                'MaxFileSize' => $this->return_bytes(ini_get('upload_max_filesize')),
                'TypedEmailsDisabled' => $this->_emails_from == 'typed' ? '' : 'disabled',
                'PlainEmailsDisabled' => $this->_emails_from == 'plain' ? '' : 'disabled',
                'CSVEmailsDisabled' => $this->_emails_from == 'csv' ? '' : 'disabled',
                'TypedEmailsChecked' => $this->_emails_from == 'typed' ? 'checked' : '',
                'PlainEmailsChecked' => $this->_emails_from == 'plain' ? 'checked' : '',
                'CSVEmailsChecked' => $this->_emails_from == 'csv' ? 'checked' : '',
                'TypedEmails' => @$this->POST['typed_emails'],
                );
        $result = $this->_tmplFiller->fill('subscribe/', 'box.tpl.html', $vars);
        return $result;
    }

    function outputAskConfirmation()
    {
        global $application;
        if (@$this->ViewState['hasCloseScript'] == 'true') {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $this->_action_key = modApiFunc('Request', 'getValueByKey', 'action_key');
        $this->_topics_ids = modApiFunc('Request', 'getValueByKey', 'topics');

        $this->_templateContents = array(
                'ConfirmTopicsList',
                );
        $application->registerAttributes($this->_templateContents);

        $this->total_known = modApiFunc('Subscriptions', 'countTempEmails', $this->_action_key, SUBSCRIPTION_TEMP_EXISTS);
        $this->total_new = modApiFunc('Subscriptions', 'countTempEmails', $this->_action_key, SUBSCRIPTION_TEMP_DONT_EXISTS);
        $vars = array(
                'FormAction' => $this->urlSelf(),
                'TopicsIds' => $this->_topics_ids,
                'ActionKey' => $this->_action_key,
                'TotalKnownEmails' => $this->total_known,
                'TotalNewEmails' => $this->total_new,
                'TotalValidEmails' => $this->total_known + $this->total_new,
                );
        $result = $this->_tmplFiller->fill('subscribe/', 'box.confirm.tpl.html', $vars);
        return $result;

    }

    function getTag($tag)
    {
        global $application;

        $res = null;
        switch($tag)
        {
            case 'ErrorsBox':
                $res = $this->getErrorsBox();
                break;

            case 'TopicsList':
                $res = $this->getTopicsList();
                break;

            case 'ConfirmTopicsList':
                $res = $this->getConfirmTopicsList();
                break;
        }
        return $res;
    }

    function getErrorsBox()
    {
        $res = '';

        if(isset($this->ErrorsArray) && count($this->ErrorsArray) > 0)
        {
            $text = '';
            foreach ($this->ErrorsArray as $key => $value) {
                $text .= '-&nbsp;'.$this->_messageResources->getMessage(new ActionMessage($value)).'<br />';
            }
            $vars = array(
                    'ErrorsText' => $text,
                    'Colspan' => 2,
                    );
            $res = $this->_tmplFiller->fill('errors/', 'errors.tpl.html', $vars);
        }

        return $res;
    }

    function getTopicsList()
    {
        $topics_ids = explode(',', $this->_topics_ids);
        $topics = modApiFunc('Subscriptions', 'getTopicsByIds', $topics_ids);
        $res = '';

        foreach ($topics as $topic) {
            $vars = array (
                    'TopicName'     => prepareHTMLDisplay($topic['topic_name']),
                    'TopicEmails'   => prepareHTMLDisplay($topic['topic_emails']),
                    'TopicStatusName' => modApiFunc('Subscriptions', 'getTopicStatusName', $topic['topic_status']),
                    'TopicAccessName' => modApiFunc('Subscriptions', 'getTopicAccessName', $topic['topic_access']),
                    );
            $res .= $this->_tmplFiller->fill('subscribe/', 'item.tpl.html', $vars);
        }

        return $res;
    }

    function getConfirmTopicsList()
    {
        $topics_ids = explode(',', $this->_topics_ids);
        $topics = modApiFunc('Subscriptions', 'getTopicsByIdsTemp', $this->_action_key, $topics_ids);
        $res = '';

        foreach ($topics as $topic) {
            $vars = array (
                    'TopicName'     => prepareHTMLDisplay($topic['topic_name']),
                    'TopicEmails'   => prepareHTMLDisplay($topic['topic_emails']),
                    'TopicStatusName' => modApiFunc('Subscriptions', 'getTopicStatusName', $topic['topic_status']),
                    'TopicAccessName' => modApiFunc('Subscriptions', 'getTopicAccessName', $topic['topic_access']),
                    'ExistingEmails' => prepareHTMLDisplay($topic['existing_emails']),
                    'NewEmails' => prepareHTMLDisplay($this->total_known + $this->total_new - $topic['existing_emails']),
            );
            $res .= $this->_tmplFiller->fill('subscribe/', 'item.confirm.tpl.html', $vars);
        }

        return $res;
    }

    function urlSelf()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        return $request->getURL();
    }

    function return_bytes($val) {
        $val = trim($val);
        $last = _ml_strtolower($val{_byte_strlen($val)-1});
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $_topics_ids;

    /**#@-*/
}

?>