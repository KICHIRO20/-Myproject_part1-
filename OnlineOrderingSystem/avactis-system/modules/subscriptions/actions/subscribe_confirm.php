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

class subscribe_confirm extends AjaxAction
{
    function subscribe_confirm()
    {

    }

    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');

        $SessionPost = array();
        /*
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }
        */

        $SessionPost = $_POST;
        $nErrors = 0;

        $key = $request->getValueByKey('action_key');
        // @ check key
        $topics = $request->getValueByKey('topics');
        $selected_topics = explode(',', $topics);
        if(! is_array($selected_topics) || empty($selected_topics)) {
            // @ INTERNAL
            $SessionPost['ViewState']['ErrorsArray'][] = 'INTERNAL';
            $nErrors ++;
        }

        modApiFunc('Subscriptions', 'copyTempEmails', $key);
        modApiFunc('Subscriptions', 'linkTempEmails', $key);
        modApiFunc('Subscriptions', 'subscribeTempEmails', $key, $selected_topics);
        modApiFunc('Subscriptions', 'cleanTempEmails', $key);
        execQuery('SUBSCR_LINK_CUSTOMER_EMAILS', null);
        execQuery('SUBSCR_LINK_ORDERS_EMAILS', null);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView('Subscriptions_Manage');
//        $request->setKey('stage', 'finish');
        $application->redirect($request);
    }

}
?>