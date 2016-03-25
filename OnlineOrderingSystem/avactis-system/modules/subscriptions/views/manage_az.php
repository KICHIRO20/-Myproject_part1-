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

define('SM_SHOW_TOPICS_LIST',   0);
define('SM_SHOW_TOPIC',         1);

/**
 *
 * @package
 * @author
 */
class Subscriptions_Manage
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Newsletter_List constructor
     */
    function Subscriptions_Manage()
    {
        global $application;
        $mmObj = & $application->getInstance('Modules_Manager');
        $mmObj->includeAPIFileOnce('Subscriptions');
        $mmObj->includeAPIFileOnce('Request');
        $mmObj->includeAPIFileOnce('Session');
    }

    /**
     *
     */
    function output()
    {
    	global $application;
        loadCoreFile('html_form.php');

        $this->_messageResources = & Subscriptions::getMessageResources();
        $this->_tmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');

        $this->_topic_id = modApiFunc('Request', 'getValueByKey', 'topic');
        if (! empty($this->_topic_id)) {
            $this->_mode = SM_SHOW_TOPIC;
            $this->initShowTopic();
            if (empty($this->_topic)) {
            	return $this->_tmplFiller->fill('', 'errors/no_topic_manage.tpl.html',
            			array('Message' => $this->_messageResources->getMessage(
            					new ActionMessage(array('TOPIC_DOESNT_EXISTS', $this->_topic_id)))));
            }
        }
        else {
            $this->_mode = SM_SHOW_TOPICS_LIST;
            $this->topics = modApiFunc('Subscriptions', 'getTopicsList');
        }

        $this->initFormData();

        $this->_templateContents = array(
                'ToolbarTop',
                'ToolbarBottom',
                'FormErrors',
                'ItemsList',
                'EmailsPaginator',
                'EmailSearchForm',
                'LinkResetSearchEmail',
        );
        $application->registerAttributes($this->_templateContents);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $vars = array(
                'FormAction' => $request->getURL(),
                'AddTopicURL' => $this->urlAddTopic(),
                'EditTopicURL' => $this->urlEditTopic(),
                'SortTopicsURL' => $this->urlSortTopic(),
                'DeleteTopicsURL' => $this->urlDeleteTopics(),
                'SubscribeURL' => $this->urlSubscribe(),
                'UnsubscribeURL' => $this->urlUnsubscribe(),
                'ExportURL' => $this->urlExport(),
                );
        $result = $this->_tmplFiller->fill('', 'manage.tpl.html', $vars);
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

                $this->ViewState = $SessionPost['ViewState'];
                unset($SessionPost['ViewState']);

                $this->POST = $SessionPost;
            }
            modApiFunc("Session", "un_Set", "SessionPost");
        }
    }

    function initShowTopic()
    {
        $this->_search_email = trim(modApiFunc('Request', 'getValueByKey', 'email'));
        $var = 'Subscription_Email_Search_'.$this->_topic_id;
        $this->_prev_search_email = modApifunc('Session', 'is_set', $var) ? modApifunc('Session', 'get', $var) : '';
        modApiFunc('Session', 'set', $var, $this->_search_email);

        $this->_topic = modApiFunc('Subscriptions', 'getTopic', $this->_topic_id);
        if (empty($this->_topic)) {
        	return;
        }

        $this->_pager_name = 'Subscription_Topic_'.$this->_topic_id;
        modAPIFunc('Paginator', 'setCurrentPaginatorName', $this->_pager_name);
        $page = modApiFunc('Paginator', 'getPaginatorPage', $this->_pager_name);
        if ($page <= 0) {
            modapiFunc('Paginator', 'resetPaginator', $this->_pager_name);
        }
        if ($this->_search_email != $this->_prev_search_email) {
            modApiFunc('Paginator', 'resetPaginator', $this->_pager_name);
        }

        $this->emails = & $this->fetchEmails();

        $paginator_offset = modApiFunc('paginator', 'getCurrentPaginatorOffset');
        $this->emails_total = modAPIFunc('paginator', 'getCurrentPaginatorTotalRows');
        $emails_per_page = modAPIFunc('paginator', 'getPaginatorRowsPerPage', $this->_pager_name);

        if($paginator_offset >= 0 && $this->emails_total > 0) {
            $this->emails_from = 1 + $paginator_offset;
            $this->emails_to =  min( $paginator_offset + $emails_per_page, $this->emails_total);
        }
        else {
            $this->emails_from = 0;
            $this->emails_to =   0;
        }
    }

    function getTag($tag)
    {
        global $application;

    	$res = null;
    	switch($tag)
    	{
    	    case 'Breadcrumb':
    	        if ($this->_mode == SM_SHOW_TOPICS_LIST) {
    	            $vars = array();
    	            $res = $this->_tmplFiller->fill('topics/', 'breadcrumb.tpl.html', $vars);
    	        }
    	        else {
    	            $vars = array('TopicName' => $this->_topic ?
    	            		$this->_topic['topic_name'] : getMsg('SUBSCR', 'TITLE_TOPIC').' #'.$this->_topic_id);
                    $res = $this->_tmplFiller->fill('emails/', 'breadcrumb.tpl.html', $vars);
                }
    	        break;
            case 'ToolbarTop':
                $res = $this->getToolbarTop();
                break;
            case 'ToolbarBottom':
                $res = $this->getToolbarBottom();
                break;
            case 'FormErrors':
                $res = $this->getErrors();
                break;
            case 'EmailSearchForm':
                $res = $this->getEmailSearchForm();
                break;

            case 'ItemsList':
                $res = $this->getItemsList();
                break;

            case 'TopicsRows':
                $res = $this->getTopicsRows();
                break;
            case 'UpdateOrdersRow':
                $res = $this->getUpdateOrdersRow();
                break;
            case 'AddTopicRow':
                $res = $this->getAddTopicRow();
                break;

            case 'EmailsRows':
                $res = $this->getEmailsRows();
                break;
            case 'DeleteEmailsRow':
                if ($this->_mode == SM_SHOW_TOPIC) {
                    $vars = array('TopicId' => $this->_topic_id);
                    $res = $this->_tmplFiller->fill('emails/', 'item_delete.tpl.html', $vars);
                }
                break;
            case 'EmailsPaginator':
                if ($this->_mode == SM_SHOW_TOPIC) {
                    $application->registerAttributes(array('PaginatorLine', 'PaginatorRows',));
                    $vars = array();
                    $res = $this->_tmplFiller->fill('emails/', 'paginator.tpl.html', $vars);
                }
                break;
            case 'PaginatorLine':
                $add_keys = array('topic' => $this->_topic_id);
                if (! empty($this->_search_email)) {
                    $add_keys['email'] = $this->_search_email;
                }
                $obj = &$application->getInstance($tag);
                $res = $obj->output($this->_pager_name, CURRENT_REQUEST_URL, $add_keys);
                break;
            case 'PaginatorRows':
                $add_keys = array('topic' => $this->_topic_id);
                if (! empty($this->_search_email)) {
                    $add_keys['email'] = $this->_search_email;
                }
                $obj = &$application->getInstance($tag);
                $res = $obj->output($this->_pager_name, CURRENT_REQUEST_URL, 'PGNTR_EMLS_ITEMS', $add_keys);
                break;
            case 'LinkResetSearchEmail':
                if (! empty($this->_search_email)) {
                    $vars = array(
                            'Url' => $this->urlResetEmailSearch(),
                            );
                    $res = $this->_tmplFiller->fill('emails/', 'link_reset_search.tpl.html', $vars);
                }

    	}
        return $res;
    }

    // tags

    function getToolbarTop()
    {
        $vars = array(
                'TopicId' => $this->_topic_id,
                'Title' => $this->_messageResources->getMessage($this->_mode == SM_SHOW_TOPICS_LIST ? 'TITLE_TOPICS' : 'TITLE_EMAILS'),
                'EditSignatureURL' => $this->urlEditSignature(),
                );
        return $this->_tmplFiller->fill($this->_mode == SM_SHOW_TOPICS_LIST ? 'topics/' : 'emails/', 'toolbar_top.tpl.html', $vars);
    }

    function getEmailSearchForm()
    {
        if ($this->_mode != SM_SHOW_TOPIC) {
            return '';
        }
        $request = new Request();
        $request->setView('Subscriptions_SortTopics');
        $vars = array(
                'urlActionSearchEmail' => $this->urlActionSearchEmail(),
                'TopicId' => $this->_topic_id,
                );
        return $this->_tmplFiller->fill('emails/', 'email_search_form.tpl.html', $vars);
    }

    function getToolbarBottom()
    {
        if ($this->_mode == SM_SHOW_TOPICS_LIST) {
            $vars = array(
                    'ClassEdit' => empty($this->topics) ? 'button_disabled' : '',
                    'ClassSort' => sizeof($this->topics) < 2 ? 'button_disabled' : '',
                    'ClassExport' => empty($this->topics) ? 'button_disabled' : '',
                    'ClassDelete' => empty($this->topics) ? 'button_disabled' : '',
                    'ClassSubscr' => empty($this->topics) ? 'button_disabled' : '',
                    'ClassUnsubscr' => empty($this->topics) ? 'button_disabled' : '',
                    'OnClickEdit' => empty($this->topics) ? '' : 'editTopic(this)',
                    'OnClickSort' => sizeof($this->topics) < 2 ? '' : 'sortTopics()',
                    'OnClickExport' => empty($this->topics) ? '' : 'exportEmails()',
                    'OnClickDelete' => empty($this->topics) ? '' : 'deleteTopics(this)',
                    'OnClickSubscr' => empty($this->topics) ? '' : 'subscribe()',
                    'OnClickUnsubscr' => empty($this->topics) ? '' : 'unsubscribe()',
                    );
        }
        else {
            $request = new Request();
            $request->setView('Subscriptions_SortTopics');
            $vars = array(
                    'TopicId' => $this->_topic_id,
                    'ClassExport' => empty($this->emails) ? 'button_disabled' : '',
                    'ClassUnsubscr' => empty($this->emails) ? 'button_disabled' : '',
                    'ClassDelete' => empty($this->emails) ? 'button_disabled' : '',
                    'OnClickExport' => empty($this->emails) ? '' : 'exportEmails()',
                    'OnClickUnsubscr' => empty($this->emails) ? '' : 'unsubscribe()',
                    'OnClickDelete' => empty($this->emails) ? '' : 'submitDeleteEmails(this)',
                    );
        }
        return $this->_tmplFiller->fill($this->_mode == SM_SHOW_TOPICS_LIST ? 'topics/' : 'emails/', 'toolbar_bottom.tpl.html', $vars);
    }

    function getErrors()
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

    function getItemsList()
    {
        global $application;

        $res = '';

        switch($this->_mode)
        {
            case SM_SHOW_TOPICS_LIST:
                $application->registerAttributes(array('TopicsRows', 'UpdateOrdersRow', 'AddTopicRow',));
                $res = $this->_tmplFiller->fill('topics/', 'list.tpl.html', array());
                break;

            case SM_SHOW_TOPIC:
                $application->registerAttributes(array('EmailsRows', 'DeleteEmailsRow',));
                if (empty($this->_search_email)) {
                    $message = new ActionMessage(array('TITLE_EMAILS_LIST', $this->emails_from, $this->emails_to, $this->emails_total));
                }
                else {
                    $message = new ActionMessage(array('TITLE_MATCHED_EMAILS_LIST', $this->emails_from, $this->emails_to, $this->emails_total, $this->_search_email));
                }
                $vars = array(
                        'PaginatorTitle' => $this->_messageResources->getMessage($message),
                        'TopicId' => $this->_topic_id,
                );
                $res = $this->_tmplFiller->fill('emails/', 'list.tpl.html', $vars);
                break;
        }

        return $res;
    }

    //

    function getTopicsRows()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $self_url = $request->getURL();

        $res = '';
        $counter = 0;
        foreach ($this->topics as $topic)
        {
            $counter++;

            $vars = array (
                    'TopicId'       => prepareHTMLDisplay($topic['topic_id']),
                    'TopicName'     => prepareHTMLDisplay($topic['topic_name']),
                    'TopicEmails'   => prepareHTMLDisplay($topic['topic_emails']),
                    'TopicOrder'    => prepareHTMLDisplay(isset($this->POST['sort_order'][$topic['topic_id']]) ? $this->POST['sort_order'][$topic['topic_id']] : $topic['sort_order']),
                    'TopicStatusName' => modApiFunc('Subscriptions', 'getTopicStatusName', $topic['topic_status']),
                    'TopicAccessName' => modApiFunc('Subscriptions', 'getTopicAccessName', $topic['topic_access']),
                    'TopicAutoSubscribeName' => modApiFunc('Subscriptions', 'getTopicAutoSubscribeName', $topic['topic_auto']),
                    'EditTopicURL'  => escapeAttr('javascript:editTopic("'.escapeJSString($topic['topic_id']).'")'),
                    'TopicEmailsURL'=> $self_url.'?topic='.prepareHTMLDisplay($topic['topic_id']),
                    'TopicSelected' => isset($this->POST['topic_id'][$topic['topic_id']]) ? 'checked' : '',
                    'RowClass'      => isset($this->POST['topic_id'][$topic['topic_id']]) ? 'selected' : '',
                    );
            $res .= $this->_tmplFiller->fill('topics/', 'item.tpl.html', $vars);
        }

        if ($counter == 0)
        {
            $res .= $this->_tmplFiller->fill('topics/', 'item_no_items.tpl.html', array());
            $counter++;
        }

        for ($i=0; $i < 10-$counter; $i++)
        {
            $res .= $this->_tmplFiller->fill('topics/', 'item_empty.tpl.html', array());
        }

        return $res;
    }

    function getTopicStatusSelect($select_name, $selected_status = null)
    {
        $statuses = Subscriptions::getTopicStatusesNames();
        $options = array();
        foreach($statuses as $status => $status_name) {
            $options[] = array('value' => $status, 'contents' => $status_name);
        }
        return HtmlForm::genDropdownSingleChoice(array(
                'select_name' => $select_name,
                'values' => $options,
                'selected_value' => $selected_status,
                ));
    }

    //

    function &fetchEmails()
    {
        $emails = modApiFunc('Subscriptions', 'getTopicEmails', $this->_topic_id, $this->_search_email);

        $customers_ids = array();
        foreach(array_keys($emails) as $i) {
            if ($emails[$i]['customer_id'] > 0) {
                $customers_ids[] = $emails[$i]['customer_id'];
            }
        }

        $names_list = modApiFunc('Customer_Account', '__fetch_customer_names', $customers_ids);

        foreach(array_keys($emails) as $i) {
            $email = & $emails[$i];
            if ($email['customer_id'] > 0 && ! empty($names_list[$email['customer_id']])) {
                $email['customer_name'] = $names_list[$email['customer_id']];
            }
        }
        return $emails;
    }

    function getEmailsRows()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $self_url = $request->getURL();

        $res = '';
        $counter = 0;
        foreach (array_keys($this->emails) as $i)
        {
            $counter++;
            $email = & $this->emails[$i];

            $vars = array (
                    'EmailId'       => prepareHTMLDisplay($email['email_id']),
                    'Email'         => prepareHTMLDisplay($email['email']),
                    'UserLink'      => $this->getUserLink($email),
                    'EmailSelected' => isset($this->POST['email_id'][$email['email_id']]) ? 'checked' : '',
                    'RowClass'      => isset($this->POST['topic_id'][$email['email_id']]) ? 'selected' : '',
                    );
            $res .= $this->_tmplFiller->fill('emails/', 'item.tpl.html', $vars);
        }
        if ($counter == 0) {
            $res .= $this->_tmplFiller->fill('emails/', 'item_no_items.tpl.html', array());
            $counter++;
        }
        for ($i=0; $i < 8-$counter; $i++) {
            $res .= $this->_tmplFiller->fill('emails/', 'item_empty.tpl.html', array());
        }

        return $res;
    }

    function getUserLink(&$email)
    {
        if (empty($email['customer_name'])) {
            return '-';
        }
        $vars = array(
                'UserID' => $email['customer_id'],
                'UserName' => prepareHTMLDisplay($email['customer_name']),
                );
        return $this->_tmplFiller->fill('emails/', 'link_customer.tpl.html', $vars);
    }

    function EmailsPaginatorTitle()
    {
        $vars = array(
                );
        return $this->_tmplFiller->fill('', 'paginator_title.tpl.html', $vars);
    }

    //

    function urlAddTopic()
    {
        $request = new Request();
        $request->setView('Subscriptions_EditTopic');
        return $request->getURL();
    }

    function urlEditTopic()
    {
        $request = new Request();
        $request->setView('Subscriptions_EditTopic');
        $request->setKey('topic', '');
        return $request->getURL();
    }

    function urlSortTopic()
    {
        $request = new Request();
        $request->setView('Subscriptions_SortTopics');
        return $request->getURL();
    }

    function urlDeleteTopics()
    {
        $request = new Request();
        $request->setView('Subscriptions_DeleteTopics');
        $request->setKey('topics', '');
        return $request->getURL();
    }

    function urlSubscribe()
    {
        $request = new Request();
        $request->setView('Subscriptions_Subscribe');
        $request->setKey('topics', '');
        return $request->getURL();
    }

    function urlUnsubscribe()
    {
        $request = new Request();
        $request->setView('Subscriptions_Unsubscribe');
        $request->setKey('topics', '');
        return $request->getURL();
    }

    function urlExport()
    {
        $request = new Request();
        $request->setView('Subscriptions_Export');
        $request->setKey('topics', '');
        return $request->getURL();
    }

    function urlActionSearchEmail()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        return $request->getURL();
    }

    function urlResetEmailSearch()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('topic', $this->_topic_id);
        return $request->getURL();
    }

    function urlEditSignature()
    {
        $request = new Request();
        $request->setView('Subscriptions_EditSignature');
        return $request->getURL();
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
    var $_search_email;

    var $topics;
    var $emails;

    var $_templateContents;
    var $_listTemplateContents;
    var $_tmplFiller;
    var $_messageResources;

    var $ErrorFields = array();
    var $ErrorsArray = array();

    /**#@-*/

}
?>