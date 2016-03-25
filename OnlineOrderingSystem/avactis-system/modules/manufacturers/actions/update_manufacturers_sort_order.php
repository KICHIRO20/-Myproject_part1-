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

/**
 * @package Manufacturers
 * @author Vadim Lyalikov
 *
 */

class update_manufacturers_sort_order extends AjaxAction
{
    function update_manufacturers_sort_order()
    {
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $sort_order = $request->getValueByKey( 'ObjectList_hidden' );
        $sort_order_array = explode('|', $sort_order);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        if ($sort_order_array != NULL)
        {
            modApiFunc('Manufacturers', 'updateManufacturersSortOrder', $sort_order_array);
        }

        modApiFunc('Session','set','ResultMessage','MSG_SORD_ORDER_UPDATED');

        $r = new Request();
        $r->setView('Manufacturers');
        $application->redirect($r);
    }
};

?>