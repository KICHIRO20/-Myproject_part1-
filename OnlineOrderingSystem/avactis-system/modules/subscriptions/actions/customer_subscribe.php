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

class customer_subscribe extends AjaxAction
{
    function customer_subscribe()
    {
        $this->account = modApiFunc('Customer_Account', 'getCurrentSignedCustomer');
        $this->signed_in = $this->account !== null;
    }

    function onAction()
    {
        global $application;

        $this->topics = modApiFunc('Request', 'getValueByKey', 'topic');
        if (empty($this->topics)) {
            $this->topics = array();
        }

        $SessionPost = array();

        $this->email = trim(modApiFunc('Request', 'getValueByKey', 'email'));
        if (modApiFunc('Users', 'isValidEmail', $this->email)) {
            if (modApiFunc('Subscriptions', 'canClientUnsubscribe')) {
                $ViewState = $this->changeSubscriptions();
            }
            else {
                $ViewState = $this->addSubscriptions();
            }
            $SessionPost['ViewState'] = $ViewState;

            if ($this->signed_in) {
	        	$params = array(
    	            	'account' => $this->account,
        	        	'email' => $this->email,
            		    );
        		execQuery('SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER', $params);
            }
            else {
            	modApiFunc('Subscriptions', 'setCustomerSubscribedEmail', $this->email);
            }
        }
        else {
            $SessionPost['ViewState']['ErrorsArray'][] = getMsg('SUBSCR', 'ERROR_SUBSCR_INVALID_EMAIL');
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setAnchor('subscribe_box');
        $application->redirect($r);
    }

    function changeSubscriptions()
    {
        return modApiFunc('Subscriptions', 'changeSubscriptions', $this->email, $this->topics, $this->signed_in);
    }

    function addSubscriptions()
    {
        return modApiFunc('Subscriptions', 'subscribeEmails', $this->topics, $this->email);
    }

    var $signed_in;
}
?>