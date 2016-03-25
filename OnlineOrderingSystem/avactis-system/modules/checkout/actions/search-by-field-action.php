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
 * Action handler on CustomersSearchByField.
 *
 * @package Checkout
 * @access  public
 */
class CustomersSearchByField extends AjaxAction
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
    function CustomersSearchByField()
    {
    }

    /**
     * @ describe the function CustomersSearchByLetter->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$field_name = $request->getValueByKey( 'field_name' );
    	$field_value = $request->getValueByKey( 'field_value' );
    	$search_array = modApiFunc('Checkout', 'getCustomerSearchFilter');
    	$search_array['field_name'] = $field_name;
    	$search_array['field_value'] = $field_value;
    	$search_array['search_by'] = 'field';
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