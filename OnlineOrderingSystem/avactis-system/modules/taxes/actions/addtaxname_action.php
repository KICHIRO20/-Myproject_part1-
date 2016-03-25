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
 *
 * @package Taxes
 * @author Alexander Girin
 */
class AddTaxNameAction extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AddTaxNameAction constructor.
     */
    function AddTaxNameAction()
    {
    }

    /**
     * Adds Product Tax Class.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $SessionPost = $_POST;
        $SessionPost["ViewState"]["hasCloseScript"] = "true";

        $included_into_price = $request->getValueByKey('included_into_price');
        $included_into_price = !empty($included_into_price);
        $SessionPost['included_into_price'] = (($included_into_price)? "true":"false");

        if($SessionPost['included_into_price'] == "false")
        {
            $address_type_id = $request->getValueByKey('Address');
        }
        else
        {
            $address_type_id = $request->getValueByKey('AddressForIncludedTaxes');
        }

        $needs_address = ($address_type_id != TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID);

        $id = modApiFunc('Taxes', 'addTaxName', (($included_into_price)? "true":"false"), $request->getValueByKey('TaxName'), $address_type_id, $needs_address);
        modApiFunc('Taxes', 'addTaxDisplayOption', '{'.$id.'}', 1, $request->getValueByKey('TaxName').":");
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
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