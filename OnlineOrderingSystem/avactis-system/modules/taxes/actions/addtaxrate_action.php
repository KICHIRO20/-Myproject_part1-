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
class AddTaxRateAction extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AddTaxRateAction constructor.
     */
    function AddTaxRateAction()
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

        $c_id = $request->getValueByKey('CountryId');
        $s_id = $request->getValueByKey('StateId');
        $ptc_id = $request->getValueByKey('ProductTaxClassId');
        $tn_id = $request->getValueByKey('TaxNameId');
        $rate_method = $request->getValueByKey('RateMethod');
        $rate_set_id = ($rate_method == 2) ? $request->getValueByKey('TaxRateByZipSetName') : 0;
        $rate = ($rate_method == 1)
            ?
            modApiFunc("Localization", "FormatStrToFloat", $request->getValueByKey('Rate'), "weight")
            :
            0;

        $taxes = modApiFunc('Taxes', 'getTaxNames');
        if(isset($taxes[$tn_id]) && $taxes[$tn_id]["NeedsAddress"] == DB_FALSE)
        {
            $c_id = TAXES_COUNTRY_NOT_NEEDED_ID;
            $s_id = TAXES_STATE_NOT_NEEDED_ID;
        }

        $formula = $request->getValueByKey('Formula');
        $not_applicable = $request->getValueByKey('NotApplicable');
        $applicable = empty($not_applicable);

        //: Add checks for Tax Rates "Not Applicable"
        $tax_formula_priority = modApiFunc("Taxes", "getTaxFormulaPriority", $s_id, $ptc_id);

        $formula_states_list = (($s_id == STATE_ID_ALL) ? modApiFunc("Location", "getStates", $c_id) : array($s_id => ""));
        $formula_product_tax_classes_list = (($ptc_id == TAX_CLASS_ID_ANY) ? modApiFunc("Taxes", "getProductTaxClasses", false) : array(0 => array("id" => $ptc_id, "value" => "")));
        foreach($formula_states_list as $state_id => $state_name)
        {
            foreach($formula_product_tax_classes_list as $product_tax_class_info)
            {
                $product_tax_class_id = $product_tax_class_info['id'];
                $product_tax_class_name = $product_tax_class_info['value'];
                $contradictory_formula_id = modApiFunc("Taxes", "isTaxFormulaContradictory"
                              ,$tn_id
                              ,$formula

                              ,$product_tax_class_id
                              ,$c_id
                              ,$state_id
                              ,$tax_formula_priority);
                if($contradictory_formula_id === false)
                {
                    $cycle = modApiFunc("Taxes", "doesAddingTaxFormulaCreateCycle"
                                  ,$tn_id
                                  ,$formula

                                  ,$product_tax_class_id
                                  ,$c_id
                                  ,$state_id
                                  ,$tax_formula_priority
                                  ,$applicable);
                    if($cycle === false)
                    {
//                        modApiFunc('Taxes', 'addTaxRate', $c_id, $state_id, $product_tax_class_id, $tn_id, modApiFunc("Localization", "FormatStrToFloat", $request->getValueByKey('Rate'), "weight"), $formula);
                    }
                    else
                    {
                        //Adding a formula would generate a formula cycle.
                        //Give an example of the cycle.
                        $SessionPost["ViewState"]["ErrorsArray"]["CyclicFormula"] = array("cycle" => $cycle);
                        $SessionPost["ViewState"]["hasCloseScript"] = "false";
                    }
                }
                else
                {
                    //The formula contradicts the already existing one.
                    //Show which one.
                    $SessionPost["ViewState"]["ErrorsArray"]["ContradictoryFormula"] = array("formula_id" => $contradictory_formula_id);
                    $SessionPost["ViewState"]["hasCloseScript"] = "false";
                }
            }
        }

        if(!modApiFunc("Taxes", "areTaxNamesValid", $request->getValueByKey('Formula')))
        {
            // add the outputting of this error to Views:
            //  AddTaxRate and EditTaxRate
            $SessionPost["ViewState"]["ErrorsArray"]["InvalidTaxNames"] = true;
        }

        if(!modApiFunc("Taxes", "areProductNamesValid", $request->getValueByKey('Formula')))
        {
            //  add the outputting of this error to Views:
            //   AddTaxRate and EditTaxRate
            $SessionPost["ViewState"]["ErrorsArray"]["InvalidProductNames"] = true;
        }

        if(empty($SessionPost["ViewState"]["ErrorsArray"]))
        {
            modApiFunc('Taxes', 'addTaxRate', $c_id, $s_id, $ptc_id, $tn_id, $rate, $formula, (($applicable)? "true":"false"), $rate_set_id);
        }

        modApiFunc("Taxes", "saveState");
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