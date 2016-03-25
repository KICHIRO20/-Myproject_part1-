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

class import extends AjaxAction
{
    function import()
    {

    }

    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;
        $nErrors = 0;

        $selected_topics = $request->getValueByKey('topic_id');
        if(! is_array($selected_topics) || empty($selected_topics)) {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_SELECT_TOPICS_TO_SUBSCRIBE';
            $nErrors ++;
        }

        $emails = $request->getValueByKey('emails_subscribe');
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
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_FILL_EMAILS_TO_SUBSCRIBE';
            $SessionPost['ViewState']['ErrorFields'][] = 'emails_subscribe';
            $nErrors ++;
        }

        if ($nErrors == 0) {
            modApiStaticFunc('Subscriptions', 'subscribeEmails', array_keys($selected_topics), $valid_emails);
        }

        if (! empty($valid_emails) && ! empty($invalid_emails)) {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_SOME_EMAILS_INVALID';
            $SessionPost['ViewState']['ErrorFields'][] = 'emails_subscribe';
            $SessionPost['emails_subscribe'] = implode("\n", $invalid_emails);
            $nErrors ++;
        }

        if ($nErrors > 0) {
            $SessionPost['ViewState']['OpenSubform'] = 'subscribe';
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }

}
?>