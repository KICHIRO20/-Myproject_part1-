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
 * Action handler on SetCurrentOrder.
 *
 * @package Checkout
 * @access  public
 * @author Alexey Kolesnikov
 */
class SetCurrentOrder extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function SetCurrentOrder()
    {
    }

    /**
     * @ describe the function SetCurrentOrder->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$order_id = $request->getValueByKey( 'order_id' );
    	$order_currency_id = $request->getValueByKey( 'order_currency_id' );

    	if ($order_id == null)
    	{
    		return;
    	}
    	modApiFunc('Checkout', 'setCurrentOrderID', $order_id);
        if ($request->getValueByKey('delete'))
        {
            modApiFunc("Checkout", "setDeleteOrdersFlag", "true");
        }
        else
        {
            modApiFunc("Checkout", "setDeleteOrdersFlag", "false");
        }

        if($order_currency_id !== NULL &&
           !empty($order_currency_id))
        {
        	//     NULL -                           Request
        	modApiFunc("Checkout", "setCurrentOrderCurrencyID", $order_currency_id);
        }
        else
        {
        	//     NULL -                            OrderInfo   Checkout
        	//                    OrderCurrency
            modApiFunc("Checkout", "setCurrentOrderCurrencyID", NULL);
        }
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