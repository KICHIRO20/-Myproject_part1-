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

class SubscribeBox extends SubscribeForm_Base
{
    function SubscribeBox()
    {
        $this->ini_section = 'SubscribeBox';
        $this->email = modApiFunc('Subscriptions', 'getCustomerSubscribedEmail');
     	$this->emails = array();
        if (! empty($this->email)) {
        	$this->emails[] = $this->email;
        }
        $this->SubscribeForm_Base();
    }
}

?>