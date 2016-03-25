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

class update_customers_accounts extends AjaxAction
{
    function update_customers_accounts()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $customers_ids = $request->getValueByKey('customers_ids');
        $customers_groups = $request->getValueByKey('customers_groups');
        if($customers_ids != null)
        {
            $customers_ids = explode('|',$customers_ids);
        }
        else
        {
            $customers_ids = array();
        };
        if($customers_groups != null)
        {
            $customers_groups = explode('|',$customers_groups);
            $arr = array();
            foreach($customers_groups as $cg)
            {
                $kv = explode('>>=',$cg);
                $arr[$kv[0]] = $kv[1];
            }
            $customers_groups = $arr;
        }
        else
        {
            $customers_groups = array();
        };

        $errors = array();

        for($i=0; $i<count($customers_ids); $i++)
        {
            $account_name = modApiFunc('Customer_Account','getCustomerAccountNameByCustomerID',$customers_ids[$i]);
            if($account_name == null)
                continue;

            modApiFunc('Customer_Account','updateCustomerAccountGroup',$account_name,$customers_groups[$customers_ids[$i]]);
        }

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_CUSTOMERS_UPDATED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$errors);
        };

        $request->setView('Customers');
        $application->redirect($request);
    }
};

?>