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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class update_group_sort_order extends AjaxAction
{
    function update_group_sort_order()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $group_name = $request->getValueByKey('GroupName');
        $sort_order = $request->getValueByKey('SortOrder_hidden');

        if($group_name != null and $sort_order != null)
        {
            $sort_order = explode("|",$sort_order);
            modApiFunc('Customer_Account','updateGroupAttrsSortOrder',$group_name,$sort_order);
        };

        modApiFunc('Session','set','ResultMessage','MSG_SORT_ORDER_UPDATED');

        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }
};

?>