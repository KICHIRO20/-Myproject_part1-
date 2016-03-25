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
class SetCurrentCustomer extends AjaxAction
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
    function SetCurrentCustomer()
    {
    }

    /**
     * @  describe the function SetCurrentOrder->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$customer_id = $request->getValueByKey( 'customer_id' );
    	if ($customer_id == null)
    	{
    		return;
    	}
    	modApiFunc('Checkout', 'setCurrentCustomerID', $customer_id);
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