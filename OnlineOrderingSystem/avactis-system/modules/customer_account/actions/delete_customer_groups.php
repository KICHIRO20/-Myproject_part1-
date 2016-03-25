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
 * @author Alexey V. Astafyev
 *
 */

class delete_customer_groups extends AjaxAction
{
    function delete_customer_groups()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $customer_gids = $request->getValueByKey('customer_group_ids');
        if($customer_gids != null)
        {
            $customer_gids = explode('|',$customer_gids);
        }
        else
        {
            $customer_gids = array();
        };

        $errors = array();

        for($i=0; $i<count($customer_gids); $i++)
        {
            modApiFunc('Customer_Account','deleteCustomerGroup',$customer_gids[$i]);
        }

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_CUSTOMER_GROUPS_DELETED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$errors);
        };

        $request->setView('CustomerGroups');
        $application->redirect($request);
    }
};

?>