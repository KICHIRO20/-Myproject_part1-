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
 * @author
 *
 */

class SubscribeForm_Base
{
    var $ini_section;

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'subscribe-box.ini',
            'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE,
            ),
            'options' => array(
            )
        );
        return $format;
    }

    function SubscribeForm_Base()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->ini_section)) {
            $this->NoView = true;
            return;
        }

        if (modApiFunc("Session", "is_Set", "SessionPost")) {
            $SessionPost = modApiFunc('Session', 'get', 'SessionPost');
            modApiFunc("Session", "un_Set", "SessionPost");
            if (isset($SessionPost)) {
                $this->ViewState = $SessionPost['ViewState'];
            }
        }

        $this->can_unsubscribe = modApiFunc('Subscriptions', 'canClientUnsubscribe');
        $this->signed_in = modApiFunc('Customer_Account', 'getCurrentSignedCustomer') !== null;
        $this->topics = modApiFunc('Subscriptions', 'getCustomerTopics', $this->signed_in);
        if (sizeof($this->topics) == 0) {
            $this->NoView = true;
            return;
        }
    }

    function fetchSubscribedTopics()
    {
        $this->subscribed_topics = array();
        if (!empty($this->emails)) {
        	$params = array(
        			'emails' => $this->emails,
        			'signed_in' => $this->signed_in,
        			);
        	$res = execQuery('SUBSCR_GET_SUBSCRIBED_TOPICS_IDS', $params);
        	foreach ($res as $r) {
        		$this->subscribed_topics[ $r['email'] ][ $r['topic_id'] ] = $r['topic_id'];
        	}
        }
    	if (isset($this->email)) {
    		$this->subscribed = @$this->subscribed_topics[ $this->email ];
    		if (! isset($this->subscribed)) {
    			$this->subscribed = array();
    		}
    	}
    }

    function output()
    {
        $this->fetchSubscribedTopics();

    	if ($this->NoView) {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_FormActionURL',
        	'Local_SubscriptionFormsList',
        	'Local_FormNum',
        	'Local_RemoveEmailURL',
            'Local_SubscribedEmail',
            'Local_TopicsList',
            'Local_SubmitButton',
            'Local_Topics',
            'Local_TopicId',
            'Local_TopicName',
            'Local_TopicChecked',
            'Local_TopicDisabled',
        	'Local_Messages',
            'Local_MessageText',
            );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate($this->ini_section);
        $this->templateFiller->setTemplate($this->template);

        if ($this->ini_section == "SubscribeFormProfile" && $this->signed_in == false)
        {
            $tpl = $this->templateFiller->fill('ContainerNoAccount');
        }
        else
        {
            $tpl = $this->templateFiller->fill('Container');
        }

        return $tpl;
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_FormActionURL':
                $r = new Request();
                $r->setView(CURRENT_REQUEST_URL);
                $value = $r->getURL();
                break;

            case 'Local_SubscriptionFormsList':
            	$value = '';
            	$this->form_num = 0;
            	if (sizeof($this->emails) > 0) {
            		foreach ($this->emails as $this->email) {
            			$this->subscribed = @$this->subscribed_topics[ $this->email ];
            			if (! isset($this->subscribed)) {
            				$this->subscribed = array();
            			}
            			$value .= $this->templateFiller->fill('SubscriptionForm');
            			$this->form_num ++;
            		}
            	}
            	else {
            		$value .= $this->templateFiller->fill('NoSubscribedEmails');
            	}
            	$this->subscribed = array();
            	$value .= $this->templateFiller->fill('SubscriptionFormNewEmail');
            	break;

            case 'Local_FormNum':
            	$value = $this->form_num;
            	break;

            case 'Local_RemoveEmailURL':
                $r = new Request();
                $r->setView(CURRENT_REQUEST_URL);
                $r->setAction('customer_remove_email');
                $r->setKey('email', $this->email);
                $value = $r->getURL();
                break;

            case 'Local_SubscribedEmail':
                $value = $this->email;
                break;

            case 'Local_TopicsList':
                if (sizeof($this->topics) > 1) {
                    $value = $this->templateFiller->fill('TopicsList');
                }
                break;

            case 'Local_SubmitButton':
                if (sizeof($this->topics) == 1) {
                    $this->topic_num = reset(array_keys($this->topics));
                    if ($this->can_unsubscribe) {
                        if (in_array($this->topics[$this->topic_num]['topic_id'], $this->subscribed)) {
                            $value = $this->templateFiller->fill('ButtonUnsubscribe');
                        }
                        else {
                            $value = $this->templateFiller->fill('ButtonSubscribe');
                        }
                    }
                    else {
                        if (in_array($this->topics[$this->topic_num]['topic_id'], $this->subscribed)) {
                        	$value = $this->templateFiller->fill('AlreadySubscribed');
                        }
                        else {
                            $value = $this->templateFiller->fill('ButtonSubscribe');
                        }
                    }
                }
                else {
                    $value = $this->templateFiller->fill($this->can_unsubscribe ? 'ButtonSubmit' : 'ButtonSubscribeOnly');
                }
                break;

            case 'Local_Topics':
                $value = '';
                foreach (array_keys($this->topics) as $this->topic_num) {
                    $value .= $this->templateFiller->fill('TopicItem');
                }
                break;

            case 'Local_TopicId':
                $value = $this->topics[$this->topic_num]['topic_id'];
                break;

            case 'Local_TopicName':
                $value = $this->topics[$this->topic_num]['topic_name'];
                break;

            case 'Local_TopicChecked':
                $value = in_array($this->topics[$this->topic_num]['topic_id'], $this->subscribed) ? 'checked="checked"' : '';
                break;

            case 'Local_TopicDisabled':
            	$value = ! $this->can_unsubscribe && in_array($this->topics[$this->topic_num]['topic_id'], $this->subscribed)
            			? ' disabled="disabled"' : '';
            	break;

            case 'Local_Messages':
                if (isset($this->ViewState['Messages'])) {
                    foreach($this->ViewState['Messages'] as $this->MessageText) {
                        $value .= $this->templateFiller->fill('Message');
                    }
                }
                if (isset($this->ViewState['ErrorsArray'])) {
                    foreach($this->ViewState['ErrorsArray'] as $this->MessageText) {
                        $value .= $this->templateFiller->fill('ErrorMessage');
                    }
                }
                break;

            case 'Local_MessageText':
                return $this->MessageText;
                break;
        };

        return $value;
    }

    var $signed_in;
    var $emails;
    var $subscribed = array();
    var $subscribed_topics;
    var $ViewState = array();
    var $MessageText;

};

?>