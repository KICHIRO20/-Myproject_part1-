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

class customer_remove_email extends AjaxAction
{
    function customer_remove_email()
    {
        $this->account = modApiFunc('Customer_Account', 'getCurrentSignedCustomer');
        $this->signed_in = $this->account !== null;
    }

    function onAction()
    {
        global $application;

        $SessionPost = array('ViewState' => array());

        if ($this->signed_in) {
        	$this->email = modApiFunc('Request', 'getValueByKey', 'email');
        	$emails = modApiFunc('Subscriptions', 'getCustomerSubscriptionEmails', $this->account);
        	if (in_array($this->email, $emails)) {
        		execQuery('SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER', array('email' => $this->email, 'customer_id' => 0));
        		execQuery('SUBSCR_UNSUBSCRIBE_FROM_ALL', array('email' => $this->email));
            	$SessionPost['ViewState']['Messages'][] = getMsg('SUBSCR', 'MSG_SUBSCR_EMAIL_REMOVED');
        	}
        }
        else {

        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setAnchor('subscribe_box');
        $application->redirect($r);
    }

    var $account;
    var $signed_in;
}
?>