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
 * CheckoutStep Link view.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class CheckoutStepLink
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  CheckoutStepLink constructor.
     */
    function CheckoutStepLink()
    {
    }

    /**
     * Returns the generated ProductList view.
     *
     * @return string
     */
    function output()
    {
        global $application;

        $arg_list = func_get_args();
        if(sizeof($arg_list) == 1)
        {
            $step_id = $arg_list[0];
        }
        else
        {
            $step_id = modApiFunc("Checkout", "getCurrentStepID");
        }

        $request = new Request();
        $request->setAction('SetCurrStep');
        $request->setView('CheckoutView');
        $request->setKey('step_id', $step_id);
        $request = modApiFunc("Checkout", "appendCheckoutCZGETParameters", $request);
        return $request->getURL();
    }

    /**#@-*/
//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------
    /**#@+
     * @access private
     */
    /**#@-*/
}
?>