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
 * Cart_Content view
 *
 * @package Cart
 * @author Alexander Girin
 */
class ShoppingCart
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
    	    'layout-file'        => 'cart-content-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_PRODUCT_TYPE
    	    )
    	   ,'options' => array(
    	        'Columns'        => TEMPLATE_OPTION_REQUIRED
    	    )
    	);
    	return $format;
    }

    /**
     * Cart_Content constructor.
     */
    function ShoppingCart()
    {
        global $application;
        $r = &$application->getInstance('Request');
        if($r->getCurrentAction()!='SetCurrentProduct')
        {
        if(modApiFunc("Session", "is_Set", "ShoppingCartResultMessage"))
        {
        	$this->ShoppingCartResultMessage = modApiFunc("Session", "get", "ShoppingCartResultMessage");
        	modApiFunc("Session", "un_set", "ShoppingCartResultMessage");
        }
        elseif(modApiFunc('Session','is_set','StockDiscardedBy'))
        {
            $stock_discarded_by = modApiFunc('Session','get','StockDiscardedBy');
            modApiFunc('Session','un_set','StockDiscardedBy');
            #$MessageResources_CZ = &$application->getInstance('MessageResources',"messages");
            $this->ShoppingCartResultMessage = $stock_discarded_by;#$MessageResources_CZ->getMessage($stock_discarded_by);
        }
        else
        {
            if(modApiFunc("Cart", "getCartProductsQuantity") > 0
              && modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT) > ZERO_PRICE
              && modApiFunc("Checkout", "getOrderPrice", "Subtotal", modApiFunc("Localization", "getMainStoreCurrency")) < modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT))
            {
                $MessageResources_CZ = &$application->getInstance('MessageResources',"messages");
                $msg = $MessageResources_CZ->getMessage("MIN_SUBTOTAL_TO_CHECKOUT", array(modApiFunc("Localization", "currency_format", modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT))));
                $this->ShoppingCartResultMessage = $msg;
            }
            else
            {
                $this->ShoppingCartResultMessage = NULL;
            }
        }
        }
        $req = new Request();

        #check if block level fatal errors exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CartContent"))
        {
            $this->NoView = true;
        }
    }

    /**
     * @ describe the function CartContent->.
     */
    function getCartContent()
    {
        global $application;
        $cc = modApiFunc('Cart', 'getCartContent');

        $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
        if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
            $disable_trtd = true;
        else
            $disable_trtd = false;

        $items = "";
        $col =1;
        $columns = intval($application->getBlockOption($this->template, "Columns"));
        $this->_Cart_Item = null;
        $i=0;
        foreach ($cc as $productInfo)
        {
            $request = new Request();
            $request->setView  ( 'CartContent' );
            $request->setAction( 'RemoveProductFromCart' );
            $request->setKey   ( 'cart_id', $productInfo['CartID']);
            $productInfo['Local_RemoveProductLink'] = $request->getURL();

            $request = new Request();
            $request->setView  ( 'CartContent' );
            $productInfo['Local_FormAction'] = $request->getURL();
            $productInfo['Local_FormId'] = "Product_Quan_".$productInfo['ID'];

            $productInfo['Local_FormActionFieldName'] = 'asc_action';
            $productInfo['Local_FormActionFieldValue'] = 'UpdateCartContent';
            //$productInfo['Local_FormProductIdFieldName'] = 'prod_id';
            //$productInfo['Local_FormProductIdArrayFieldName'] = 'prod_id['.$i.']';
            $productInfo['Local_FormCartIdArrayFieldName'] = 'cart_id['.$i.']';
            $productInfo['Local_CartID']=$productInfo['CartID'];
            $productInfo['Local_HiddenFieldCartID']='<input type="hidden" name="cart_id['.$i.']" value="'.$productInfo['CartID'].'" />';

            $productInfo['Local_CartItemSalePrice']=modApiFunc("Localization", "currency_format", $productInfo['CartItemSalePrice']);
            $productInfo['Local_CartItemWeight']=modApiFunc("Localization", "format", $productInfo['CartItemWeight'],"weight")." ".modApiFunc("Localization","getUnitTypeValue","weight");
            $productInfo['Local_CartItemPerItemShippingCost']=modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemShippingCost']);
            $productInfo['Local_CartItemPerItemHandlingCost']=modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemHandlingCost']);

            $unit_values = $productInfo['attributes']['SalePrice']['unit_type_values'];
            $unit_id = $productInfo['attributes']['SalePrice']['unit_type_value'];
            //$productInfo['Local_ProductSubtotal'] = modApiFunc("Localization", "currency_format", $productInfo['Quantity_In_Cart']*$productInfo['attributes']['SalePrice']['value']);
            $productInfo['Local_ProductSubtotal'] = modApiFunc("Localization", "currency_format", $productInfo['Total']);
            //$productInfo['Local_FormQuantityFieldName'] = 'quantity_in_cart';
            $productInfo['Local_FormQuantityArrayFieldName'] = 'quantity_in_cart['.$i.']';
            $productInfo['Local_FormQuantityFieldName']      = 'quantity_in_cart['.$i.']';

            $productInfo['Local_ProductQuantity'] = $productInfo['Quantity_In_Cart'];
////             $productInfo['Quantity_In_Cart']       ,  . .                                                .                                                      .
//            $productInfo['Local_ProductQuantityOptions'] = modApiFunc("Cart", "getProductQuantityOptions", $productInfo['Quantity_In_Cart'], $productInfo['ID']);

            $productInfo['ImageSize'] = 'width="'.$productInfo['SmallImageWidth'].'" height="'.$productInfo['SmallImageHeight'].'"';
            if ($col == 1)
            {
                if ($disable_trtd == false) $items .= '<tr><td>';
                $col++;
            }
            else
            {
                if ($disable_trtd == false)  $items .= '<td>';
                $col++;
            }
            if ($col > $columns)
            {
                $col = 1;
            }
            $this->_Cart_Item = $productInfo;
            $application->registerAttributes($this->_Cart_Item);
            modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => "prod_id", "value" => $productInfo['ID'])));
            $items .= $this->templateFiller->fill("Item", $productInfo['TypeID']);
            modApiFunc("tag_param_stack", "pop", __CLASS__);
            $i++;
        }
        $this->_Cart_Item = null; // clear the loop variable
        return $items;
    }

    function outputPriceItemSeparator()
    {
        global $application;
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ShoppingCart');
        $this->templateFiller->setTemplate($this->template);
        $value = $this->templateFiller->fill("PriceItemSeparator");
        return $value;
    }

    function outputShoppingCartPrices()
    {
        $ShoppingCartSubtotal = $this->outputPrice("Subtotal");
        $ShoppingCartGlobalDiscount = $this->outputPrice("GlobalDiscount");
        $ShoppingCartPromoCodeDiscount = $this->outputPrice("PromoCodeDiscount");
        $ShoppingCartQuantityDiscount = $this->outputPrice("QuantityDiscount");
        $ShoppingCartDiscountedSubtotal = $this->outputPrice("DiscountedSubtotal");


        $PriceItemSeparator = $this->outputPriceItemSeparator();

        if($ShoppingCartGlobalDiscount == "" &&
           $ShoppingCartPromoCodeDiscount == "" &&
           $ShoppingCartQuantityDiscount == "")
        {
            return $ShoppingCartSubtotal;
        }
        else
        {
            return $ShoppingCartSubtotal
                 . $ShoppingCartGlobalDiscount
                 . $ShoppingCartPromoCodeDiscount
                 . $ShoppingCartQuantityDiscount
                 . $PriceItemSeparator
                 . $ShoppingCartDiscountedSubtotal;
        }
    }

    function outputPrice($tag)
    {
    	global $application;
        $MessageResources_CZ = &$application->getInstance('MessageResources',"messages");

        $_template_tags = array('Local_PriceName' => "",
                                'Local_PriceAmount' => "");
        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ShoppingCart');
        $this->templateFiller->setTemplate($this->template);

    	switch($tag)
        {
        	case "Subtotal":
            {
                $price_formatted = getShoppingCartSubtotal();
                $this->_Local_PriceName = $MessageResources_CZ->getMessage("CART_PRICE_SUBTOTAL_FIELD_NAME");
                $this->_Local_PriceAmount = $price_formatted;
                $value = $this->templateFiller->fill("PriceItem");
                break;
            }
            case "DiscountedSubtotal":
            {
                //            Promo Code    Global Discount -             DiscountedSubtotal,
                //          Subtotal.
                $price_formatted = getShoppingCartDiscountedSubtotal();
                $this->_Local_PriceName = $MessageResources_CZ->getMessage("CART_PRICE_DISCOUNTED_SUBTOTAL_FIELD_NAME");
                $this->_Local_PriceAmount = $price_formatted;
                $value = $this->templateFiller->fill("PriceItem");

                break;
            }
            case "GlobalDiscount":
            {
            	//                                   GlobalDiscount -               ,
                //       -    .
                $global_discount_rates = modApiFunc("Discounts", "getGlobalDiscountRates");
                if(empty($global_discount_rates))
                {
                	$value = "";
                }
                else
                {
                    $price_formatted = getShoppingCartGlobalDiscount();
                    $this->_Local_PriceName = $MessageResources_CZ->getMessage("CART_PRICE_GLOBAL_DISCOUNT_FIELD_NAME");
                    $this->_Local_PriceAmount = $price_formatted;
                    $value = $this->templateFiller->fill("PriceItem");
                }
                break;
            }
            case "QuantityDiscount":
            {
                //                                   QuantityDiscount -               ,
                //       -    .
                $quantity_discount_rates = modApiFunc("Quantity_Discounts", "getQuantityDiscountRates");
                if(empty($quantity_discount_rates))
                {
                    $value = "";
                }
                else
                {
                    $price_formatted = getShoppingCartQuantityDiscount();
                    $this->_Local_PriceName = $MessageResources_CZ->getMessage("CART_PRICE_QUANTITY_DISCOUNT_FIELD_NAME");
                    $this->_Local_PriceAmount = $price_formatted;
                    $value = $this->templateFiller->fill("PriceItem");
                }
                break;
            }
            case "PromoCodeDiscount":
            {
            	//                  -                .       -    .
                if(modApiFunc("PromoCodes", "isPromoCodeIdSet") === false)
                {
                	$value = "";
                }
                else
                {
                    $price_formatted = getShoppingCartPromoCodeDiscount();
                    $this->_Local_PriceName = $MessageResources_CZ->getMessage("CART_PRICE_PROMO_CODE_DISCOUNT_FIELD_NAME");
                    $this->_Local_PriceAmount = $price_formatted;
                    $value = $this->templateFiller->fill("PriceItem");
                }
                break;
            }
            default:
            {
            	//: report error
                $value = "";
            }
        }
        return $value;
    }

    /**
     * Outputs the cart contents view.
     *
     * @ $request->setView  ( '' ) - define the view name
     */
    function output($hide_checkout_link = false)
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CartContent", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "CartContent", "Warnings");
        }

        $this -> hide_checkout_link = $hide_checkout_link;

        $cc = modApiFunc('Cart', 'getCartContent');

        $_template_tags = array('Local_ClearLink' => "",
                                'Local_FormAction' => "",
                                'Local_FormId' => "",
                                'Local_FormActionFieldName' => "",
                                'Local_FormActionFieldValue' => "",
                                'Local_ProductOptionsSelected' => "",
                                'Local_CartItemSalePrice' => "",
                                'Local_CartItemWeight' => "",
                                'Local_CartItemPerItemShippingCost' => "",
                                'Local_CartItemPerItemHandlingCost' => "",
                                'Local_ShoppingCartPrices' => "",
                                'Local_ProductQuantityOptions' => "",
                                'Local_ShoppingCartResultMessage' => "",
                                'Local_HideCheckoutLink' => "",
                                'Local_ThumbnailSide' => "",
        			'Local_ColorSwatchName'	=> "",
                                );

        $application->registerAttributes($_template_tags,'ShoppingCart');
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ShoppingCart');
        $this->templateFiller->setTemplate($this->template);

        $unit_values = '';
        $unit_id = '';
        if (NULL == $cc)
        {
            $retval = $this->templateFiller->fill("ContainerEmpty");
        }
        else
        {
            $retval = $this->templateFiller->fill("Container");
        }
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_FormAction':
                $request = new Request();
                $request->setView  ( 'CartContent' );
                $request->setAction  ( 'UpdateCartContent' );
                $value = $request->getURL();
                break;
            case 'Local_FormId':
                $value = "Product_Quan";
                break;
            case 'Local_FormActionFieldName':
                $value = 'asc_action';
                break;
            case 'Local_FormActionFieldValue':
                $value = 'UpdateCartContent';
                break;

        	case 'Local_Items':
        		$value = $this->getCartContent();
        		break;

            case 'Local_ClearLink':
                $request = new Request();
                $request->setView  ( 'CartContent' );
                $request->setAction( 'ClearCart' );
                $value = $request->getURL();
                break;

            case 'Local_ProductOptionsSelected':
                $value = getOptionsCombination($this->_Cart_Item['Options']);
                break;

            case 'Local_PriceName':
            {
            	if(isset($this->_Local_PriceName))
                {
                    return $this->_Local_PriceName;
                }
                else
                {
                	return "";
                }
            }
            case 'Local_PriceAmount':
            {
                if(isset($this->_Local_PriceAmount))
                {
                    return $this->_Local_PriceAmount;
                }
                else
                {
                    return "";
                }
            }
            case 'Local_ShoppingCartPrices':
            {
                $value = $this->outputShoppingCartPrices();
                break;
            }
            case 'Local_ProductQuantityOptions':
            {
                if ($this->_Cart_Item["TypeID"] == GC_PRODUCT_TYPE_ID) {
                    $value = '<option value="1">1</option>';
                } else {
                $qty_in_cart = $this->_Cart_Item['Quantity_In_Cart'];
                $value =modApiFunc("Cart", "getProductQuantityOptions", $qty_in_cart, $this->_Cart_Item['ID'], true);
                }
                break;
            }
            case 'Local_ShoppingCartResultMessage':
            {
                if($this->ShoppingCartResultMessage === NULL)
                {
                	$value = "";
                }
                else
                {
                    $value = $this->ShoppingCartResultMessage;
                }

            	break;
            }
            case 'Local_HideCheckoutLink':
                $value = $this -> hide_checkout_link;
                break;
            case 'Local_ThumbnailSide':
                $pi_settings = modApiFunc('Product_Images','getSettings');
                $value = $pi_settings['MAIN_IMAGE_SIDE'];
                break;

			case 'Local_ColorSwatchName':
					$colname = $this->_Cart_Item['Colorname'];
					$value = "";
					if (!($colname == ""))
					$value = "<div style='clear:both;float:left; padding-left: 10px;'>Chosen colors : </div><div style='font-weight:normal;float: left;'>&nbsp;$colname</div>";
				break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
                if ($entity == 'product' || $entity == 'unknown')
                {
                    $value = getKeyIgnoreCase($tag, $this->_Cart_Item)? getKeyIgnoreCase($tag, $this->_Cart_Item):null;
                }

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

    var $_Cart_Item;

    /**
     * A reference to the TemplateFiller object.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current specified template.
     *
     * @var array
     */
    var $template;

    var $hide_checkout_link;

    /**#@-*/

}
?>