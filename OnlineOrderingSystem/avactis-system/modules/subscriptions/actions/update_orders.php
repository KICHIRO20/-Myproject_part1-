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

class update_orders extends AjaxAction
{
    function update_orders()
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

        $sort_orders = $request->getValueByKey('sort_order');
        if (! empty($sort_orders) && is_array($sort_orders)) {
            foreach($sort_orders as $topic_id => $sort_order) {
                modApiStaticFunc('Subscriptions', 'updateOrder', $topic_id, $sort_order);
            }
        }
    }

}
?>