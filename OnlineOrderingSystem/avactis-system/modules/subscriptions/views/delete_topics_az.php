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
class Subscriptions_DeleteTopics
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function Subscriptions_DeleteTopics()
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

        $this->_topics_ids = modApiFunc('Request', 'getValueByKey', 'topics');

        $this->initFormData();

        if (@$this->ViewState['hasCloseScript'] == 'true') {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $this->_templateContents = array(
                'ErrorsBox',
                'TopicsList',
                );
        $application->registerAttributes($this->_templateContents);

        $vars = array(
                'FormAction' => $this->urlSelf(),
                'TopicsIds' => $this->_topics_ids,
                );
        $result = $this->_tmplFiller->fill('delete_topics/', 'delete_topics.tpl.html', $vars);
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
                    'Colspan' => 3,
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
            $res .= $this->_tmplFiller->fill('delete_topics/', 'item.tpl.html', $vars);
        }

        return $res;
    }

    function urlSelf()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        return $request->getURL();
    }

    function urlTopicsList()
    {
        $request = new Request();
        $request->setView('Subscriptions_Manage');
        return $request->getURL();
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