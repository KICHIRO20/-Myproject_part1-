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
 *        Hook                action checkout 'SetCurrentStep'
 *
 *                                                                 "Credit Card Info",
 * "Bank Account Info"
 *         Checkout CZ ,                          Checkout
 *                         "Credit Card", "Bank Account"                          .
 *                                   -                     .
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class AdditionalPersonInfoSetCurrStepHook
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     */
    function AdditionalPersonInfoSetCurrStepHook()
    {
    }

    /**
     *
     */
    function onHook()
    {
        global $application;
        $request = &$application->getInstance("Request");

        #                          ,                                                    CCInfo
        if ($paymentModule = $request->getValueByKey("paymentModule"))
        {
            $pm_id = $paymentModule["method_code"];
            $mmObj = &$application->getInstance('Modules_Manager');
            $mmObj->includeAPIFileOnce("Checkout");
            Checkout::AdditionalPersonInfoSetCurrStepHook($pm_id);
        }
        modApiFunc("Checkout", "saveState");
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