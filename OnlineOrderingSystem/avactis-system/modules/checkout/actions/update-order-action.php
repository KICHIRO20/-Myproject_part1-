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
 * Action handler on OrderUpdateLight.
 *
 * @package Checkout
 * @access  public
 */
class UpdateOrderAction extends AjaxAction
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
    function UpdateOrderAction()
    {
    }

    /**
     * @ describe the function OrdersSearchByDate->.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $EditOrderForm = $_POST;
        // create an empty array of errors.
        $EditOrderForm["ViewState"]["ErrorsArray"] = array();

        // define the user actions
        switch ($request->getValueByKey('FormSubmitValue'))
        {
            case 'AdvancedForm':
                $EditOrderForm['style'] = ORDERS_INFO_ADVANCED_FORM;
                // save user data in the session, to use it in view
                modApiFunc('Session', 'set', 'EditOrderForm', $EditOrderForm);
                break;

            case 'Save':
                $style = $request->getValueByKey('style', ORDERS_INFO_SIMPLE_FORM);
            	# required parameters
            	$order_id = $request->getValueByKey('order_id');
            	$status_id = $request->getValueByKey('status_id');
            	$payment_status_id = $request->getValueByKey('payment_status_id');
            	$track_id = $request->getValueByKey('track_id');
            	$comment = $request->getValueByKey('comment');
            	# parameters passed through the advanced form
            	$processor_order_id = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('processor_order_id') : null;
            	$payment_method = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('payment_method') : null;
            	$shipping_method = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('shipping_method') : null;
            	$customer_info = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('customer') : null;
            	$billing_info = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('billing') : null;
            	$shipping_info = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('shipping') : null;
                $bank_account_info = ($style == ORDERS_INFO_ADVANCED_FORM) ? $request->getValueByKey('bankaccount') : null;
                /**
                 * If the user inputted RSA Private key and decrypted info (and then perhaps edited it),
                 * then save new values in the database decrypted.
                 * Otherwise, do not update the database.
                 */
            	$credit_card_info = ($style == ORDERS_INFO_ADVANCED_FORM) ?
                    (
                        ($request->getValueByKey('group_creditCardInfo_is_encrypted') == 'false')
                        ?
                            $request->getValueByKey('creditcard')
                        :
                            null
                    )
                    :
                    null;

                $tax_exemption = null;
                $order_totals = null;
                $product_prices = null;
                $product_qty = null;
                $product_names = null;
                $product_options = null;
                $taxes = null;

          //===============================================================

                // order advanced edit
                if ($style == ORDERS_INFO_ADVANCED_FORM)
                {
                    // taxes
                    $taxes = $request->getValueByKey("tax");

                    // tax exemption
                    $tax_exemption = $request->getValueByKey("taxExemption", null);
                    if ($tax_exemption)
                    {
                        if ($tax_exemption === "on")
                        {
                            $tax_exemption = "true";
                        }
                        else
                        {
                            $tax_exemption = null;
                        }
                    }
                    else
                        $tax_exemption = "false";

                    // product names
                    $product_names = $request->getValueByKey("productName");

                    // product options
                    $product_options = $request->getValueByKey("productOption");

                    // product_price
                    $product_prices = $request->getValueByKey("productPrice");

                    // product quantities
                    $product_qty = $request->getValueByKey("productQty");

                    // totals
                    $order_totals = array(
                             "arePricesEdited"    => $request->getValueByKey("arePricesEdited")
                            ,"global_discount"    => $request->getValueByKey("globalDiscount")
                            ,"promocode_discount" => $request->getValueByKey("promoCodeDiscount")
                            ,"qty_discount"       => $request->getValueByKey("qtyDiscount")
                            ,"shipping_handling"  => $request->getValueByKey("shippingHandling")
                            ,"order_currency"     => $request->getValueByKey("order_currency")
                        );

                }

          //===============================================================

            	$data = array();
            	if (is_array($order_id))
            	{
            		foreach ($order_id as $order)
            		{
            			$data[] = array(
                            'order_id' => $order
                           ,'status_id' => $status_id[$order]
                           ,'payment_status_id' => $payment_status_id[$order]
                           ,'track_id' => $track_id[$order]
                           ,'comment' => $comment[$order]
                           ,'processor_order_id' => null
                           ,'payment_method' => null
                           ,'shipping_method' => null
                           ,'customer_info' => null
                           ,'billing_info' => null
                           ,'shipping_info' => null
                           ,'bank_account_info' => null
                           ,'credit_card_info' => null
                           ,"tax_exemption" => null
                           ,"order_totals" => null
                           ,"product_prices" => null
                           ,"product_qty" => null
                           ,"product_names" => null
                           ,"product_options" => null
                           ,"taxes" => null
            			);
            		}
            	}
            	else
            	{
        			$data[] = array(
                        'order_id' => $order_id
                       ,'status_id' => $status_id
                       ,'payment_status_id' => $payment_status_id
                       ,'track_id' => $track_id
                       ,'comment' => $comment
                       ,'processor_order_id' => $processor_order_id
                       ,'payment_method' => $payment_method
                       ,'shipping_method' => $shipping_method
                       ,'customer_info' => $customer_info
                       ,'billing_info' => $billing_info
                       ,'shipping_info' => $shipping_info
                       ,'bank_account_info' => $bank_account_info
                       ,'credit_card_info' => $credit_card_info
                       ,"tax_exemption" => $tax_exemption
                       ,"order_totals" => $order_totals
                       ,"product_prices" => $product_prices
                       ,"product_qty" => $product_qty
                       ,"product_names" => $product_names
                       ,"product_options" => $product_options
                       ,"taxes" => $taxes
                    );
            	}

            	$this->result = modApiFunc('Checkout', 'updateOrder', $data);
                modApiFunc('Session','set','ResultMessage','MSG_ORDER_UPDATED');
                $EditOrderForm["ViewState"]["hasCloseScript"] = "true";
                modApiFunc('Session', 'set', 'OrderViewState', $EditOrderForm);
                break;
        }

        //prevent twice POST
        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $application->redirect($r);
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