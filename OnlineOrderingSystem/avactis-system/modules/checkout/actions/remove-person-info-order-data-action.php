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
 * Action handler for RemovePersonInfo.
 *
 * @package Checkout
 * @access  public
 * @author Vadim Lyalikov
 */
class RemovePersonInfoOrderData extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     *
     * @ finish the functions on this page
     */
    function RemovePersonInfoOrderData()
    {
    }

    /**
     * @                      SetCurrentOrder->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$order_id = $request->getValueByKey( 'order_id' );
    	$person_info_variant_id = $request->getValueByKey( 'person_info_variant_id' );

        if(!is_numeric($order_id) ||
           !is_numeric($person_info_variant_id))
        {
            exit();
            //: report error.
        }
        else
        {
            modApiFunc('Checkout', 'removePersonInfoOrderData', $order_id, $person_info_variant_id);
            modApiFunc("Checkout", "log_person_info_removal",   $order_id, $person_info_variant_id);
            $MessageResources = &$application->getInstance('MessageResources');
            $msg = $MessageResources->getMessage("CHECKOUT_ORDER_INFO_REMOVE_ENCRYPTED_PERSON_INFO_SUCCESS_MSG");
            echo "<script language='javascript'>/*alert('".$msg."');*/parent.location.href = parent.location.href;</script>";
            exit();
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