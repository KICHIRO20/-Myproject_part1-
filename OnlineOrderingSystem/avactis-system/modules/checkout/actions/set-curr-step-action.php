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
 * Action handler on SetCurrentStep.
 *
 * @package Checkout
 * @access  public
 */
class SetCurrStep extends AjaxAction
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
    function SetCurrStep()
    {
    }

    /**
     * Sets current checkout step from Request.
     *
     * Action: setCurrStep
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $_POST;
        /** Check prerequisites for the next step.
         *  If they are performed, set the required step number.
         *  Perform all extra required actions.
         *  After that can be mapped a new checkout step.
         */
        global $application;

        $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");
        //   ConfirmOrder
        if(empty($lastPlacedOrderID))
        {
	        $CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST = modApiFunc("Checkout", "getPerRequestVariable", "CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST");
	        if($CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST === true)
	        {
	            //         ,
	            //                   ,                                        .
	            //            Credit Card Info.
	            //:         ,                          redirect
	            //  estimateLostEncryptedData       ,     step_id    GET        ,
	            //                                       step_id,          ,
	            //                 estimateLostEncryptedData.
	            //  estimateLostEncryptedData               step_id    GET        ,
	            //                           (2007 dec)                            .
	            modApiFunc("Checkout", "estimateLostEncryptedData");
	        }
        }

        $request = $application->getInstance('Request');

        $step_id = $request->getValueByKey( 'step_id' );
        $previous_step_id = $request->getValueByKey( 'previous_step_id' );

//        $SessionPost_PreviousStep_Name = 'SessionPost'.$previous_step_id;
//        $$SessionPost_PreviousStep_Name = array();
//        $SessionPost_PreviousStep = &$$SessionPost_PreviousStep_Name;

        // is next check correct? Consider both "success" e.g. 1=>2 and "error" e.g. 3=>2
//        if(modApiFunc('Session', 'is_Set', '$SessionPost_PreviousStep_Name'))
//        {
//            _fatal(__CLASS__ .'::'. __FUNCTION__ . " session[" . $SessionPost_PreviousStep_Name . "] is already set.");
//        }

//        $SessionPost_PreviousStep = $_POST;

        // move initialization into init section
//        $SessionPost_PreviousStep["ViewState"] = array();
        //END

//        $SessionPost_PreviousStep["ViewState"]["ErrorsArray"] = array();

        modApiFunc("Checkout", "ProcessNewStepID", $step_id);

        modApiFunc("Checkout", "clearNotMetPrerequisitesValidationResultsDataForAllPosteriorSteps", $step_id);

        $step_id_to_redirect_to = modApiFunc("Checkout", "getCurrentStepID");
        $request = new Request();
        $request->setView('CheckoutView');
        $request->setAction("SetCurrStep");
        $request->setKey   ( 'step_id', $step_id_to_redirect_to);
        $request = modApiFunc("Checkout", "appendCheckoutCZGETParameters", $request);

        modApiFunc("Checkout", "saveState");

        if(empty($_POST))
        {
            //                                 action    view:            view
        }
        else
        {
            //                            action "                    ",
            //                                    view
            $application->redirect($request);
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