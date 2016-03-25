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

class delete_topics extends AjaxAction
{
    function delete_topics()
    {

    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $topics = $request->getValueByKey( 'topics' );
        $topics_ids = explode(',', $topics);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        if ($topics_ids != NULL) {
            modApiFunc('Subscriptions', 'deleteTopics', $topics_ids);
        }
    }

}
?>