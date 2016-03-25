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
 *      action                                                    .
 *
 *                             .                   5654         Cart
 *                .                                                       -                            .
 *                                                   Cart (         loadState).
 *             ,                            action,                                ,                    ,
 *                        .
 *
 * @package Cart
 * @author Alexander Girin
 */
class UpdateCartContent extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * UpdateCartContent constructor.
     */
    function UpdateCartContent()
    {
    }

    /**
     * Updates the cart contents.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $msgres = $application->getInstance("MessageResources","messages");

        $cart_id = $request->getValueByKey('cart_id');
        $quantity_in_cart = $request->getValueByKey('quantity_in_cart');

        if (!is_array($cart_id))
        {
            $cart_id = array(0=>$cart_id);
        }

        if (!is_array($quantity_in_cart))
        {
            $quantity_in_cart = array(0=>$quantity_in_cart);
        }

        if (is_array($cart_id))
        {
            for ($i=0; $i<sizeof($cart_id); $i++)
            {
                $obj_cart = &$application->getInstance('Cart');
                if (!isset($obj_cart->CartContent[$cart_id[$i]]))
                {
                    continue;
                }

                $quantity_in_cart[$i] *= 1;
                if (is_int($quantity_in_cart[$i]) && $quantity_in_cart[$i] > 0 && is_string($cart_id[$i]) && _ml_strlen($cart_id[$i])>2)
                {
	                list($prod_id) = explode('_', $cart_id[$i]);

                    $obj_product = new CProductInfo($prod_id);
                    if ($obj_product->isProductIdCorrect() == false)
                    {
                        continue;
                    }
                    $stock_method = $obj_product->whichStockControlMethod();
                    $qty_in_stock = $obj_product->getProductTagValue('QuantityInStock',PRODUCTINFO_NOT_LOCALIZED_DATA);
                    $sum_qty = $quantity_in_cart[$i];

                    //                          -        Stock.
                    if($stock_method == PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE)
	                {
		                //          ,                                    ,
		                if($qty_in_stock != '' &&
		                   $qty_in_stock <  $sum_qty &&
		                   modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === false )
		                {
		                    modApiFunc("Session", 'set', 'ShoppingCartResultMessage', cz_getMsg("ERR_NOT_ALLOWED_TO_BUY_MORE_THAN_IN_STOCK"));
		                }
		                else
		                {
                            //         ,               "min order"               -                                       .
                            $min_order = $obj_product->getProductTagValue('MinQuantity',PRODUCTINFO_NOT_LOCALIZED_DATA);
                            if($min_order != '' &&
                               $min_order >  $sum_qty)
                            {
                                modApiFunc("Session", 'set', 'ShoppingCartResultMessage', $msgres->getMessage("ERR_NOT_ALLOWED_TO_BUY_LESS_THAN_MIN_ORDER",array($obj_product->getProductTagValue('MinQuantity'))));
                            }
                            else
                            {
	                            modApiFunc('Cart', 'updateQuantityInCart', $cart_id[$i], $quantity_in_cart[$i]);
                            }
		                }
	                }
	                else //                         inventory -
	                {
                        //          ,                                    ,
                        $obj_cart = &$application->getInstance('Cart');
                        $options = $obj_cart->CartContent[$cart_id[$i]]['options'];
                        $inv_id = modApiFunc("Product_Options", "getInventoryIDByCombination", 'product', $prod_id, $options);
                        if($inv_id != null)
                        {
                            $options_settings = modApiFunc("Product_Options","getOptionsSettingsForEntity",'product',$prod_id);
                        	$inv_info = modApiFunc('Product_Options','getInventoryInfo',$inv_id);
                            if($options_settings['AANIS']=='N' && $inv_info['quantity'] < $sum_qty &&
                               modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === false)
                            {
                                modApiFunc("Session", 'set', 'ShoppingCartResultMessage', cz_getMsg("ERR_NOT_ALLOWED_TO_BUY_MORE_THAN_IN_STOCK",$obj_product->getProductTagValue('MinQuantity')));
                            }
                            else
                            {
                                modApiFunc('Cart', 'updateQuantityInCart', $cart_id[$i], $quantity_in_cart[$i]);
                            }
                        }
                        else
                        {
                            $options_settings = modApiFunc("Product_Options","getOptionsSettingsForEntity",'product',$prod_id);
                            if ($options_settings['AANIC']=='Y')
                            {
                                modApiFunc('Cart', 'updateQuantityInCart', $cart_id[$i], $quantity_in_cart[$i]);
                            }
                        }
	                }
                }
            }
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
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