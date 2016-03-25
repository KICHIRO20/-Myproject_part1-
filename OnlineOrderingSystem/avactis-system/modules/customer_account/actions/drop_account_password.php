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

class drop_account_password extends AjaxAction
{
    function drop_account_password()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $account_name = $request->getValueByKey('account_name');
        $errors = array();

        if(!modApiFunc('Customer_Account','doesAccountExists',$account_name))
        {
            $errors[] = 'E_NOT_EXISTS_ACCOUNT_NAME';
        }
        elseif(modApiFunc('Customer_Account','getAccountStatus',$account_name) != 'A')
        {
            $errors[] = 'E_NOT_ACTIVE_ACCOUNT';
        }
        else
        {
            if(!modApiFunc('Customer_Account','dropCustomerPassword',$account_name))
            {
                $errors[] = 'E_UNKNOWN';
            };
        };

        if(!empty($errors))
        {
            modApiFunc('Session','set','RegisterErrors',$errors);
        }
        else
        {
            modApiFunc('Session','set','ResultMessage','MSG_PASSWD_DROPED');
            modApiFunc('EventsManager','throwEvent','CustomerPasswordDroped',$account_name);
        };

        $request->setView('CustomerForgotPassword');
        $application->redirect($request);
    }
};

?>