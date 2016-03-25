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
 * Action handler on UpdatePaymentStatus.
 *
 * @package Checkout
 * @access  public
 */
class UpdatePaymentStatus extends AjaxAction
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
    function UpdatePaymentStatus()
    {
    }

    /**
     * Returns a list of Checkout-related store blocks, which were viewed on the
     * previous View, and data which is going to be found in GET/POST.
     * Every block can declare itself by outputting some hidden field in the View.
     * For example SubmitedCheckoutStoreBlocksList['shipping-address'].
     */
    function getLastViewSubmitedCheckoutStoreBlocksList()
    {
        global $application;
        $request = $application->getInstance('Request');
        if($request->getValueByKey('SubmitedCheckoutStoreBlocksList') != NULL)
        {
            return $request->getValueByKey('SubmitedCheckoutStoreBlocksList');
        }
        else
        {
            return array();
        }
    }

    /**
     *  if not paypal.
     */
    function getOrderIDFromGatewayResponse()
    {
        global $application;
        $request = $application->getInstance('Request');
        $order_id = $request->getValueByKey( 'asc_oid' );
        if($order_id === NULL)
        {
            //PayFlow doesn't allow to pass a GET parameter with the name 'asc_oid'
            $order_id = $request->getValueByKey( 'USER1' );
        };
        if($order_id === NULL && class_exists('Payment_Module_Cyberbit_CC'))
        {
            //check for CyberBit
            $xml = $request->getValueByKey('xml');
            if($xml != null)
            {
                $order_id = modApiFunc('Payment_Module_Cyberbit_CC','getOrderIDFromXML',$xml);
            };
        };
        if($order_id === NULL && class_exists('Payment_Module_EPDQ_CC'))
        {
            //check for ePDQ
            $order_id = modApiFunc('Payment_Module_EPDQ_CC','getOrderIDFromPost');
        };
        if($order_id === NULL && class_exists('Payment_Module_IDEAL_CC'))
        {
            //check for iDEAL
            $order_id = modApiFunc('Payment_Module_IDEAL_CC','getOrderIDFromRequest');
        };
        if ($order_id === NULL)
        {
            // Gate2Shop
            $order_id = $request->getValueByKey("ClientUniqueID");
        }
        if ($order_id === NULL)
        {
            // WorldPay
            $order_id = $request->getValueByKey("cartId");
        }
        if($order_id === NULL)
        {
            //get from Session
            if(modApiFunc('Session','is_set','lastPlacedOrderID'))
            {
                $order_id = modApiFunc('Session','get','lastPlacedOrderID');
            };
        };
        return $order_id;
    }

    function getPaymentModuleByOrderID($oid)
    {
        $oInfo = modApiFunc("Checkout", "getOrderInfo", $oid, modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $oid, GET_PAYMENT_MODULE_FROM_ORDER));
        return $oInfo["PaymentModuleId"];
