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
 * Action handler on OrdersSearchByDate.
 *
 * @package Checkout
 * @access  public
 */
class OrdersSearchById extends AjaxAction
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
    function OrdersSearchById()
    {
    }

    /**
     * @ describe the function OrdersSearchByDate->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$order_id = $request->getValueByKey( 'order_id' );
    	$search_array = modApiFunc('Checkout', 'getOrderSearchFilter');
    	//: : more accurate type check.
    	$search_array['order_id'] = is_numeric($order_id) ? $order_id : "";
    	$search_array['search_by'] = 'id';
    	modApiFunc('Checkout', 'setOrderSearchFilter', $search_array);
        modApiFunc('paginator', 'setPaginatorPage', "Checkout_Orders", 1);
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