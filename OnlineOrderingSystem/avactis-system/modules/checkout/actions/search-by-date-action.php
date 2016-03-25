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
class OrdersSearchByDate extends AjaxAction
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
    function OrdersSearchByDate()
    {
    }

    /**
     * @ describe the function OrdersSearchByDate->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$from_day = $request->getValueByKey( 'from_day' );
    	$from_month = $request->getValueByKey( 'from_month' );
    	$from_year = $request->getValueByKey( 'from_year' );
    	$to_day = $request->getValueByKey( 'to_day' );
    	$to_month = $request->getValueByKey( 'to_month' );
    	$to_year = $request->getValueByKey( 'to_year' );
    	$status_id = $request->getValueByKey( 'status_id' );
    	$status_ids = $request->getValueByKey( 'order_status' );
    	$payment_status_id = $request->getValueByKey( 'payment_status_id' );
    	$payment_status_ids = $request->getValueByKey( 'payment_status' );
        $affiliate_id = $request->getValueByKey( 'affiliate_id' );

    	$search_array = modApiFunc('Checkout', 'getOrderSearchFilter');

    	$search_array['from_day'] = $from_day;
    	$search_array['from_month'] = $from_month;
    	$search_array['from_year'] = $from_year;
    	$search_array['to_day'] = $to_day;
    	$search_array['to_month'] = $to_month;
    	$search_array['to_year'] = $to_year;
    	$search_array['status_id'] = $status_id;
    	$search_array['payment_status_id'] = $payment_status_id;
    	$search_array['search_by'] = 'date';
    	$search_array['order_statuses'] = $status_ids;
    	$search_array['payment_statuses'] = $payment_status_ids;
        $search_array['affiliate_id'] = $affiliate_id;
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