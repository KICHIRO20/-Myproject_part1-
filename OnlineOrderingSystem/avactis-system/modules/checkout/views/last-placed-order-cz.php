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
 * Checkout module.
 * Last Placed Order page.
 *
 * @package Checkout
 * @access  public
 * @author  Sergey Kulitsky
 */
class LastPlacedOrder
{
    function output()
    {
        if (modApiFunc('Checkout', 'getCurrentStepID') == 4)
            return getCheckout();

        if (modApiFunc('Session', 'is_Set', '_lastPlacedOrderID'))
            return getOrderInfo(modApiFunc('Session', 'get',
                                           '_lastPlacedOrderID'));

        global $application;

        $request = new Request();
        $request -> setView('Checkout');
        $application -> redirect($request);
    }
}