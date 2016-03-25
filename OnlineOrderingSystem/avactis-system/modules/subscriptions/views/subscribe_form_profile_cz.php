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

loadModuleFile('subscriptions/abstract/subscribe_cz.php');

/**
 * @package Subscriptions
 * @author
 *
 */

class SubscribeFormProfile extends SubscribeForm_Base
{
    function SubscribeFormProfile()
    {
        $this->ini_section = 'SubscribeFormProfile';
        $account = modApiFunc('Customer_Account', 'getCurrentSignedCustomer');
        $this->emails = modApiFunc('Subscriptions', 'getCustomerSubscriptionEmails', $account);
        $this->SubscribeForm_Base();
    }
}

?>