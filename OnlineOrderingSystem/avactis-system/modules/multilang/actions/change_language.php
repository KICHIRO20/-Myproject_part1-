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
 * @package MultiLang
 * @author Sergey Kulitsky
 *
 */

class ChangeLanguage extends AjaxAction
{
    function ChangeLanguage()
    {
    }

    function onAction()
    {
        global $application;
        global $zone;

        // getting the request data
        $request = &$application -> getInstance('Request');
        $return_url = $request -> getValueByKey('returnURL');
        $lng = $request -> getValueByKey('lng');

        if ($zone == 'AdminZone')
        {
            setcookie('current_language_az', $lng, time() + 2592000);
            if ($adminID = modApiFunc('Users', 'getCurrentUserID'))
                modApiFunc('Users', 'updateAccountLanguage',
                           $adminID, $lng);
        }
        elseif ($zone == 'CustomerZone')
        {
            setcookie('current_language', $lng, time() + 2592000);
            if ($customer = modApiFunc('Customer_Account',
                                       'getCurrentSignedCustomer'))
                modApiFunc('Customer_Account', 'setAccountLanguage',
                                               $customer, $lng);
        }

        $req_to_redirect = new Request($return_url);
        $application -> redirect($req_to_redirect);
    }
}