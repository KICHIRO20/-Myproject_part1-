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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class CustomerAccountInfo
{
    function CustomerAccountInfo()
    {
        global $application;
        $request = new Request();
        $customer_id = $request->getValueByKey('customer_id');
        $account_name = modApiFunc('Customer_Account','getCustomerAccountNameByCustomerID',$customer_id);
        $this->customer_obj = &$application->getInstance('CCustomerInfo',$account_name);
        $this->customer_obj->setPersonInfoAttrsType(PERSON_INFO_GROUP_ATTR_VISIBLE);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
    }

    function out_AttributesList($group_name)
    {
        global $application;
        $html_code = '';

        $group_info = $this->customer_obj->getPersonInfoGroupInfoByName($group_name);

        foreach($this->customer_obj->getPersonInfoGroupAttrsNames($group_name) as $attr_name)
        {
            if(preg_match("/password/i",$attr_name))
                continue;

            if($attr_name == 'AccountName' and $this->customer_obj->getPersonInfo('Status') == 'B')
                continue;

            $attr_info = $this->customer_obj->getPersonInfoAttrInfoByName($attr_name, $group_name);
            $attr_value = $this->customer_obj->getPersonInfo($attr_name, $group_name);

            if($attr_name == 'Country')
            {
                $attr_value = modApiFunc('Location','getCountry',$attr_value);
            };
            if($attr_name == 'State')
            {
                $state_code = modApiFunc('Location','getStateCode',$attr_value);
                if($state_code != '')
                {
                    $attr_value = modApiFunc('Location','getState',$attr_value);
                };
            };

            $template_contents = array(
                'AttributeName' => $attr_info['visible_name']
               ,'AttributeValue' => htmlspecialchars($attr_value)
               ,'AttributeID' => $attr_info['attr_id']
               ,'GroupID' => $group_info['id']
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $html_code .= $this->mTmplFiller->fill("customer_account/account_info/", "attribute.tpl.html",array());
        };

        return $html_code;
    }

    function out_GroupsList()
    {
        global $application;
        $html_code = '';

        foreach($this->customer_obj->getPersonInfoGroupsNames() as $group_name)
        {
            if(!modApiFunc('Customer_Account','isPersionInfoGroupActive',$group_name))
                continue;

            $group_info = $this->customer_obj->getPersonInfoGroupInfoByName($group_name);

            $template_contents = array(
                'GroupName' => getMsg('CA',$group_info['lang_code'])
               ,'GroupID' => $group_info['id']
               ,'AttributesList' => $this->out_AttributesList($group_name)
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("customer_account/account_info/", "group.tpl.html",array());
        };

        return $html_code;
    }

    function out_OrdersList()
    {
        global $application;
        $html_code = '';

        $orders_filter = array(
            'type' => 'quick'
           ,'order_status' => ORDER_STATUS_ALL
        );

        $this->customer_obj->setOrdersHistoryFilter($orders_filter);

        foreach($this->customer_obj->getOrdersIDs() as $order_id)
        {
            $base_info = $this->customer_obj->getBaseOrderInfo($order_id);

            $template_contents = array(
                'OrderId' => sprintf("%05d", $order_id)
               ,'OrderDate' => $this->__format_date($base_info['order_date'])
               ,'OrderPriceTotal' =>  modApiFunc('Localization','currency_format',$base_info['order_total'])
               ,'OrderStatus' => getMsg('SYS','ORDER_STATUS_'.sprintf("%03d",$base_info['order_status_id']))
               ,'OrderPaymentStatus' => getMsg('SYS','ORDER_PAYMENT_STATUS_'.sprintf("%03d",$base_info['order_payment_status_id']))
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("customer_account/account_info/", "order.tpl.html",array());
        };

        return $html_code;
    }

    function get_SubscribedTopics()
    {
        $subscribed = execQuery('SUBSCR_GET_CUSTOMER_SUBSCRIBED_TO', array('customer' => $this->customer_obj->account));
        $subscriptions = array();
        $emails = modApiFunc('Subscriptions', 'getCustomerSubscriptionEmails', $this->customer_obj->account);
		foreach ($emails as $email) {
			$subscriptions[$email] = array();
		}
        foreach($this->customer_obj->getPersonInfoGroupsNames() as $group_name) {
        	$email = _ml_strtolower($this->customer_obj->getPersonInfo('Email', $group_name));
        	if (modApiFunc('Users', 'isValidEmail', $email)) {
            	$subscriptions[$email] = array();
        	}
        }
        foreach (array_keys($subscribed) as $i) {
            $subscriptions[ _ml_strtolower($subscribed[$i]['email']) ][ $subscribed[$i]['topic_id'] ] = 1;
        }
        return $subscriptions;
    }

    function url_ActionUpdateSubscriptions()
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        return $request->getURL();
    }

    function out_SubscriptionsList()
    {
        global $application;
        $topics = modApiFunc('Subscriptions', 'getTopicsList');
        $subscriptions = $this->get_SubscribedTopics();
        $res = '';
        $this->selected_all_topics = true;
        if (sizeof($subscriptions) == 0) {
            $res = $this->mTmplFiller->fill('customer_account/account_info/', 'subscr_item_no_emails.tpl.html', array());
        }
        else
        if (sizeof($topics) > 0) {
            foreach (array_keys($subscriptions) as $email) {
                $subscribed = & $subscriptions[$email];
            	$email_id = preg_replace('/\.@/', '_', $email);
                $selected_all_topics = true;
                foreach ($topics as $topic) {
                    if (! isset($subscribed[$topic['topic_id']])) {
                        $selected_all_topics = false;
                    }
                }
                $vars = array(
                        'Email' => $email,
                        'EmailId' => $email_id,
                        'SelectedAllTopics' => $selected_all_topics ? 'checked' : '',
                         );
                $res .= $this->mTmplFiller->fill('customer_account/account_info/', 'subscr_email.tpl.html', $vars);
                foreach ($topics as $topic) {
                    $vars = array (
                            'RowClass'      => isset($subscribed[$topic['topic_id']]) ? 'info' : '',
                            'Email'         => prepareHTMLDisplay($email),
                            'TopicId'       => prepareHTMLDisplay($topic['topic_id']),
                            'TopicName'     => prepareHTMLDisplay($topic['topic_name']),
                            'TopicEmails'   => prepareHTMLDisplay($topic['topic_emails']),
                            'TopicStatusName' => modApiFunc('Subscriptions', 'getTopicStatusName', $topic['topic_status']),
                            'TopicAccessName' => modApiFunc('Subscriptions', 'getTopicAccessName', $topic['topic_access']),
                            'TopicAutoSubscribeName' => modApiFunc('Subscriptions', 'getTopicAutoSubscribeName', $topic['topic_auto']),
                            'TopicSelected' => isset($subscribed[$topic['topic_id']]) ? 'checked' : '',
                            );
                    $res .= $this->mTmplFiller->fill('customer_account/account_info/', 'subscr_item.tpl.html', $vars);
                }
            }

            $res .= $this->mTmplFiller->fill('customer_account/account_info/', 'subscr_item_update.tpl.html', array());
        }
        else {
            $res .= $this->mTmplFiller->fill('customer_account/account_info/', 'subscr_item_no_items.tpl.html', array());
        }
        return $res;
    }

    function getMessageBox()
    {
        $html = '';
        if (modApiFunc('Session','is_set','AplicationSettingsMessages'))
        {
            $messages = modApiFunc('Session','get','AplicationSettingsMessages');
            modApiFunc('Session','un_set','AplicationSettingsMessages');

            if (isset($messages['ERRORS']))
            {
                $html .= $this->renderMessages($messages['ERRORS'], "errors.tpl.html");
            }

            if (isset($messages['MESSAGES']))
            {
                $html .= $this->renderMessages($messages['MESSAGES'], "messages.tpl.html");
            }
        }
        return $html;
    }

    function renderMessages($messages, $tpl)
    {
        $this->__msg = '';
        foreach ($messages as $msg) {
            $this->__msg .= $msg."<br>";
        }
        $html = $this->mTmplFiller->fill('customer_account/account_info/', $tpl, array());
        $this->__msg = '';
        return $html;
    }

    function output()
    {
        global $application;

        $template_contents = array(
            'GroupsList' => $this->out_GroupsList()
           ,'OrdersList' => $this->out_OrdersList()
           ,'SubscriptionsList' => $this->out_SubscriptionsList()
           ,'ActionUpdateSubscriptions' => $this->url_ActionUpdateSubscriptions()
           ,'CustomerId' => $this->customer_obj->base_info['ID']
           ,'Messages' => ''
           ,'Errors' => ''
           ,'MessageBox' => ''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/account_info/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        switch($tag)
        {
            case 'MessageBox':
                return $this->getMessageBox();
            case 'Messages':
                return $this->__msg;
            case 'Errors':
                return $this->__msg;
        }
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    function __format_date($date)
    {
        $arr = explode("-", array_shift(explode(' ', $date)));
        $ts = mktime(0,0,0,$arr[1],$arr[2],$arr[0]);
        return modApiFunc('Localization','timestamp_date_format',$ts);
    }

    var $_Template_Contents;
    var $customer_obj;
};

?>