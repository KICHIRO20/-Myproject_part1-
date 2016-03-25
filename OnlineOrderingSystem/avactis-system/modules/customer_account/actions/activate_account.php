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

class activate_account extends AjaxAction
{
    function activate_account()
    {}

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $key = $request->getValueByKey('key');

        $account = modApiFunc('Customer_Account','getAccountByActivationKey',$key);

        if($account !== null)
        {
            if(modApiFunc('Customer_Account','setAccountStatus',$account,'A'))
            {
                modApiFunc('Customer_Account','dropActivationKey',$account,'customer_account');
                modApiFunc('Session','set','ResultMessage','MSG_ACTIVATED');

                modApiFunc('EventsManager','throwEvent','CustomerActivateSelf',$account);

                //
                $obj = &$application->getInstance('CCustomerInfo',$account);
                $obj->SignIn();
            }
            else
            {
                modApiFunc('Session','set','RegisterErrors',array('E_NOT_ACTIVATED'));
            };
        }
        else
        {
            modApiFunc('Session','set','RegisterErrors',array('E_INVALID_KEY'));
        };

        $r = new Request();
        $r->setView('AccountActivation');
        $application->redirect($r);
    }
};

?>