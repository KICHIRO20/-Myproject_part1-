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
_use(dirname(__FILE__).'/tax-settings-add-name-az.php');

/**
 * Checkout Module, EditTaxName View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class EditTaxName extends AddTaxName
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $TaxInfo = modApiFunc("Taxes", "getTaxNameInfo", modApiFunc("Taxes", "getEditableTaxId", "TaxName"));
        $this->POST  =
            array(
                "Edit"                  => true
               ,"included_into_price"   => $TaxInfo['included_into_price']
               ,"TaxName"               => $TaxInfo['Name']
               ,"Id"                    => $TaxInfo['Id']
               ,"Address"               => $TaxInfo['AddressId']
            );
        if($TaxInfo['needs_address'] == DB_FALSE)
        {
            $this->POST["Address"]  = TAXES_TAX_NAME_DOES_NOT_NEED_ADDRESS_ID;
        }
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('EditTaxName');
        $request->setAction('UpdateTaxNameAction');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');
        return $MessageResources->getMessage('EDIT_TAX_NAME_PAGE_SUBTITLE');
    }

    function outputButton()
    {
        return $this->MessageResources->getMessage('BTN_UPDATE');
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