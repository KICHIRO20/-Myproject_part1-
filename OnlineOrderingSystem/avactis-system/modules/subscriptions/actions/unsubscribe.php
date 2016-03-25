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

class unsubscribe extends AjaxAction
{
    function unsubscribe()
    {

    }

    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');
        $topic = $request->getValueByKey('topic');

        $SessionPost = array();
        /*
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }
        */

        $SessionPost = $_POST;
        $nErrors = 0;

        $selected_topics = $request->getValueByKey('topic_id');
        if(! is_array($selected_topics) || empty($selected_topics)) {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_SELECT_TOPICS_TO_UNSUBSCRIBE';
            $nErrors ++;
        }

        $emails = $request->getValueByKey('emails_unsubscribe');
        $emails = preg_split('/[\s,;]+/', $emails);
        $valid_emails = array();
        $invalid_emails = array();
        foreach ($emails as $email) {
            if (modApiFunc("Users", "isValidEmail", $email)) {
                $valid_emails[] = $email;
            }
            else {
                $invalid_emails[] = $email;
            }
        }

        if (empty($valid_emails)) {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_FILL_EMAILS_TO_UNSUBSCRIBE';
            $SessionPost['ViewState']['ErrorFields'][] = 'emails_unsubscribe';
            $nErrors ++;
        }
        if ($nErrors == 0) {
            modApiStaticFunc('Subscriptions', 'unsubscribeEmails', array_keys($selected_topics), $valid_emails);
        }

        if (! empty($valid_emails) && ! empty($invalid_emails)) {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_SOME_EMAILS_INVALID';
            $SessionPost['ViewState']['ErrorFields'][] = 'emails_unsubscribe';
            $SessionPost['emails_unsubscribe'] = implode("\n", $invalid_emails);
            $nErrors ++;
        }

        if ($nErrors > 0) {
            $SessionPost['ViewState']['OpenSubform'] = 'unsubscribe';
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        if (! empty($topic)) {
            $request->setKey('topic', $topic);
        }
        $application->redirect($request);
    }

}
?>