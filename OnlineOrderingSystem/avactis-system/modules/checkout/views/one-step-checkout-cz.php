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
 * Checkout Common page.
 *
 * @package Checkout
 * @access  public
 * @author  Sergey Kulitsky
 */
class OneStepCheckout
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'one-step-checkout-config.ini',
            'files'       => array(
                'Container' => TEMPLATE_FILE_SIMPLE,
                'Step1'     => TEMPLATE_FILE_SIMPLE,
                'Step2'     => TEMPLATE_FILE_SIMPLE,
                'Step3'     => TEMPLATE_FILE_SIMPLE,
                'Error'     => TEMPLATE_FILE_SIMPLE
            ),
            'options' => array(
            )
        );
        return $format;
    }

    /**
     * The view constructor.
     */
    function OneStepCheckout()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> pCheckout = &$application->getInstance('Checkout');
        $this -> MessageResources = &$application -> getInstance('MessageResources', 'messages');

        #check if fatal errors exist in the block tag

        $this->NoView = false;
        if ($application -> issetBlockTagFatalErrors('OneStepCheckout'))
            $this->NoView = true;
    }

    /**
     * Returns link to the provided step
     */
    function getLinkToCheckoutStep($step_id = '')
    {
        $_request = new Request();
        $_request -> setView('CheckoutView');
        if ($step_id)
        {
            $_request -> setAction('SetCurStep');
            $_request -> setKey('step_id', $step_id);
            $_request = modApiFunc('Checkout', 'appendCheckoutCZGETParameters', $_request);
        }

        return $_request->getURL();
    }

    function isAllInactiveMethodActive($pm_list)
    {
        $ai_name = modApiFunc('Checkout', 'getAllInactiveModuleClassAPIName',
                              'payment');
        foreach ($pm_list as $i => $pm)
        {
            if ($pm -> name == $ai_name)
                return $i;
        }

        return false;
    }

    function getPaymentModulesList($groups = null)
    {
        global $application;

        $person_info_types = modApiFunc("Checkout", "getPersonInfoTypeList");
        if($person_info_types[6]['active'] == DB_FALSE)
        {
            return "";
        }
        //If method hasn't been selected, tick off the first one in the list.
        modApiFunc("Checkout", "setPerRequestVariable", "checkedPaymentMethod", "");

        $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "payment");
        $pm_list = modApiFunc("Checkout", "getInstalledAndActiveModulesListData", "payment", $groups);

        $total_to_pay = modApiFunc("Checkout", "getOrderPrice", "TotalToPay", modApiFunc("Localization", "getMainStoreCurrency"));

        if (floatval($total_to_pay) == 0.0)
        {
            $gc_info = modApiFunc("Checkout", "getGiftCertificatePaymentModuleInfo");
            $items = array($gc_info['CZInputViewClassName']);
        }
        else
        {
            if (is_array($pm_list) && (count($pm_list) > 1) && $this->isAllInactiveMethodActive($pm_list)) # remove all_inactive_module from the list
            {
                unset($pm_list[$this->isAllInactiveMethodActive($pm_list)]);
            }

            $items = array();
            $new_selected_module_sort_order = 0;
            foreach ($pm_list as $pm_item)
            {
                //      create/use some mm function to convert class names.
                $name = strtolower($pm_item->name);

                $pmInfo = modApiFunc($name, "getInfo");

                //      : check if function exists
                $module_uid = $pmInfo['GlobalUniquePaymentModuleID'];

                if (array_key_exists($module_uid, $SelectedModules) == true)
                {
                    $payment_gw_currency_id = modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", ORDER_NOT_CREATED_YET, $module_uid);
                    if(modApiFunc("Localization", "doesPMSMAcceptCurrency", ORDER_NOT_CREATED_YET, $module_uid, $payment_gw_currency_id) == true)
                    {
                        $sort_id = empty($SelectedModules[$module_uid]["sort_order"]) ? $new_selected_module_sort_order-- : $SelectedModules[$module_uid]["sort_order"];
                        $items[$sort_id] = $pmInfo['CZInputViewClassName'];
                    }
                }
            }
        }

        //Sort items by sort id and implode them.
        ksort($items, SORT_NUMERIC);
        $items_content = array();
        foreach($items as $sord_id => $view_class_name)
        {
            $view_class_name = 'get'.$view_class_name;
            $items_content[$sord_id] = $view_class_name();
        }
        if(empty($items_content))
        {
        	//                     ,                                          .                  .
            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('CheckoutView');
            $this->templateFiller->setTemplate($this->template);
            $value = $this->templateFiller->fill("CurrencyNotAppropriateForActivePaymentModules");
        }
        else
        {
            $value = implode("", $items_content);
        }
        return $value;
    }

    function getPaymentModuleOutput($groups = null)
    {
        global $application;

        $pmId =  modApiFunc('Checkout', 'getChosenPaymentModuleIdCZ');
        if($pmId === NULL)
        {
            return '';
        }
        else
        {
            $pmInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $pmId);
            $f = 'get' . $pmInfo['CZOutputViewClassName'];
            $value = $f();
            return $value;
        }
    }

    function getErrors($step_id = 1)
    {
        global $application;

        if ($step_id != $this -> pCheckout -> getCurrentStepID())
            return '';

        $curr_step_prerequisites = modApiFunc('Checkout',
                                              'getPrerequisitesListForStep',
                                              $step_id);
        if (modApiFunc('Checkout', 'isLastStepWithPrerequisites', $step_id))
        {
            $prerequisites_for_current_page__list = $curr_step_prerequisites;
        }
        else
        {
            $next_step_prerequisites = modApiFunc('Checkout',
                                                  'getPrerequisitesListForStep',
                                                   $step_id + 1);
            $prerequisites_for_current_page__list = array();

            foreach($next_step_prerequisites as $next_step_prerequisite)
            {
                if (!in_array($next_step_prerequisite, $curr_step_prerequisites))
                {
                    $prerequisites_for_current_page__list[] = $next_step_prerequisite;
                }
            }
        }

        // Overview the list of prerequisites for the current page and errors,
        // occurred while validating data, which came from store blocks.
        // The blocks match these prerequisites.
        $templateFiller = &$application -> getInstance('TemplateFiller');
        $template = $application -> getBlockTemplate('CheckoutView');
        $templateFiller -> setTemplate($template);

        $cnt = 0;
        $value = "";
        foreach($prerequisites_for_current_page__list as $prerequisite_for_current_page)
        {
            $person_info_types = modApiFunc('Checkout', 'getPersonInfoTypeList');
            foreach($person_info_types as $id => $info)
            {
                if ($info['tag'] == $prerequisite_for_current_page
                    && $info['active'] == DB_FALSE)
                {
                    continue 2;
                }
            }

            $PrerequisiteValidationResults = modApiFunc('Checkout', 'getPrerequisiteValidationResults', $prerequisite_for_current_page);
            if (!is_array($PrerequisiteValidationResults)) {
                continue;
            }

            if($PrerequisiteValidationResults['error_code'] != '')
            {
                //An error in prerequisite, e.g. a matching it POST-data block didn't come.
                $cnt++;
                $text = $this -> MessageResources -> getMessage($PrerequisiteValidationResults['error_code'], empty($PrerequisiteValidationResults['error_message_parameters']) ? array() : $PrerequisiteValidationResults['error_message_parameters']);
                $_tags = array(
                    'Local_FormFieldErrorIndex' => $cnt,
                    'Local_FormFieldErrorText' => $text
                );
                $this -> _Template_Contents = $_tags;
                $application -> registerAttributes($this -> _Template_Contents);
                $template_name = 'LocalFormFieldError';
                $value .= $templateFiller -> fill($template_name);
            }

            foreach($PrerequisiteValidationResults['validatedData'] as $validatedData)
            {
                if ($validatedData['error_code_full'] != '')
                {
                    //Output error message:
                    $cnt++;
                    $text = $this -> MessageResources -> getMessage($validatedData['error_code_full'], empty($validatedData['error_message_parameters']) ? array() : $validatedData['error_message_parameters']);

                    $_tags = array(
                        'Local_FormFieldErrorIndex' => $cnt,
                        'Local_FormFieldErrorText' => $text
                    );
                    $this -> _Template_Contents = $_tags;
                    $application -> registerAttributes($this -> _Template_Contents);
                    $template_name = 'LocalFormFieldError';
                    $value .= $templateFiller -> fill($template_name);
                }
            }
        }
        return $value;
    }

    function getJSAttrRules()
    {
        $value = '';
        $i=0;
        $cc_types = modApiFunc('Configuration', 'getCreditCardSettings', true);
        foreach($cc_types as $type => $val)
        {
            $value .= "\nnames['$type'] = $i;\n";
            $value .= "cc_attrs[$i] = new Array();\n";
            $at = modApiFunc('Configuration', 'getAttributesForCardType', $val['id']);
            $j=0;
            foreach ($at as $id => $attr)
            {
                $value .= "cc_attrs[$i][$id] = new Array();\n";
                $value .= "cc_attrs[$i][$id][0] = {$attr['visible']};\n";
                $value .= "cc_attrs[$i][$id][1] = {$attr['required']};\n";
            }
            $i++;
        }

        return $value;
    }

    /**
     * Outputs the OneStepCheckout view.
     */
    function output($current_step = '', $errors_only = false)
    {
        global $application;

        if ($current_step < 1 || $current_step > 3)
            $current_step = '';

        $step_id = $this -> pCheckout -> getCurrentStepID();

        if($step_id == 3)
        {
            $payment_module_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
            $prerequisite_name = Checkout::getAdditionalPrerequisiteName("creditCardInfo", $payment_module_id);
            $PrerequisitesValidationResults = modApiFunc("Checkout", "getPrerequisitesValidationResults");
            if(isset($PrerequisitesValidationResults[$prerequisite_name]) &&
                 $PrerequisitesValidationResults[$prerequisite_name]["isMet"] != true)
	    {
		if ($prerequisite_name == "creditCardInfoAC593800_68BA_A4D3_6A14_49BA5022FED7" && //module id for paypalpro
			isset($PrerequisitesValidationResults['billingInfo']['variant_tag']) &&
			$PrerequisitesValidationResults['billingInfo']['variant_tag'] == "PayPalProExpressCheckout")
		{

		}
		    else
		{
                    $request = new Request();
                    $request->setView('CheckoutView');
                    $request->setAction("SetCurrStep");
                    $request->setKey   ( 'step_id', 2);
                    $request = modApiFunc("Checkout", "appendCheckoutCZGETParameters", $request);
                    modApiFunc("Checkout", "saveState");
                    $application->redirect($request);
                    return '';
		}
            }
        }

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('OneStepCheckout');
        $this -> mTmplFiller -> setTemplate($template_block);

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
            return getCheckout();
        }

        $_tags = array(
            'Local_StepID' => $step_id,
            'Local_FormAction' => $this -> getLinkToCheckoutStep(),
            'Local_BlowFishKey' => modApiFunc('Checkout',
                                              'getPerRequestVariable',
                                              'CHECKOUT_CZ_BLOWFISH_KEY'),
            'Local_PaymentMethods' => $this -> getPaymentModulesList(),
            'Local_PaymentMethodOutput' => $this -> getPaymentModuleOutput(),
            'Local_CreditCardInfoJSAttrRules' => $this -> getJSAttrRules(),
            'Local_Errors_1' => $this -> getErrors(1),
            'Local_Errors_2' => $this -> getErrors(2),
            'Local_Errors_3' => $this -> getErrors(3),
            'Local_Errors' => $this -> getErrors($step_id)
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents,'OneStepCheckout');

        if ($current_step)
        {
            if ($errors_only && $this -> getErrors($step_id))
                return $this -> mTmplFiller -> fill('error');

            return $this -> mTmplFiller -> fill('step' . $current_step);
        }

        return $this -> mTmplFiller -> fill('container');
    }

    function getTag($tag)
    {
    	global $application;
        $value = null;
        switch ($tag)
        {
            default:

            $value = getKeyIgnoreCase($tag, $this -> _Template_Contents);

                break;
        }

        return $value;
    }

    /**#@-*/


//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;

    var $pCheckout;

    var $_Template_Contents;

    var $MessageResources;

    /**#@-*/
}
?>