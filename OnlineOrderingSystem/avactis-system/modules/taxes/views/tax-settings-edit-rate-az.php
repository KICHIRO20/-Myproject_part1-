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
_use(dirname(__FILE__).'/tax-settings-add-rate-az.php');

/**
 * Taxes Module, EditTaxRate View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class EditTaxRate extends AddTaxRate
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
               ,"TaxRateAction"  => ""
                 );
        $TaxInfo = modApiFunc("Taxes", "getTaxRateInfo", modApiFunc("Taxes", "getEditableTaxId", "TaxRate"));

        $replace = array();
        $TaxNamesList = modApiFunc("Taxes", "getTaxNamesList");
        foreach ($TaxNamesList as $taxNameInfo)
        {
            $replace['{t_'.$taxNameInfo['Id'].'}'] = prepareHTMLDisplay($taxNameInfo['Name']);
        }
        $costList = modApiFunc("Taxes", "getTaxCostsList");
        foreach ($costList as $cost)
        {
            $replace['{p_'.$cost['id'].'}'] = $this->MessageResources->getMessage($cost['name']);
        }
        preg_match_all("/([0-9]+\.?[0-9]+)/", $TaxInfo['Formula'], $numbers);
        for ($j =0; $j<sizeof($numbers[0]); $j++)
        {
            $replace[$numbers[0][$j]] = modApiFunc("Localization", "num_format", $numbers[0][$j]);
        }

        if (!$TaxInfo['Id'])
        {
            $TaxInfo['CountryId'] = modApiFunc("Taxes", "getCountryId");
            $TaxInfo['StateId'] = "0";
            $this->ViewState["TaxRateAction"] =  "AddTaxRateAction";
        }
        else
        {
            $this->ViewState["TaxRateAction"] =  "UpdateTaxRateAction";
        }

        $ProductTaxClassId = modApiFunc("Taxes", "getTaxClassId");
        $ProductTaxClassName = modApiFunc("Taxes", "getTaxClassInfo", $ProductTaxClassId);
        $ProductTaxClassName = $ProductTaxClassName["name"];
        $this->POST  =
            array(
                "Id"                    => $TaxInfo['Id']
               ,"CountryId"             => $TaxInfo['CountryId']
               ,"StateId"               => $TaxInfo['StateId']
               ,"ProductTaxClassId"     => $ProductTaxClassId
               ,"ProductTaxClassName"   => $ProductTaxClassName
               ,"TaxNameId"             => $TaxInfo['TaxNameId']
               ,"Rate"                  => ($TaxInfo['rates_set'] == 0) ? modApiFunc("Localization", "num_format", $TaxInfo['Rate']) : 0
               ,"FormulaView"           => ($TaxInfo['Formula'])? strtr($TaxInfo['Formula'], $replace):"&nbsp;"
               ,"Formula"               => $TaxInfo['Formula']
               ,"Applicable"            => $TaxInfo['Applicable']
               ,"TaxRateByZipSet"       => $TaxInfo['rates_set']
               );
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('EditTaxRate');
        $request->setAction($this->ViewState["TaxRateAction"]);
        return $request->getURL();
    }

    function outputSubtitle()
    {
        return $this->MessageResources->getMessage('EDIT_TAX_RATE_PAGE_SUBTITLE');
    }

    function outputButton()
    {
        return $this->MessageResources->getMessage('BTN_UPDATE');
    }

    function outputPrevOperand()
    {
        if (!$this->POST['Formula'])
        {
            return "_";
        }
        $pos=_ml_strrpos($this->POST['Formula'], ' ');
        if ($pos === false)
        {
            return $this->POST['Formula'];
        }
        else
        {
            return _ml_substr($this->POST['Formula'], $pos+1);
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