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
class OrdersSearchByStatus extends AjaxAction
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
    function OrdersSearchByStatus()
    {
    }

    /**
     * @ describe the function OrdersSearchByDate->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$status_id = $request->getValueByKey( 'status_id' );
    	$search_array = modApiFunc('Checkout', 'getOrderSearchFilter');
    	if ($status_id == null)
    	{
         	# clear the search
        	$search_array['search_by'] = null;
        	modApiFunc('Checkout', 'setOrderSearchFilter', $search_array);
            modApiFunc('paginator', 'setPaginatorPage', "Checkout_Orders", 1);
    		return;
    	}

    	$search_array['filter_status_id'] = $status_id;
    	$search_array['search_by'] = 'status';
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