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

class unsubscribe_by_link extends AjaxAction
{
    function unsubscribe_by_link()
    {
    }

    function onAction()
    {
        $errors = array();
        $topics = modApiFunc('Request', 'getValueByKey', 'topic');

        if ($topics && is_array($topics)) {
            $this->key = modApiFunc('Request', 'getValueByKey', 'key_unsubscribe');
            $this->rec = modApiFunc('Newsletter', 'getUnsubscribeRecord', $this->key);
            if ($this->rec) {
                $email = modApiFunc('Subscriptions', 'getEmailById', $this->rec['email_id']);
                if ($email) {
                    modApiFunc('Subscriptions', 'unsubscribeEmails', $topics, $email);
                }
                else {
                    $errors[] = getMsg('SUBSCR', 'ERROR_UNSUBSCRIBE_GENERAL');
                }
            }
            else {
                $errors[] = getMsg('SUBSCR', 'ERROR_UNSUBSCRIBE_GENERAL');
            }
        }
        else {
            $errors[] = getMsg('SUBSCR', 'ERROR_UNSUBSCRIBE_NO_TOPICS');
        }

        $SessionPost['ViewState']['ErrorsArray'] = $errors;
        $SessionPost['ViewState']['Stage'] = 'finish';
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        global $application;
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }
}
?>