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

class change_account_password extends AjaxAction
{
    function change_account_password()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $account_name = modApiFunc('Customer_Account','getCurrentSignedCustomer');

        $errors = array();

        if($account_name != null)
        {
            $current_password = $request->getValueByKey('current_password');
            if(!modApiFunc('Customer_Account','isCorrectAccountAndPasswd',$account_name,$current_password))
            {
                $errors[] = 'E_INVALID_CURRENT_PASSWD';
            }
            else
            {
                $validator = &$application->getInstance('CAValidator');
                $passwd = array(
                    'passwd' => $request->getValueByKey('new_password')
                   ,'re-type' => $request->getValueByKey('retype_password')
                );
                if(!$validator->isValid('passwd',$passwd))
                {
                    $errors[] = 'E_INVALID_PASSWD';
                };
            };

            if(!empty($errors))
            {
                modApiFunc('Session','set','RegisterErrors',$errors);
            }
            else
            {
                modApiFunc('Session','set','ResultMessage','MSG_PASSWD_UPDATED');

                $obj = &$application->getInstance('CCustomerInfo',$account_name);
                $obj->changePassword($passwd['passwd']);
            };
        };

        $request->setView('CustomerChangePassword');
        $application->redirect($request);
    }
};

?>