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

class delete_emails extends AjaxAction
{
    function delete_topics()
    {

    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $topics_ids = $request->getValueByKey( 'topic_id' );
        $emails_ids = $request->getValueByKey( 'email_id' );
        modApifunc('Subscriptions', 'deleteEmails', $topics_ids, $emails_ids);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('topic', reset($topics_ids));
        $application->redirect($request);
    }

}
?>