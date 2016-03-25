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
 * Action handler on Ajax SetCurrentStep.
 *
 * @package Checkout
 * @access  public
 */
class JSSetCurrStep extends AjaxAction
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
    function JSSetCurrStep()
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
        global $application;
        global $_RESULT;

        $lastPlacedOrderID = modApiFunc('Checkout', 'getLastPlacedOrderID');
        if(empty($lastPlacedOrderID))
        {
            $CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST = modApiFunc('Checkout', 'getPerRequestVariable', 'CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST');
            if ($CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST === true)
                modApiFunc('Checkout', 'estimateLostEncryptedData');
        }

        $request = $application->getInstance('Request');

        $step_id = $request -> getValueByKey('step_id');
        $previous_step_id = $request -> getValueByKey('previous_step_id');

        $curr_step = modApiFunc('Checkout', 'getCurrentStepID');
        if ($curr_step == $step_id)
        {
            modApiFunc('Checkout', 'ProcessNewStepID', $previous_step_id);
            modApiFunc('Checkout', 'clearNotMetPrerequisitesValidationResultsDataForAllPosteriorSteps', $previous_step_id);
        }

        modApiFunc('Checkout', 'ProcessNewStepID', $step_id);
        modApiFunc('Checkout', 'clearNotMetPrerequisitesValidationResultsDataForAllPosteriorSteps', $step_id);

        $step_id_to_redirect_to = modApiFunc('Checkout', 'getCurrentStepID');

        modApiFunc('Checkout', 'saveState');

        if ($step_id_to_redirect_to == '3')
        {
            $pm_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
            if($pm_id !== NULL)
                if (!modApiFunc('Checkout', 'AdditionalPersonInfoSetCurrStepHook', $pm_id, false))
                {
                    modApiFunc('Checkout', 'ProcessNewStepID', 2);
                    $step_id_to_redirect_to = 2;
                }
        }

        // added due to http://projects.simbirsoft.com/issues/86
        global $zone;
        if ($zone == 'CustomerZone')
        {
            //Check if shippingInfo has already been outputted
            $ShippingInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo");
            if($ShippingInfo["isMet"]!=false)
            {
                //check if shipping method hasn't been selected yet
                $shippingModuleAndMethod = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingModuleAndMethod");
                if($shippingModuleAndMethod["isMet"] == false)
                {
                    $the_only_available_shipping_method_info = modApiFunc("Checkout", "getTheOnlyAvailableAndComputableShippingMethodId");
                    if($the_only_available_shipping_method_info !== false)
                    {
                        //Auto-select method
                        modApiFunc("Checkout", "setChosenShippingMethod",
                                   $the_only_available_shipping_method_info["module_id"]
                                  ,$the_only_available_shipping_method_info["method_id"]);
                    }
                }
            }
        }

        $_RESULT['error'] = '';
        if (!modApiFunc('Cart', 'getCartProductsQuantity')
            || modApiFunc('Checkout', 'getLastPlacedOrderID')
            || (modApiFunc('Configuration', 'getValue',
                           SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT) > ZERO_PRICE
                && modApiFunc('Checkout', 'getOrderPrice', 'Subtotal',
                              modApiFunc('Localization',
                                         'getMainStoreCurrency')) <
                   modApiFunc('Configuration', 'getValue',
                              SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT)))
        {
            $_RESULT['error'] = 'Y';
        }
        $_RESULT['step'] = $step_id_to_redirect_to;
        $_RESULT['output'] = getOneStepCheckout($step_id_to_redirect_to);
        if ($step_id_to_redirect_to != $previous_step_id)
            $_RESULT['prev_output'] = getOneStepCheckout($previous_step_id);
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