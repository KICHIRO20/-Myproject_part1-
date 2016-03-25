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

class activate_customers_accounts extends AjaxAction
{
    function activate_customers_accounts()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $customers_ids = $request->getValueByKey('customers_ids');
        if($customers_ids != null)
        {
            $customers_ids = explode('|',$customers_ids);
        }
        else
        {
            $customers_ids = array();
        };

        $errors = array();

        for($i=0; $i<count($customers_ids); $i++)
        {
            $account_name = modApiFunc('Customer_Account','getCustomerAccountNameByCustomerID',$customers_ids[$i]);
            if($account_name == null)
                continue;

            $customer_obj = &$application->getInstance('CCustomerInfo',$account_name);
            if($customer_obj->getPersonInfo('Status') != 'N')
            {
                continue;
            };

            $customer_obj->setPersonInfo(array(array('Status','A', 'base')));
            modApiFunc('EventsManager','throwEvent','AdminActivateCustomer',$account_name);
            modApiFunc('Customer_Account','dropActivationKey',$account_name,'customer_account');
        }

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_CUSTOMERS_ACTIVATED');
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