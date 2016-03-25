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

class customer_sign_in extends AjaxAction
{
    function customer_sign_in()
    {}

    function onAction()
    {
        global $application;
        $request = new Request();

        $email = $request->getValueByKey('email');
        $passwd = $request->getValueByKey('passwd');
        $remember_me = $request->getValueByKey('remember_me');

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);

        if(!modApiFunc('Customer_Account','isCorrectAccountAndPasswd',$email,$passwd))
        {
            modApiFunc('Session','set','RegisterErrors',array('E_INVALID_SIGN_IN_INFO'));
        }
        else
        {
            $customer_obj = &$application->getInstance('CCustomerInfo',$email);
            if($customer_obj->getPersonInfo('Status') != 'A')
            {
                modApiFunc('Session','set','RegisterErrors',array('E_ACCOUNT_NOT_ACTIVATED'));
            }
            else
            {
                $customer_obj->SignIn();
                if(modApiFunc('Session','is_set','toCheckoutAfterSignIn'))
                {
                    modApiFunc('Session','un_set','toCheckoutAfterSignIn');
                    $r->setView('CheckoutView');
                }
                elseif (modApiFunc('Session','is_set','toURLAfterSignIn'))
                {
                    $target_url = modApiFunc('Session','get','toURLAfterSignIn');
                    if ($target_url != '')
                        $r = new Request($target_url);
                    modApiFunc('Session','un_set','toURLAfterSignIn');
                }

                if (!empty($customer_obj->affiliate_id))
                    modApiFunc('Session','set','AffiliateID',$customer_obj->affiliate_id);

                if ( modApiFunc('Settings','getParamValue','CUSTOMER_ACCOUNT_SETTINGS','ENABLE_SAVE_SESSION') === 'YES' )
                {

                    $t=time()+3600*24;

                    $value = ($remember_me == "save")?$remember_me:"shared";

                    setcookie('save_session',$value,$t, '/');

                }
                else
                {
                    setcookie('save_session','',0, '/');
                }
            }
        }

        $application->redirect($r);
    }
};

?>