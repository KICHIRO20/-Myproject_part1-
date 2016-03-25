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
 * @package Cart
 * @author Alexander Girin
 */
class AddToCart extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AddToCart constructor.
     */
    function AddToCart()
    {
    }

    /**
     * Adds the product to the cart.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
	$msgres = $application->getInstance("MessageResources", "messages");

        // checking if wishlist product is being added
        $wl_id = $request->getValueByKey('wl_id');
        if ($wl_id > 0)
        {
            $data = modApiFunc('Wishlist', 'getWishlistRecordCartData', $wl_id);
            if (!$data)
            {
                // invalid wishlist record...
                $request = new Request();
                $request -> setView('Wishlist');
                $application -> redirect($request);
		$this->setStatusError();
		$this->setMessage('Invalid wishlist record');
            }

            $options_sent = $data['options_sent'];
            $prod_id = $data['entity_id'];
        }
        else
        {
            $prod_id = $request->getValueByKey('prod_id');

            $data=array(
                'parent_entity' => 'product'
               ,'entity_id' => $prod_id
               ,'options' => $request->getValueByKey('po')
               ,'qty' => $request->getValueByKey('quantity_in_cart')
     		   ,'colorname' => $request->getValueByKey('colorname')

            );

            $options_sent = $request->getValueByKey('options_sent');
        }

        // setting qty=1 if quantity_in_stock is not specified
        if (!$data['qty'] && $data['qty'] !== 0)
            $data['qty'] = 1;

        if($data['options']==null)
        {
            $data['options']=array();
        }


        $result = modApiFunc('Cart', 'processPostedProductData', $data, $options_sent);

        if (!$result['is_error'])
        {
            if(!empty($result['stock_discarded_by_warning']))
            {
                modApiFunc('Session','set','StockDiscardedBy',$result['stock_discarded_by_warning']);
            }

            $added = modApiFunc('Cart', 'addToCart', $result['data']);
	    $cc = modApiFunc('Cart', 'getCartContent');
	    $cartPrice = '';
	    $cart_id = $prod_id."_".modApiFunc("Product_Options", "getCombinationHash", $result['data']['options']);
	    if( !empty($cc))
		    foreach($cc as $product)
			    if($product["CartID"]==$cart_id)
				    $cartPrice = modApiFunc("Localization", "format", $product["CartItemSalePrice"], "currency");

	    $this->setStatusSuccess();

	    $p = new CProductInfo($prod_id);
	    $message = array(
		    "<h2>".$msgres->getMessage('CATALOG_PRODUCT_ADDED')."</h2>".
		    "<div class='ajax_message_cart_prod_link'><a href='". $p->getProductInfoLink($prod_id, $p->chooseCategoryID()) ."'>".$p->getProductTagValue('name')."</a></div>".
                   "<div class='ajax_message_cart_prod_qty'>".$data['qty']."&nbsp;".$msgres->getMessage('CATALOG_X')."&nbsp;".$cartPrice."</div>"
            );

            // if a wishlist product is being added...
            if ($wl_id > 0 && $added)
            {
                modApiFunc('Wishlist', 'removeFromWishlist', $wl_id);
		$message[] = 'This product was removed from your Wishlist';
            }

	    $this->setMessage($message);
            $request = new Request();
            $request->setView(CURRENT_REQUEST_URL);
            $application->redirect($request);
        }
        else
        {
	    $this->setStatusError();
	    $message = array();

            if($result['discard_by'] != 'none')
            {
                modApiFunc('Session','set','OptionsDiscardedBy',$result['discard_by']);
		$message[] = $msgres->getMessage($result['discard_by']);
            }
            if($result['stock_discarded_by'] != 'none')
            {
		    modApiFunc('Session','set','StockDiscardedBy',$result['stock_discarded_by']);
		    $message[] = $msgres->getMessage($result['stock_discarded_by']);
            }
            modApiFunc('Session','set','sentCombination',$result['data']['options']);
	    $this->setMessage($message);
            $request = new Request();
            $request->setView('ProductInfo');
            $request->setAction('SetCurrentProduct');
            $request->setKey('prod_id',$prod_id);
            $request->setProductID($prod_id);
            $p = new CProductInfo($prod_id);
            $request->setCategoryID($p->chooseCategoryID());
            $application->redirect($request);
        };
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