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
 * @author  Vadim Lyalikov
 */
class CheckoutView
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
            'layout-file'        => 'checkout-default-config.ini'
           ,'files' => array(
                'CurrencyNotAppropriateForActivePaymentModules' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function CheckoutView()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources',"messages");

        $this->pCheckout = &$application->getInstance('Checkout');

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        $this->cachedConfirmationData = null;

        // do we need this check?
        #check if fatal errors exist in the block tag

        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutView"))
        {
            $this->NoView = true;
        }
    }

    function getLinkToCheckoutStep($step_id)
    {
        $_request = new Request();
        $_request->setView  ( 'CheckoutView' );
        ##$_request->setAction( Catalog_SetCurrCat::ACT_NM() );
        $_request->setAction( "SetCurStep" );
        $_request->setKey   ( "step_id", $step_id );
        $_request = modApiFunc("Checkout", "appendCheckoutCZGETParameters", $_request);

        return $_request->getURL();
    }


    function outputRedirectPage()
    {
        global $application;
        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('CheckoutView');
        $this->templateFiller->setTemplate($this->template);

        $template_name = "ConfirmationRedirectToPaymentGateway";
        $retval = $this->templateFiller->fill($template_name);

        $retval = str_replace("___CheckoutConfirmationFormAction",
                              $this -> getTag("Local_ConfirmationFormAction"),
                              $retval);

        $retval = str_replace("___CheckoutConfirmationFormMethod",
                              $this -> getTag("Local_ConfirmationFormMethod"),
                              $retval);

        $retval = str_replace("___Errors",
                              $this -> getTag("CheckoutErrors"),
                              $retval);

        $retval = str_replace("___ProcessPaymentHiddenFields",
                              $this -> getTag("Local_ProcessPaymentHiddenFields"),
                              $retval);

        $retval = str_replace("___CheckoutConfirmationBodyOnLoad",
                              $this -> getTag("Local_CheckoutConfirmationBodyOnLoad"),
                              $retval);
        return $retval;
    }

    /**
     * Returns the CheckoutView view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;

        $step_id = $this->pCheckout->getCurrentStepID();

        if (NULL == $step_id)
        {
            $err_params = array(
                                "CODE"    => "CHECKOUT_ERR_VIEW_001"
                               );
            _fatal($err_params);
        }
        else
        {
            if($step_id == 3)
            {
                $payment_module_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
                $prerequisite_name = Checkout::getAdditionalPrerequisiteName("creditCardInfo", $payment_module_id);
                $PrerequisitesValidationResults = modApiFunc("Checkout", "getPrerequisitesValidationResults");
                if(isset($PrerequisitesValidationResults[$prerequisite_name])
                    && $PrerequisitesValidationResults[$prerequisite_name]["isMet"] != true)
                {
					if ($prerequisite_name == "creditCardInfoAC593800_68BA_A4D3_6A14_49BA5022FED7" && //line to be added
								    isset($PrerequisitesValidationResults['billingInfo']['variant_tag']) && //line to be added
									$PrerequisitesValidationResults['billingInfo']['variant_tag'] == "PayPalProExpressCheckout")
					{ //line to be added

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

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('CheckoutView');
            $this->templateFiller->setTemplate($this->template);

            $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");
            //If the cart is empty from the very beginning (not after cle ring on CheckoutConfirmation)
            // - output the message "Empty cart".
            if ((modApiFunc("Cart", "getCartProductsQuantity") == 0) && empty($lastPlacedOrderID))
            {
                $template_name = "ErrorEmptyCart";
                $application->registerAttributes(array());
            }
            //                    ,                   (Subtotal                              ),
            //                     ,
            elseif (modApiFunc("Cart", "getCartProductsQuantity") > 0
                    && empty($lastPlacedOrderID)
                    && modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT) > ZERO_PRICE
                    && modApiFunc("Checkout", "getOrderPrice", "Subtotal", modApiFunc("Localization", "getMainStoreCurrency")) < modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT))
            {
                //                    ,                   (Subtotal                              ),
                //                     ,
                $application->registerAttributes(array("Local_CartMinSubtotal" => ''));
                $template_name = "ErrorCartMinSubtotal";
            }
            else
            {
                $orderInfo = NULL;
                if ($lastPlacedOrderID !== NULL)
                {
                    $orderInfo = modApiFunc("Checkout", "getOrderInfo", $lastPlacedOrderID, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $lastPlacedOrderID));
                }
                if ($lastPlacedOrderID !== NULL &&
                    $orderInfo['PaymentStatusId'] == 3)
                {
                    $template_name = "PaymentTransactionFailed";
                    $application->registerAttributes(array("Local_OrderID" => ''
                                                          ,"Local_Step4PaymentModuleMessage" => ''
                                                          ,"Local_OrderTotal" => ''
                                                          ,"Local_OrderTotalRaw" => ''));
                }
                elseif(modApiFunc("Checkout", "getCustomPaymentGatewayPageContents") != NULL)
                {
                    // If the curent step is checkout-confirmation and an answer came from
                    // the server as pre-formatted HTML, then view it. Workaround: check if the
                    // variable containing the HTML code in the Checkout module is not empty.
                    # fill the template on the current step of the checkout process
                    $template_name = "CustomPaymentGatewayPage";
                    $application->registerAttributes(array(
                                                         "CustomPaymentGatewayPageContents" => ''
                                                          )
                                                    );
                }
                else
                {
                    # fill the template on the current step of the checkout process
                    $template_name = "Step" . $this->pCheckout->getCurrentStepID();
                    $default = array("Local_OrderID" => '',
                                                         "Local_Step4PaymentModuleMessage" => '',
                                                         "Local_OrderTotal" => '',
                                                         "Local_OrderTotalRaw" => '',

                                                         "Local_OrderDate" => '',
                                                         "Local_OrderStatus" => '',
                                                         "Local_OrderPaymentStatus" => '',
                                                         "Local_OrderPaymentMethod" => '',
                                                         "Local_OrderPaymentProcessorOrderId" => '',
                                                         "Local_OrderShippingMethod" => '',


                                                         "Local_ProcessPaymentHiddenFields" => '',
                                                         "Local_PaymentMethodOutput" => '',
                                                         "CheckoutPaymentMethodsOutput" => '',
                                                         "CheckoutPaymentMethodsSelect" => '',
                            									 "Local_CreditCardInfoJSAttrRules" => '',
                                                         "Local_PaymentMethods" => '',
                                                         "CheckoutConfirmationFormAction" => '',
                                                         "Local_ConfirmationFormAction" => '',
                                                         "Local_ConfirmationFormMethod" => '',
                                                         "Local_CheckoutConfirmationBodyOnLoad" => '',

                                                         "Local_FormName" => '',
                                                         "Local_FormAction" => '',
                                                         "Local_FormMethod" => '',

//                                                         "CheckoutFormNextStepID" => '',
//                                                         "CheckoutFormCurrentStepID" => '',

                                                         "Local_FormHiddenFields" => '',
                                                         "Local_FormActionFieldName" => '',
                                                         "Local_FormActionFieldValue" => '',
                                                         "Local_FormStepIDFieldName" => '',
                                                         "Local_FormStepIDFieldValue" => '',
                                                         "Local_FormPreviousStepIDFieldName" => '',
                                                         "Local_FormPreviousStepIDFieldValue" => '',
                                                         "Local_FormCHECKOUT_CZ_BLOWFISH_KEYName" => '',
                                                         "Local_FormCHECKOUT_CZ_BLOWFISH_KEYValue" => '',

                                                         "CheckoutErrors" => ''
                                                    );

		    $default=apply_filters("avactis_checkout_view_addAttributes",$default);

                    $application->registerAttributes($default,'');
		}
		}


            $retval = $this->templateFiller->fill($template_name);

            //Output blocks of current step page.
            /**
             * Don't check for prerequisites: it should be done while processing Action.
             */
        }

        if(modApiFunc("Checkout", "isLastStepWithPrerequisites", $step_id))
        {
            //remove validation info of the inputted data after checkout
            // clear QuickCheckout Customers' personal information
            if (modApiFunc('Settings','getParamValue','CUSTOMER_ACCOUNT_SETTINGS','CLEAR_QCC_PERSONAL_INFO') === 'YES'
                && modApiFunc('Customer_Account', 'getCurrentSignedCustomer') === null)
            {
                $sess_obj = $application->getInstance("Session");
                $sess_obj->un_Set('PrerequisitesValidationResults');
            }
        };

        return $retval;
    }
    /**#@-*/

    function getErrors()
    {
        global $application;
        //Output validation errors for the data, which was passed from this step.

        //Find out which store blocks for which prerequisites should be outputted
        // on this step of the checkout process, see if errors occurred
        // while validating (if they were validated and any error occurred
        // at all). Describe the errors.

        // As the one effective way to define a list of all the store blocks
        // is the analysis of .html templates, and it is hard to be realized
        // then at first define the list of the store blocks as a difference
        // between the variety of prerequisites for the next step and for
        // the current one.
        //For example if they equal correspondingly:
        // Option-prerequisite2 = shipping-info,shipping-method
        // Option-prerequisite3 = shipping-info,shipping-method,payment-address,payment-method
        // then here there are outputted blocks
        // ( i.e. the errors for prerequisites, which match the blocks):
        // payment-address and payment-method


        //:for the last step. For example if the whole checkout
        // is on one page.
        $step_id = modApiFunc("Checkout", "getCurrentStepID");

        $curr_step_prerequisites = modApiFunc("Checkout", "getPrerequisitesListForStep", $step_id);
        if(modApiFunc("Checkout", "isLastStepWithPrerequisites", $step_id))
        {
            $prerequisites_for_current_page__list = $curr_step_prerequisites;
        }
        else
        {
            $next_step_prerequisites = modApiFunc("Checkout", "getPrerequisitesListForStep", $step_id + 1);
            $prerequisites_for_current_page__list = array();

            foreach($next_step_prerequisites as $next_step_prerequisite)
            {
                if(!in_array($next_step_prerequisite, $curr_step_prerequisites))
                {
                    $prerequisites_for_current_page__list[] = $next_step_prerequisite;
                }
            }
        }

        //Overview the list of prerequisites for the current page and errors,
        // occurred while validating data, which came from store blocks.
        //  The blocks match these prerequisites.
        $templateFiller = &$application->getInstance('TemplateFiller');
        $template = $application->getBlockTemplate('CheckoutView');
        $templateFiller->setTemplate($template);

        $cnt = 0;
        $value = "";
        foreach($prerequisites_for_current_page__list as $prerequisite_for_current_page)
        {
            //                            CheckoutFormEditor'  -                            .              .
            $person_info_types = modApiFunc("Checkout", "getPersonInfoTypeList");
            foreach($person_info_types as $id => $info)
            {
                if($info['tag'] == $prerequisite_for_current_page &&
                   $info['active'] == DB_FALSE)
                {
                    continue 2;
                }
            }

            $PrerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", $prerequisite_for_current_page);
            if (! is_array($PrerequisiteValidationResults)) {
                continue;
            }

            if($PrerequisiteValidationResults['error_code'] != '')
            {
                //An error in prerequisite, e.g. a matching it POST-data block didn't come.
                $cnt++;
                $text = $this->MessageResources->getMessage($PrerequisiteValidationResults['error_code'], empty($PrerequisiteValidationResults['error_message_parameters']) ? array() : $PrerequisiteValidationResults['error_message_parameters']);
                $this->_Local_FormFieldError = array("Local_FormFieldErrorIndex" => $cnt,
                                                     "Local_FormFieldErrorText" => $text);
                $application->registerAttributes($this->_Local_FormFieldError);
                $template_name = "LocalFormFieldError";
                $value .= $templateFiller->fill($template_name);
            }

            foreach($PrerequisiteValidationResults['validatedData'] as $validatedData)
            {
                if($validatedData['error_code_full'] != '')
                {
                    //Output error message:
                    $cnt++;
                    $text = $this->MessageResources->getMessage($validatedData['error_code_full'], empty($validatedData['error_message_parameters']) ? array() : $validatedData['error_message_parameters']);

                    $this->_Local_FormFieldError = array("Local_FormFieldErrorIndex" => $cnt,
                                                         "Local_FormFieldErrorText" => $text);
                    $application->registerAttributes($this->_Local_FormFieldError);
                    $template_name = "LocalFormFieldError";
                    $value .= $templateFiller->fill($template_name);
                }
            }
        }
        return $value;
    }

    function isAllInactiveMethodActive ($pm_list)
    {
            $ai_name = modApiFunc("Checkout", "getAllInactiveModuleClassAPIName", "payment");
    	    foreach ($pm_list as $i=>$pm)
            {
            	if ($pm->name == $ai_name)
        	    {
        	    	return $i;
        	    }
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

        $pmId =  modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
        if($pmId === NULL)
        {
            return "";
        }
        else
        {
            $pmInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $pmId);
            //$value = call_user_func($pmInfo['CZOutputViewClassName']);
            $f = 'get'.$pmInfo['CZOutputViewClassName'];
            $value = $f();
            return $value;
        }
    }

    /**
     * @ describe the function ->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;

        $b = class_exists('$tag');

        $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");
        $lastPlacedOrderInfo = null;
        if($lastPlacedOrderID !== NULL)
        {
            $lastPlacedOrderInfo = modApiFunc("Checkout", "getOrderInfo", $lastPlacedOrderID, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $lastPlacedOrderID));
        }

        switch ($tag)
        {
            case 'Local_PaymentMethodOutput':
                $value = $this->getPaymentModuleOutput();
                break;
            case 'CheckoutPaymentMethodsOutput':
                //          -                             PersonInfoType,                   ,
                //                                    .
                $person_info_types = modApiFunc("Checkout", "getPersonInfoTypeList");
                foreach($person_info_types as $id => $info)
                {
                    if($info['tag'] == "paymentModule" &&
                       $info['active'] == DB_FALSE)
                    {
                        $value = "";
                        break 2;
                    }
                }
                $this->templateFiller = &$application->getInstance('TemplateFiller');
                $this->template = $application->getBlockTemplate('CheckoutView');
                $this->templateFiller->setTemplate($this->template);
                $value = $this->templateFiller->fill("PaymentMethodsOutput");
                break;

            case 'Local_CreditCardInfoJSAttrRules':
                $i=0;
                $cc_types = modApiFunc("Configuration", "getCreditCardSettings", true);
                foreach($cc_types as $type => $val)
                {
                    $value .= "\nnames['$type'] = $i;\n";
                    $value .= "cc_attrs[$i] = new Array();\n";
                    $at = modApiFunc("Configuration", "getAttributesForCardType", $val['id']);
                    $j=0;
                    foreach ($at as $id => $attr)
                    {
                        $value .= "cc_attrs[$i][$id] = new Array();\n";
                        $value .= "cc_attrs[$i][$id][0] = {$attr['visible']};\n";
                        $value .= "cc_attrs[$i][$id][1] = {$attr['required']};\n";
                    }
                    $i++;
                }
                break;

            case 'Local_PaymentMethods':
                $value = $this->getPaymentModulesList();
                break;
            case 'CheckoutPaymentMethodsSelect':
                //          -                             PersonInfoType,                   ,
                //                                    .
                $person_info_types = modApiFunc("Checkout", "getPersonInfoTypeList");
                foreach($person_info_types as $id => $info)
                {
                    if($info['tag'] == "paymentModule" &&
                       $info['active'] == DB_FALSE)
                    {
                        $value = "";
                        break 2;
                    }
                }
//                $value .= $this->getPaymentModulesList();

//                //The name of the outputted data block is passed as a hidden field,
//                // to know what data should have come to Action, and how to check it.
//                // The first idea was that the data shouldn't have been filled in CheckoutView,
//                //  each separate storeBlock/prerequisite fills it itself.
//                $HiddenField = "<input type=\"hidden\" name=\"SubmitedCheckoutStoreBlocksList[payment-method-list-input]\">";
//                $value .=  $HiddenField;
                $this->templateFiller = &$application->getInstance('TemplateFiller');
                $this->template = $application->getBlockTemplate('CheckoutView');
                $this->templateFiller->setTemplate($this->template);
                $value = $this->templateFiller->fill("PaymentMethodsList");
                break;

            case 'Local_ConfirmationFormAction':
            case 'CheckoutConfirmationFormAction':
//                // If it is a CheckoutConfirmation page, inquire for a URL from the class
//                // view "CheckoutConfirmation", otherwise leave the current address.
//                if(modApiFunc("Checkout", "isCheckoutConfirmationStep", modApiFunc("Checkout", "getCurrentStepID")))
//             {
                //Inquire for a Payment gateway address or a local address of the next
                // checkout step (perhaps invsible one) from CheckoutConfirmation view.
                // It can be inquired from the current selected Payment module.
                // If the module hasn't been selected, and you are at CheckoutConfirmation -
                // output an error.

                //Check if Action "ConfirmOrder" was performed. If it has been done,
                // it's time to refer a buyer to the payment gateway, otherwise refer
                // him to "ConfirmOrder".

                $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");

                if(!empty($lastPlacedOrderID))
                {
                    $paymentProcessingData = $this->cachedGetConfirmationData($lastPlacedOrderID);
                    $value = $paymentProcessingData["FormAction"];
                }
                else
                {
                    //The page "Checkout Confirmation"
                    //Refer to Action "ConfirmOrder".
                    //The blowfish-encrypted data can be lost
                    //GET-parameters are specified in the acrtion form at the time, when this form is POST.
                    //can GET and POST parameters be mixed up?
                    $request = new Request();
                    $request->setView  ( 'CheckoutView' );
                    $request->setAction( 'ConfirmOrder' );
                    $value = $request->getURL();
                }
//            }

                break;

            case 'Local_ConfirmationFormMethod':
            {
                $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");

                if(!empty($lastPlacedOrderID))
                {
                    $paymentProcessingData = $this->cachedGetConfirmationData($lastPlacedOrderID);
                    $value = $paymentProcessingData["FormMethod"];
                }
                else
                {
                    //Perhaps it's an odd branch.
                    $value = 'post';
                }
                break;
            }

            case 'Local_CheckoutConfirmationBodyOnLoad':
            {
                $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");

                if(!empty($lastPlacedOrderID))
                {
                    //Get payment confirmation data
                    $oInfo = modApiFunc("Checkout", "getOrderInfo", $lastPlacedOrderID, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $lastPlacedOrderID));
                    $module_id = $oInfo["PaymentModuleId"];
                    $pmInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $module_id);
                    ////check if function exists
                    $pmObj = &$application->getInstance($pmInfo["APIClassName"]);
                    if (method_exists($pmObj, "getBodyOnLoad")==true)
                    {
                        $value  = modApiFunc($pmInfo["APIClassName"], "getBodyOnLoad");
                    }
                    else
                    {
                        $value = "document.forms['checkout'].submit();";
                    }
                }
                else
                {
                    //Perhaps it's an odd branch.
                    //Refer to Action "ConfirmOrder".
                    $request = new Request();
                    $request->setView  ( 'CheckoutView' );
                    $request->setAction( 'ConfirmOrder' );
                    $value = $request->getURL();
                }
                break;
            }

            case "Local_FormName":
                $value = "checkout";
                break;

            case 'Local_FormAction':
                $request = new Request();
                $request->setView  ( 'CheckoutView' );
                $value = $request->getURL();
                break;

            case 'Local_FormMethod':
                //If it is a CheckoutConfirmation page, inquie for Method from the class
                // view "CheckoutConfirmation",otherwise the default value "POST".
                if(modApiFunc("Checkout", "isCheckoutConfirmationStep", modApiFunc("Checkout", "getCurrentStepID")))
                {
                    _fatal(array( "CODE" => "CORE_055"), __FILE__, __LINE__);
                }
                else
                {
                    $value = "post";
                }
                break;
/*        case 'ViewState':
            $value = $this->getViewStateAsHiddenFieldList();
            break;
*/

            case "Local_FormHiddenFields":
                $value = '<input type="hidden" name="' . getLocal_FormStepIDFieldName() . '" value="' . getLocal_FormStepIDFieldValue() . '" />'.
                         '<input type="hidden" name="' . getLocal_FormPreviousStepIDFieldName() . '" value="' . getLocal_FormPreviousStepIDFieldValue() . '" />' .
                         '<input type="hidden" name="' . getLocal_FormCHECKOUT_CZ_BLOWFISH_KEYName() . '" value="' . getLocal_FormCHECKOUT_CZ_BLOWFISH_KEYValue() . '" />';
                break;

            case "Local_FormActionFieldName":
                $value = "asc_action";
                break;

            case "Local_FormActionFieldValue":
                $value = "SetCurrStep";
                break;

            case "Local_FormStepIDFieldName":
                $value = "step_id";
                break;

            case "Local_FormPreviousStepIDFieldName":
                $value = "previous_step_id";
                break;

            case 'Local_FormStepIDFieldValue':
            case 'CheckoutFormNextStepID':
                $value = modApiFunc("Checkout", "getCurrentStepID") + 1;
                break;

            case "Local_FormCHECKOUT_CZ_BLOWFISH_KEYName":
                $value = "CHECKOUT_CZ_BLOWFISH_KEY";
                break;

            case "Local_FormCHECKOUT_CZ_BLOWFISH_KEYValue":
                $value = modApiFunc("Checkout", "getPerRequestVariable", "CHECKOUT_CZ_BLOWFISH_KEY");
                break;


            case 'Local_FormPreviousStepIDFieldValue':
            case 'CheckoutFormCurrentStepID':
                $value = modApiFunc("Checkout", "getCurrentStepID");
                break;

            case 'CheckoutErrors':
                $value = $this->getErrors();
                break;

            case 'CustomPaymentGatewayPageContents' :
                $value = modApiFunc("Checkout", "getCustomPaymentGatewayPageContents");
                break;

            case 'Local_ProcessPaymentHiddenFields':
                $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");

                if(!empty($lastPlacedOrderID))
                {
                    $paymentProcessingData = $this->cachedGetConfirmationData($lastPlacedOrderID);
                    $_value = "";
                    if(!empty($paymentProcessingData["DataFields"]))
                    {
                        foreach($paymentProcessingData["DataFields"] as $key => $val)
                        {
                        	if(is_array($val) && !empty($val))
                        	{
                                //: Have the values $key and $val to be converted to the
                                //admissible format?
                                foreach($val as $val_item)
                                {
                                    $_value .= "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $val_item . "\" />";
                                }
                        	}
                        	else
                        	{
                                //: Have the values $key and $val to be converted to the
                                //admissible format?
                                $_value .= "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $val . "\" />";
                        	}
                        }
                    }
                    $value = $_value;
                }
                else
                {
                    //: Perhaps it's an odd branch.
                    //Refer to Action "ConfirmOrder".
                    $request = new Request();
                    $request->setView  ( 'CheckoutView' );
                    $request->setAction( 'ConfirmOrder' );
                    $value = $request->getURL();
                }
                break;

            case 'Local_FormFieldErrorIndex':
            case 'Local_FormFieldErrorText':
            {
                $value = $this->_Local_FormFieldError[$tag];
                break;
            }

            case 'Local_OrderID':
            {
                $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");

                $value = ($lastPlacedOrderID === NULL) ? "" : modApiFunc('Checkout', 'outputOrderId', $lastPlacedOrderID);
                break;
            }
            case 'Local_Step4PaymentModuleMessage':
            {
                $module_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
                if($module_id === NULL)
                {
                    $value = '';
                    break;
                }
                $pmInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $module_id);

                $classObj = &$application->getInstance($pmInfo["APIClassName"]);
                if (method_exists($classObj, "getStep4PaymentModuleMessage"))
                    $value = modApiFunc($pmInfo["APIClassName"], "getStep4PaymentModuleMessage");
                else
                    $value = '';
                break;
            }
            case 'Local_OrderTotalRaw':
            {
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = $lastPlacedOrderInfo['Total'];
                }
                break;
            }
            case 'Local_OrderTotal':
            {
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = modApiFunc("Localization", "currency_format", $lastPlacedOrderInfo['Total']);
                }
                break;
            }

            case "Local_OrderDate":
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = modApiFunc("Localization", "date_format", $lastPlacedOrderInfo['Date']);
                }
                break;

            case "Local_OrderStatus":
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = $lastPlacedOrderInfo['Status'];
                }
                break;

            case "Local_OrderPaymentStatus":
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = $lastPlacedOrderInfo['PaymentStatus'];
                }
                break;

            case "Local_OrderPaymentMethod":
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = $lastPlacedOrderInfo['PaymentMethod'];
                }
                break;

            case "Local_OrderPaymentProcessorOrderId":
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = $lastPlacedOrderInfo['PaymentProcessorOrderId'];
                }
                break;

            case "Local_OrderShippingMethod":
                $value = '';
                if($lastPlacedOrderInfo !== NULL)
                {
                    $value = $lastPlacedOrderInfo['ShippingMethod'];
                }
                break;

            case 'Local_CartMinSubtotal':
            	$value = modApiFunc("Localization", "currency_format", modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT));
                break;

            default:
                //$value = "default CheckoutView::getTag() value." . $tag;
                do_action("checkout_view",$tag);
                 $val1=modApiFunc('Session', 'get','plugin_return_action');
                 if(strcmp($tag,"Local_OrderLinkInvoicePDF")==0)
                {


                    $value=$val1;
                }
                break;
        }
        return $value;
    }

    function cachedGetConfirmationData($lastPlacedOrderID)
    {
        if ($this->cachedConfirmationData == null)
        {
	        $oInfo = modApiFunc("Checkout", "getOrderInfo", $lastPlacedOrderID, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $lastPlacedOrderID));
	        $module_id = $oInfo["PaymentModuleId"];
	        $pmInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $module_id);
	        $this->cachedConfirmationData = modApiFunc($pmInfo["APIClassName"], "getConfirmationData", modApiFunc("Checkout", "getLastPlacedOrderID"));
        }
        return $this->cachedConfirmationData;
    }


//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Pointer to the module object.
     */
    var $pCheckout;

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;

    var $MessageResources;

    var $cachedConfirmationData;

    /**#@-*/
}
?>