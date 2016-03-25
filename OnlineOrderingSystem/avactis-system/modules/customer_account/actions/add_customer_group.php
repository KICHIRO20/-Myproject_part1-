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

class add_customer_group extends AjaxAction
{
    function add_customer_group()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $errors = array();
        modApiFunc('Customer_Account','addCustomerGroup',$request->getValueByKey('new_customer_group'));

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_CUSTOMER_GROUP_ADDED');
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