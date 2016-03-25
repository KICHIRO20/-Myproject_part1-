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
 * Action handler on ConfirmOrder.
 *
 * @package Checkout
 * @access  public
 */

class ConfirmOrder extends AjaxAction
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
    function ConfirmOrder()
    {
    }

    function checkPromoCode()
    {
        $promo_code_id = modApiFunc("PromoCodes", "getPromoCodeId");
        if(modApiFunc("PromoCodes", "isPromoCodeIdSet") === true)
        //if(!empty($promo_code_id))
        {
            //                              Inactive
            //    -                           .
            $b_applicable = modApiFunc("PromoCodes", "isPromoCodeApplicableWithoutMinSubtotal", $promo_code_id);
            if($b_applicable !== true)
            {
                modApiFunc("PromoCodes", "removePromoCode");
                modApiFunc("PromoCodes", "setAddPromoCodeError", "CART_ADD_PROMO_CODE_ERROR_002");

                $request = new Request();
                $request->setView  ( 'CartContent' );
                $value = $request->getURL();
                header("Location:" . $value);
                exit(0);
            }
        }
    }

    function placeOrderIntoDB()
    {
        $order_id = modApiFunc("Checkout", "createOrderInDB");
        //Change the session.
        //Remove product info
        modApiFunc("Cart", "removeAllFromCart");
        return $order_id;
    }

    function &getPMInfo()
    {
        global $application;
        $module_id = modApiFunc("Checkout", "getChosenPaymentModuleIdCZ");
        if($module_id === NULL)
        {
            $module_id = modApiFunc("Checkout", "getAllInactiveModuleId", "payment");
        }
        $pmInfo = modApiFunc("Checkout", "getPaymentModuleInfo", $module_id);
        $pmObject = &$application->getInstance($pmInfo["APIClassName"]);
        return $pmObject;
    }

    /**
     * Saves confirmed data about order in the DB.
     * It changes the data in the session.
     *
     * Action: confirmOrder
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;

        // exclusive action
        $application->enterCriticalSection('ConfirmOrderAction');

        $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");
        $current_step = modApiFunc('Checkout', 'getCurrentStepID');

        if(empty($lastPlacedOrderID) && $current_step == 3)
        {
            //                           ,
            if (modApiFunc("Cart","wasCartModified") === true)
            {
                $MessageResources_CZ = &$application->getInstance('MessageResources',"messages");
                $msg = $MessageResources_CZ->getMessage("CHECKOUT_ERR_006");
                modApiFunc("Session", "set", "ShoppingCartResultMessage", $msg);
                $request = new Request();
                $request->setView('CartContent');
                $application->redirect($request);
                $application->leaveCriticalSection();
                return;
            }
            //                     -         , GET        action=ConfirmOrder,                                  .
            else if((modApiFunc("Cart", "getCartProductsQuantity") == 0))
            {
                $request = new Request();
                $request->setView('CartContent');
                $application->redirect($request);
                $application->leaveCriticalSection();
                return;
            }

            $CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST = modApiFunc("Checkout", "getPerRequestVariable", "CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST");
            if($CHECKOUT_CZ_BLOWFISH_KEY_WAS_LOST === true)
            {
                //         ,
                //                   ,                                        .
                //            Credit Card Info:
                modApiFunc("Checkout", "estimateLostEncryptedData");
            }

            //                        -                      .
			//                           .
			//                              .
		    $this->checkPromoCode();

            //Trying to start background process
            $pmObject = &$this->getPMInfo();

            //Select either Direct Payment mode or Redirect to Payment Gateway mode.
            $mode = method_exists($pmObject, "getPaymentMode")? $pmObject->getPaymentMode() :"redirect";
            switch ($mode)
            {
                case "direct":
                    //invoke the backgroundProcess method of the selected payment gateway
                    //The method performs the following actions:
                    // 1. It prepares and sends data to the payment gateway, using the class bouncer;
                    // 2. It gets and processes data from the payment gataway
                    //    - on success, adds the order to the DB (with the specified payment status), clears the cart, updates
                    //   the product number in the stock.
                    //    - on failure, redirects to specified checkout step, depending on the error, i.e. ( the Info page or Shipping/Payment Select)
                    $pmObject->backgroundProcess();
                    break;
                case "redirect":
                    //Create order
                    $order_id = $this->placeOrderIntoDB();

                    //Display the page, which redirects to
                    // the payment gateway site(if JavaScript is on).
                    $application->prepareStorefrontBlockTag("CheckoutView");
                    echo modApiFunc("CheckoutView", "outputRedirectPage");
                    $application->leaveCriticalSection();
                    $application->_exit();
                    break;
                default:
                    $request = new Request();
                    $request->setView('CheckoutView');
                    $application->redirect($request);
                    break;
            }
        }
        else
        {
            //Error: customer has already passed "Confirm Order" page. Redirect to "You cart is empty".
            $request = new Request();
            $request->setView('CheckoutView');
            $application->redirect($request);
        }
        $application->leaveCriticalSection();
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