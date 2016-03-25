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

class delete_customers_accounts extends AjaxAction
{
    function delete_customers_accounts()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $customers_ids = $request->getValueByKey('customers_ids');
        if($customers_ids != null)
        {
            $customers_ids = explode('|',$customers_ids);
        };

        if(modApiFunc('Customer_Account','deleteCustomers',$customers_ids))
        {
            modApiFunc('Session','set','ResultMessage','MSG_CUSTOMERS_DELETED');
        }
        else
        {
            modApiFunc('Session','set','Errors',array('E_CUSTOEMRS_NOT_DELETED'));
        };

        $request->setView('Customers');
        $application->redirect($request);
    }
};

?>