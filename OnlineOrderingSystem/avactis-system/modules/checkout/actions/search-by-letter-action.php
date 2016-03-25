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
 * Action handler on CustomersSearchByLetter.
 *
 * @package Checkout
 * @access  public
 */
class CustomersSearchByLetter extends AjaxAction
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
    function CustomersSearchByLetter()
    {
    }

    /**
     * @ describe the function CustomersSearchByLetter->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$letter = $request->getValueByKey( 'letter' );
    	$search_array = modApiFunc('Checkout', 'getCustomerSearchFilter');
    	$search_array['letter'] = $letter;
    	$search_array['search_by'] = 'letter';
    	modApiFunc('Checkout', 'setCustomerSearchFilter', $search_array);
        modApiFunc('paginator', 'setPaginatorPage', "Checkout_Customers", 1);
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