///        //! Change the DB and store there the ID of the AVACTIS payment gateway!
///        return 3;
    }

    /**
     * Updates a payment status for the given order. According to information
     * from the payment gateway.
     *
     * Action: UpdatePaymentStatus.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $oid = $this->getOrderIDFromGatewayResponse();
        $pm_id = $this->getPaymentModuleByOrderID($oid);
        $pm_info = modApiFunc("Checkout", "getPaymentModuleInfo", $pm_id);
        $this->GlobalUniquePaymentModuleID = $pm_info["GlobalUniquePaymentModuleID"];
        $pm_APIClassName = $pm_info["APIClassName"];

        $data = array("_POST" => $_POST,
                      "_GET" => $_GET);

        $result = modApiFunc($pm_APIClassName, "processData", $data, $oid);
        $EventType = $result["EventType"];
        $this->result = $result["statusChanged"];

            //payment_module_paypal_cc
            //modApiFunc($pm_APIClassName, "processData", $data, $DBPaymentStatus, $bStop, $bPaymentFinished, $resultEvent);
            //$resultEvent:
            //  {
            //    ConfirmationFailure        follows to "confirmation-error"
            //                                   (payment-method step by default)
            //        An example:
            //        1) the payment gateway doesn't reply
            //        2) the payment gateway reports an error, which makes impossible
            //        the second payment effort
            //        I.e. these are those variants, which require to select the other Payment gateway.
            //
            //    ConfirmationCustomPaymentPage
            //                               follows to "confirmation-success"
            //                                   (next to confirmation step)
            //
            //    ConfirmationSuccess        follows to "confirmation-success"
            //                                   (next to confirmation step)
            //    BackgroundEvent            outputs nothing
            //                                   (payment module's output goes to payment gateway)
            //  }

/////            $messages = $res["Messages"];
/////            foreach($messages as $message)
/////            {
/////                if(!empty($message['paymentStatusId']))
/////                {
/////                    modApiFunc("Checkout", "UpdatePaymentStatusInDB", $oid, $message['paymentStatusId'], $message["historyMessage"]);
/////                }
/////                else
/////                {
/////                    //only add message, without changing order's payment status
/////                    modApiFunc("Checkout", "addHistoryNoticeToOrderInDB", $oid, $message["historyMessage"]);
/////                }
/////            }

        $step_id = NULL; // = $request->getValueByKey( 'step_id' );
        $previous_step_id = NULL; // = $request->getValueByKey( 'previous_step_id' );
        //Previous_step_id can be undefined if it's an automated request from
        // payment gateway.

        switch($EventType)
        {
            case "BackgroundEvent":
                //
                //no additional output is needed. Payment module has already
                // sent all outputs to Payment Gateway.
                $application->_exit();
                break;

            case "ConfirmationSuccess":
                //go to the step next to checkout confirmation
                //: ask checkout for proper step_id
                $step_id = modApiFunc("Checkout", "getCurrentStepID") + 1;
                break;

            case "ConfirmationFailure":
                //ask checkout for the step id at which payment method is defined.
                //Workaround - set "Payment method" prerequisite to "Not Met" and provide error message. And don't change step id (keep "Confirmation" step id).
                $isMet = false;
                $res = modApiFunc("Checkout", "getPrerequisiteValidationResults", 'paymentModule');
                $validatedData = $res["validatedData"];
                //$res['error_code'] = "";//'CHECKOUT_ERR_CUSTOMER_INFO_EMAIL_001';
                /* Clear out the selected payment module, and make the user return to that checkout step,
                 * where the payment module is selected. Define an appropriate step for it and redirect.
                 * You can just remove the field "selected payment module", but then
                 * the system in the method ProcessNewStepID below, when checking, will add an error
                 * "no data on selected payment module", which is unwanted.
                 */
                modApiFunc("Checkout", "setPrerequisitesValidationResultsItem", "paymentModule", "", $isMet, "", array(), $validatedData);

                $step_id_to_redirect_to = modApiFunc('Checkout', 'getStepIDtoRedirectToAfterPrerequisitesValidationErrors', modApiFunc("Checkout", "getCurrentStepID"));

                $request = new Request();
                $request->setView('CheckoutView');
                $request->setAction("SetCurrStep");
                $request->setKey   ( 'step_id', $step_id_to_redirect_to);
               //This field is added to be used in hook, e.g. for Authorize.Net to Redirect to the Store Front.
                $this->RedirectToURL = $request->getURL();
                $application->redirect($request);
                return;
            case "ConfirmationCustomPaymentPage":
                $step_id = modApiFunc("Checkout", "getCurrentStepID");
                $previous_step_id = modApiFunc("Checkout", "getCurrentStepID");
                break;

            default:
                //Payment module returned undefined $resultEvent code.
                //: TEST IT
                $err_params = array(
                                    "CODE"    => "CHECKOUT_PAYMENT_001"
                                   );
                _fatal($err_params, $pm_APIClassName, $pm_id, $EventType);
                break;
        }

        modApiFunc("Checkout", "ProcessNewStepID", $step_id);
        modApiFunc("Checkout", "saveState");
        $step_id_to_redirect_to = modApiFunc("Checkout", "getCurrentStepID");
        $request = new Request();
        $request->setView('CheckoutView');
        $request->setAction("SetCurrStep");
        $request->setKey   ( 'step_id', $step_id_to_redirect_to);
        //This field is added to be used in hook, e.g. for Authorize.Net to Redirect to the Store Front.
        $this->RedirectToURL = $request->getURL();
        // for Worldpay redirects 301/302 are prohibited
        if ($pm_APIClassName == 'Payment_Module_Worldpay_CC')
            $application->jsRedirect($request);
        else
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