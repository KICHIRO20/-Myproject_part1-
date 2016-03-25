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

class save_account_password extends AjaxAction
{
    function save_account_password()
    {}

    function onAction()
    {
        global $application;

        $request = new Request();
        $key = $request->getValueByKey('key');

        $passwd = array(
            'passwd' => $request->getValueByKey('new_password')
           ,'re-type' => $request->getValueByKey('retype_password')
        );


        $account_name = modApiFunc('Customer_Account','getAccountByActivationKey',$key);
        $errors = array();

        if($account_name != null)
        {
            $validator = &$application->getInstance('CAValidator');
            if(!$validator->isValid('passwd',$passwd))
            {
                $errors[] = 'E_INVALID_PASSWD';
            };

        };

        if(!empty($errors))
        {
            modApiFunc('Session','set','RegisterErrors',$errors);

            $request->setView('CustomerNewPassword');
            $request->setKey('key',$key);
        }
        else
        {
            modApiFunc('Session','set','ResultMessage','MSG_PASSWD_UPDATED');
            modApiFunc('Customer_Account','dropActivationKey',$account_name,'customer_account');
            $obj = &$application->getInstance('CCustomerInfo',$account_name);
            $obj->setPersonInfo(array(array('Status','A','base')));
            $obj->changePassword($passwd['passwd']);
            $obj->SignIn();

            $request->setView('CustomerAccountHome');
        };


        $application->redirect($request);
    }
};

?>