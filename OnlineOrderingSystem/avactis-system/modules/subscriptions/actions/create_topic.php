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

class create_topic extends AjaxAction
{
    function create_topic()
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

        $topic_name = $request->getValueByKey('topic_name');
        $sort_order = modApiStaticFunc('Subscriptions', 'getNextTopicOrder');
        $topic_status = $request->getValueByKey('topic_status');
        $topic_access = $request->getValueByKey('topic_access');
        $topic_auto = $request->getValueByKey('topic_auto');
        if ($topic_name != '') {
            modApiStaticFunc('Subscriptions', 'createTopic', $topic_name, $sort_order, $topic_status, $topic_access, $topic_auto);
        }
        else {
            $SessionPost['ViewState']['ErrorsArray'][] = 'ALERT_FILL_TOPIC_NAME';
            $SessionPost['ViewState']['ErrorFields'][] = 'topic_name';
            $SessionPost['ViewState']['hasCloseScript']= 'false';
        }
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }

}
?>