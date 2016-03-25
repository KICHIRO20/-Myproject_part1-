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

class update_customer_subscriptions extends AjaxAction
{
    function update_customer_subscriptions()
    {

    }

    function onAction()
    {
        global $application;
        $emails_keys = modApiFunc('Request', 'getValueByKey', 'emails');
        $emails_topics = modApiFunc('Request', 'getValueByKey', 'topic');
        $customer_id = modApiFunc('Request', 'getValueByKey', 'customer_id');
        if (! is_array($emails_topics)) {
            $emails_topics = array();
        }
        foreach (array_keys($emails_keys) as $email) {
            $topics = @$emails_topics[$email];
            if (! is_array($topics)) {
                $topics = array();
            }
            modApiFunc('Subscriptions', 'changeSubscriptions', $email, $topics);
            $params = array(
                    'customer_id' => $customer_id,
                    'email' => $email,
                    );
            execQuery('SUBSCR_LINK_SUBSCRIPTION_TO_CUSTOMER', $params);
        }

        $messages['MESSAGES'][] = getMsg('SYS', 'SUBSCRIPTIONS_UPDATED');
        modApiFunc('Session', 'set', 'AplicationSettingsMessages', $messages);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('page_view', modApiFunc('Request', 'getValueByKey', 'page_view'));
        $request->setKey('customer_id', $customer_id);
        $application->redirect($request);
    }

}

?>