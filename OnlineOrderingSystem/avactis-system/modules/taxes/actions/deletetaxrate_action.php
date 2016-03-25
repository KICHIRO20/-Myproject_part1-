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
class DeleteTaxRateAction extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DeleteTaxRateAction constructor.
     */
    function DeleteTaxRateAction()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $tax_rate_id = $request->getValueByKey('TaxId');

        $cycle = modApiFunc("Taxes", "doesDeletingTaxFormulaCreateCycle", $tax_rate_id);

        if($cycle === false)
        {
            modApiFunc('Taxes', 'deleteTaxRate', $tax_rate_id);
        }
        else
        {
            $SessionPost["ViewState"] = array();
            //Removing a formula would generate a formula cycle.
            //Give an example of the cycle.
            $SessionPost["ViewState"]["ErrorsArray"]["CyclicFormula"] = array("cycle" => $cycle);
            $SessionPost["ViewState"]["hasCloseScript"] = "false";
            modApiFunc("Taxes", "saveState");
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        }

        $request = new Request();
        $request->setView('TaxSettings');
        $application->redirect($request);
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