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
_use(dirname(__FILE__).'/tax-settings-add-class-az.php');

/**
 * Checkout Module, EditTaxClass View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class EditTaxClass extends AddTaxClass
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
        $TaxInfo = modApiFunc("Taxes", "getTaxClassInfo", modApiFunc("Taxes", "getEditableTaxId", "TaxClass"));
        $this->POST  =
            array(
                "name"               => $TaxInfo['name']
               ,"id"                 => $TaxInfo['id']
               ,"descr"              => $TaxInfo['descr']
            );
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('EditTaxClass');
        $request->setAction('UpdateProdTaxClass');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');
        return $MessageResources->getMessage('EDIT_TAX_CLASS_PAGE_SUBTITLE');
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