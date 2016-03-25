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

class drop_accounts_passwords extends AjaxAction
{
    function drop_accounts_passwords()
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

            if(modApiFunc('Customer_Account','getAccountStatus',$account_name) == 'B')
            {
                continue;
            };

            if(!modApiFunc('Customer_Account','dropCustomerPassword',$account_name,'customer_account'))
            {
                $errors[] = 'E_PASSWORDS_NOT_DROPED';
                break;
            }
            else
            {
                modApiFunc('EventsManager','throwEvent','AdminDropCustomerPassword',$account_name);
            };
        };

        if(empty($errors))
        {
            modApiFunc('Session','set','ResultMessage','MSG_PASSWORDS_DROPED');
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