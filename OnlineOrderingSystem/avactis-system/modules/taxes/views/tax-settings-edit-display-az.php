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
_use(dirname(__FILE__).'/tax-settings-add-display-az.php');

/**
 * Checkout Module, EditTaxName View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class EditTaxDisplayOption extends AddTaxDisplayOption
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
        $TaxInfo = modApiFunc("Taxes", "getTaxDisplayOptionInfo", modApiFunc("Taxes", "getEditableTaxId", "TaxDisplayOption"));

        $replace = array();
        foreach ($this->TaxList as $taxInfo)
        {
            $replace['{'.$taxInfo['Id'].'}'] = $taxInfo['Name'];
        }

        $this->POST  =
            array(
                "FormulaView"           => strtr($TaxInfo['Formula'], $replace)
               ,"Formula"               => $TaxInfo['Formula']
               ,"Id"                    => $TaxInfo['Id']
               ,"OptionId"              => $TaxInfo['OptionId']
               ,"Display"               => $TaxInfo['Display']
            );
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('EditTaxDisplayOption');
        $request->setAction('UpdateTaxDisplayAction');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        return $this->MessageResources->getMessage('EDIT_TAX_DISPLAY_OPTION_PAGE_SUBTITLE');
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