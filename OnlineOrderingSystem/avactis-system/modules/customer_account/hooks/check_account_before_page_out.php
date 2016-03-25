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

class CheckAccountBeforePageOut
{
    function CheckAccountBeforePageOut()
    {}

    function onHook()
    {
        global $zone;
        if($zone == 'CustomerZone')
        {
            global $application;
            $sections = $application->getSectionByCurrentPagename();
            $settings = modApiFunc('Customer_Account','getSettings');

            if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_ACCOUNT_REQUIRED)
            {
                $this->redirect_scheme_not_signed['CustomerSignIn'][] = 'Checkout';
            };

            $use_scheme = modApiFunc('Customer_Account','getCurrentSignedCustomer') == null ? $this->redirect_scheme_not_signed : $this->redirect_scheme_signed;

            foreach($use_scheme as $to_view => $from_views)
            {
                if(array_diff($from_views, $sections) != $from_views)
                {
                    $request = new Request();
                    $request->setView($to_view);
                    $application->redirect($request);

                    if(in_array('Checkout',$sections))
                    {
                        modApiFunc('Session','set','toCheckoutAfterSignIn',true);
                        modApiFunc('Session','set','ResultMessage','MSG_NEED_REGISTER');
                    };

                    break;
                };
            };
        };
    }

    var $redirect_scheme_not_signed = array(
        'CustomerSignIn' => array (
                    'CustomerPersonalInfo', 'CustomerOrdersHistory', 'CustomerOrderInfo'
                   ,'CustomerOrderInvoice', 'CustomerOrderPackingSlip', 'CustomerOrderDownloadLinks'
                   ,'CustomerChangePassword', 'CustomerAccountHome', 'CustomerSubscription'
                )
    );

    var $redirect_scheme_signed = array(
        'CustomerAccountHome' => array('Registration', 'CustomerSignIn', 'CustomerForgotPassword')
    );
};

?>