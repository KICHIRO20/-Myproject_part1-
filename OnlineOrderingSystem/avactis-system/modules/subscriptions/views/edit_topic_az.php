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

define('SM_NEW_TOPIC',  0);
define('SM_EDIT_TOPIC', 1);

/**
 *
 * @package
 * @author
 */
class Subscriptions_EditTopic
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function Subscriptions_EditTopic()
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

        $this->_topic_id = modApiFunc('Request', 'getValueByKey', 'topic');
        if (! empty($this->_topic_id)) {
            $this->_mode = SM_EDIT_TOPIC;
        }
        else {
            $this->_mode = SM_NEW_TOPIC;
        }

        $this->initFormData();

        if (@$this->ViewState['hasCloseScript'] == 'true') {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $this->_templateContents = array(
                'ErrorsBox',
                'AscAction'
                );
        $application->registerAttributes($this->_templateContents);

        if ($this->_mode == SM_EDIT_TOPIC && empty($this->_topic)) {
        	return $this->_tmplFiller->fill('', 'errors/no_topic_edit.tpl.html',
            			array('Message' => $this->_messageResources->getMessage(
            					new ActionMessage(array('TOPIC_DOESNT_EXISTS', $this->_topic_id)))));
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $vars = array(
                'FormAction' => $request->getURL(),
                'AscAction' => $this->_mode == SM_NEW_TOPIC ? 'create_topic' : 'update_topic',
                'TopicId' => $this->_topic_id,
                'WinTitle' => $this->_messageResources->getMessage($this->_mode == SM_NEW_TOPIC ? 'TITLE_ADD_TOPIC' : 'TITLE_EDIT_TOPIC'),
                'TopicName' => @$this->POST['topic_name'],
                'TopicStatusSelect' => $this->getTopicStatusSelect('topic_status', @$this->POST['topic_status']),
                'TopicAccessSelect' => $this->getTopicAccessSelect('topic_access', @$this->POST['topic_access']),
                'TopicAutosubscribeSelect' => $this->getTopicAutoSubscribeSelect('topic_auto', @$this->POST['topic_auto']),
                );
        $result = $this->_tmplFiller->fill('', 'edit_topic.tpl.html', $vars);
        return $result;
    }

    function initFormData()
    {
        $this->POST = array();
        $this->ErrorsArray = array();
        $this->ErrorFields = array();

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

                $this->POST = $SessionPost;
            }

            $this->ViewState = $SessionPost['ViewState'];
            modApiFunc("Session", "un_Set", "SessionPost");
        }
        elseif($this->_mode == SM_EDIT_TOPIC) {
            $this->_topic = modApiFunc('Subscriptions', 'getTopic', $this->_topic_id);
            if (empty($this->_topic)) {
            	return;
            }
            $this->POST = $this->_topic;
        }
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
            case 'AscAction':
                $res = $this->_mode == SM_NEW_TOPIC ? 'create_topic' : 'update_topic';
                break;
        }
        return $res;
    }

    function getTopicStatusSelect($select_name, $selected_status = null)
    {
        $statuses = Subscriptions::getTopicStatusesNames();
        if (empty($selected_status)) {
            $selected_status = SUBSCRIPTION_TOPIC_ACTIVE;
        }
        $options = array();
        foreach($statuses as $status => $status_name) {
            $options[] = array('value' => $status, 'contents' => $status_name);
        }
        return HtmlForm::getRadio(array(
                'select_name' => $select_name,
                'values' => $options,
                'selected_value' => $selected_status,
                ));
    }

    function getTopicAccessSelect($select_name, $selected_access = null)
    {
        $statuses = Subscriptions::getTopicAccessesNames();
        if (empty($selected_access)) {
            $selected_access = SUBSCRIPTION_TOPIC_FULL_ACCESS;
        }
        $options = array();
        foreach($statuses as $status => $status_name) {
            $options[] = array('value' => $status, 'contents' => $status_name);
        }
        return HtmlForm::getRadio(array(
                'select_name' => $select_name,
                'values' => $options,
                'selected_value' => $selected_access,
                ));
    }

    function getTopicAutoSubscribeSelect($select_name, $selected_option = null)
    {
        $statuses = Subscriptions::getTopicAutoSubscribeNames();
        if (empty($selected_option)) {
            $selected_option = SUBSCRIPTION_TOPIC_AUTOSUBSCRIBE_YES;
        }
        $options = array();
        foreach($statuses as $status => $status_name) {
            $options[] = array('value' => $status, 'contents' => $status_name);
        }
        return HtmlForm::getRadio(array(
                'select_name' => $select_name,
                'values' => $options,
                'selected_value' => $selected_option,
                ));
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
                    'Colspan' => 3,
                    );
            $res = $this->_tmplFiller->fill('errors/', 'errors.tpl.html', $vars);
        }

        return $res;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $_mode;

    var $_topic_id;

    var $_topic;

    /**#@-*/
}

?>