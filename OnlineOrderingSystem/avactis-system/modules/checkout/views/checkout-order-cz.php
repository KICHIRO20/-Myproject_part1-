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
 * Checkout
 *
 * @package Checkout
 * @author Girin Alexander
 */
class CheckoutOrder
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
            'layout-file'        => 'checkout-order-output-config.ini'
           ,'files' => array(
                'Container'      => TEMPLATE_FILE_SIMPLE
               ,'Item'           => TEMPLATE_FILE_PRODUCT_TYPE
               ,'ItemTax'        => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    /**
     *  CheckoutConfirmationInput constructor.
     */
    function CheckoutOrder()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutOrder"))#: add check for config errors
        {
            $this->NoView = true;
        }
    }


    function outputCartContent()
    {
        global $application;
        $cc = modApiFunc('Cart', 'getCartContent');

        /*           ,                                     4-   (success)      checkout.
         *                                          .
         *      4-                          .
         *                                         (         ),
         *             ,                                                  ,               .
         */
        $lastPlacedOrderID = modApiFunc("Checkout", "getLastPlacedOrderID");
        if($lastPlacedOrderID !== NULL && empty($cc))
        {
            $oi = modApiFunc("Checkout", "getOrderInfo", $lastPlacedOrderID, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $lastPlacedOrderID));
            $oi = $oi['Products'];
            $cc = array();
            foreach ($oi as $pi)
            {
                $pi['Quantity_In_Cart'] = $pi['qty'];
                $pi['Total'] = $pi['qty'] * $pi['SalePrice'];
                $pi['TotalIncludingTaxes'] = $pi['Total'];
                $pi['TotalExcludingTaxes'] = $pi['Total'];
                $pi['CartItemSalePrice'] = $pi['SalePrice'];
                $pi['CartItemSalePriceIncludingTaxes'] = $pi['CartItemSalePrice'];
                $pi['CartItemSalePriceExcludingTaxes'] = $pi['CartItemSalePrice'];
                $pi['CartItemWeight'] = $pi['Weight'];
                $pi['CartItemPerItemShippingCost'] = $pi['PerItemShippingCost'];
                $pi['CartItemPerItemHandlingCost'] = $pi['PerItemHandlingCost'];
                $pi['ID'] = $pi['storeProductID'];
                $pi['TypeID'] = $pi['type'];
                $pi['Options'] = $pi['options'];
				$pi['Colorname'] = $pi['Colorname'];

                $cc[] = $pi;
            }
            $this->order_mode_enabled = true;
        }

        $items = "";
        $this->_Cart_Item = null;
        $item_number = 1;
        foreach ($cc as $productInfo)
        {
            if($lastPlacedOrderID !== NULL) // revert currency convertation
            {
                $default_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
                $local_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getLocalDisplayCurrency"));
                $productInfo['Total'] = modApiFunc("Currency_Converter", "convert", $productInfo['Total'], $local_currency_code, $default_currency_code);
                $productInfo['CartItemSalePrice'] = modApiFunc("Currency_Converter", "convert", $productInfo['CartItemSalePrice'], $local_currency_code, $default_currency_code);
            }

            $productInfo['Local_CartItemNumber'] = $item_number;
            $productInfo['Local_ProductQuantity'] = $productInfo['Quantity_In_Cart'];
            $productInfo['Local_ProductSubtotal'] = modApiFunc("Localization", "currency_format", $productInfo['Total']);
            $productInfo['Local_ProductSubtotalIncludingTaxes'] = modApiFunc("Localization", "currency_format", $productInfo['TotalIncludingTaxes']);
            $productInfo['Local_ProductSubtotalExcludingTaxes'] = modApiFunc("Localization", "currency_format", $productInfo['TotalExcludingTaxes']);
            $productInfo['Local_CartItemSalePrice']=modApiFunc("Localization", "currency_format", $productInfo['CartItemSalePrice']);
            $productInfo['Local_CartItemSalePriceIncludingTaxes']=modApiFunc("Localization", "currency_format", $productInfo['CartItemSalePriceIncludingTaxes']);
            $productInfo['Local_CartItemSalePriceExcludingTaxes']=modApiFunc("Localization", "currency_format", $productInfo['CartItemSalePriceExcludingTaxes']);
            $productInfo['Local_CartItemWeight']=modApiFunc("Localization", "format", $productInfo['CartItemWeight'],"weight")." ".modApiFunc("Localization","getUnitTypeValue","weight");
            $productInfo['Local_CartItemPerItemShippingCost']=modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemShippingCost']);
            $productInfo['Local_CartItemPerItemHandlingCost']=modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemHandlingCost']);

            $this->_Cart_Item = $productInfo;
            $productInfo['SubTotal'] = "";
            $productInfo['ClearCart_Link'] = "";
            $application->registerAttributes($productInfo);

            modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $productInfo['ID'])));
            $items .= $this->templateFiller->fill("Item", $productInfo['TypeID']);
            modApiFunc("tag_param_stack", "pop", __CLASS__);

            $item_number++;
        }
        $this->_Cart_Item = null; // clear the loop variable
        return $items;
    }

    function outputTax()
    {
        global $application;
        $retval ="";
        $this->_Tax_Item = array();
        $tax = modApiFunc("Taxes", "getTax");
        //                                             -
        switch(modApiFunc("TaxExempts", "getFullTaxExemptStatus"))
        {
            case DB_TRUE:
                $tax['TaxSubtotalAmountView'][] = array
                (
                    'view' => cz_getMsg("FULL_TAX_EXEMPT_MSG")
                   ,'value'=> -1 * modApiFunc("Checkout", "getOrderPrice", "Tax_NoExempts", modApiFunc("Localization", "getMainStoreCurrency"))
                );
                break;
            default:
                break;
        }
        $lastPlacedOrderID = modApiFunc('Checkout', 'getLastPlacedOrderID');
        if ($lastPlacedOrderID !== NULL)
        {
            $oi = modApiFunc('Checkout', 'getOrderInfo', $lastPlacedOrderID,
                             modApiFunc('Localization',
                                        'whichCurrencyToDisplayOrderIn',
                                        $lastPlacedOrderID));
            $tax['TaxSubtotalAmountView'] = array();
            foreach($oi['Price']['tax_dops'] as $tax_info)
                $tax['TaxSubtotalAmountView'][] = array(
                    'view' => preg_replace("/:$/", '', $tax_info["name"]),
                    'value' => $tax_info['value']
                );
        }
        foreach ($tax['TaxSubtotalAmountView'] as $taxView)
        {
            $dont_convert = modApiFunc('Checkout','getCurrentStepID') == 4 ? true : false;
            $this->_Tax_Item['Local_TaxName'] = prepareHTMLDisplay($taxView['view']);
            $this->_Tax_Item['Local_TaxAmount'] = modApiFunc("Localization", "currency_format", $taxView['value'], $dont_convert);
            $application->registerAttributes($this->_Tax_Item);
            $retval .= $this->templateFiller->fill("ItemTax");
        }
        return $retval;
    }

    /**
     * Otputs the view.
     *
     * @ $request->setView  ( '' ) - define the view name
     */
    function output()
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CheckoutConfirmationInput", "Errors");
            return "";
        }

        $application->registerAttributes(array('Local_Subtotal',
                                               'Local_GlobalDiscount',
                                               'Local_PromoCodeDiscount',
                                               'Local_QuantityDiscount',
                                               'Local_ShippingCost', // = Local_TotalShippingAndHandlingCost

                                               'Local_CartItemSalePrice',
                                               'Local_CartItemWeight',
                                               'Local_CartItemPerItemShippingCost',
                                               'Local_CartItemPerItemHandlingCost',

                                               'Local_ProductOptionsSelected',

                                               'Local_FreeHandlingForOrdersOver',
                                               'Local_FreeShippingForOrdersOver',
                                               'Local_MinimumShippingCost',

                                               'Local_PerItemShippingCostSum',
                                               'Local_PerOrderShippingFee',
                                               'Local_ShippingMethodName',
                                               'Local_ShippingMethodCost',
                                               'Local_TotalShippingCharge',
                                               'Local_PerItemHandlingCostSum',
                                               'Local_PerOrderHandlingFee',
                                               'Local_TotalHandlingCharge',
                                               'Local_TotalShippingAndHandlingCost',

                                               'Local_Taxes',
                                               'Local_Total',
                                               'Local_GiftCertificatePrepaidAmount',
                                               'Local_TotalToPay',
												'Local_SwatchColorSelected'
                                               ));

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('CheckoutOrder');
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("Container");

        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        $curr_id = modApiFunc("Localization", "getMainStoreCurrency");
        switch ($tag)
        {
            case 'Local_Items':
                $value = $this->outputCartContent();
                break;
            case 'Local_ProductOptionsSelected':
                $value = getOptionsCombination($this->_Cart_Item['Options'],'Order', $this->order_mode_enabled);
                break;
            case 'Local_Subtotal':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "Subtotal", $curr_id));
                break;
            case 'Local_GlobalDiscount':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "SubtotalGlobalDiscount", $curr_id));
                break;
            case 'Local_PromoCodeDiscount':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "SubtotalPromoCodeDiscount", $curr_id));
                break;
            case 'Local_QuantityDiscount':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "QuantityDiscount", $curr_id));
                break;
            case 'Local_ShippingCost':
            case 'Local_TotalShippingAndHandlingCost':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "TotalShippingAndHandlingCost", $curr_id));
                break;
            case 'Local_FreeHandlingForOrdersOver':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "FreeHandlingForOrdersOver", $curr_id));
                break;
            case 'Local_PerItemShippingCostSum':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "PerItemShippingCostSum", $curr_id));
                break;
            case 'Local_ShippingMethodCost':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "ShippingMethodCost", $curr_id));
                break;
            case 'Local_FreeShippingForOrdersOver':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "FreeShippingForOrdersOver", $curr_id));
                break;
            case 'Local_MinimumShippingCost':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "MinimumShippingCost", $curr_id));
                break;
            case 'Local_PerOrderShippingFee':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "PerOrderShippingFee", $curr_id));
                break;
            case 'Local_TotalShippingCharge':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "TotalShippingCharge", $curr_id));
                break;
            case 'Local_ShippingMethodName':
                $shipping_module_id = modApiFunc("Checkout","getChosenShippingModuleIdCZ");
                if($shipping_module_id === NULL)
                {
                    //                               ,
                    return '---';
                }
                $shipping_method_id = modApiFunc("Checkout","getChosenShippingMethodIdCZ");
                $shipping_module_info = modApiFunc("Checkout","getShippingModuleInfo",$shipping_module_id);

                if($shipping_module_info["GlobalUniqueShippingModuleID"]=="6F82BA03-C5B1-585B-CE2E-B8422A1A19F6")
                {
                    $mRes = &$application->getInstance('MessageResources',"messages");
                    $value=$mRes->getMessage('ALL_SM_ARE_INACTIVE');
                    unset($mRes);
                }
                else
                {
                    $ShippingMethodInfo=modApiFunc("Shipping_Cost_Calculator","getCalculatedMethod",$shipping_module_info["APIClassName"],$shipping_method_id);
                    $value = $ShippingMethodInfo['method_name'];
                }
                break;
            case 'Local_PerItemHandlingCostSum':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "PerItemHandlingCostSum", $curr_id));
                break;
            case 'Local_PerOrderHandlingFee':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "PerOrderHandlingFee", $curr_id));
                break;
            case 'Local_TotalHandlingCharge':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "TotalHandlingCharge", $curr_id));
                break;
            case 'Local_Taxes':
                $value = $this->outputTax();
                break;
            case 'Local_Total':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "Total", $curr_id));
                break;
            case 'Local_GiftCertificatePrepaidAmount':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "TotalPrepaidByGC", $curr_id));
                break;
            case 'Local_TotalToPay':
                $value = modApiFunc("Localization", "currency_format", modApiFunc("Checkout", "getOrderPrice", "TotalToPay", $curr_id));
                break;

			case 'Local_SwatchColorSelected':
				$colname =$this->_Cart_Item['Colorname'];
				$value="";
				if (!($colname == ""))
				$value = "<div style='clear:both;float:left; padding-left: 10px;'>Chosen colors : </div><div style='font-weight:normal;float: left;'>&nbsp;$colname</div>";
			    break;

            default:
                list($entity, $tag) = getTagName($tag);
                if ($entity == 'product' || $entity == 'unknown')
                {
                    //                        "                             Subtotal",
                    //                                                    .
                    if(getKeyIgnoreCase($tag, $this->_Cart_Item))
                    {
                        $value = getKeyIgnoreCase($tag, $this->_Cart_Item);
                    }
                    else
                    {
                        $value = (isset($this->_Tax_Item) ? getKeyIgnoreCase($tag, $this->_Tax_Item) : null);
                        if ($value == null && $entity == 'product')
                        {
                            $po = new CProductInfo($this->_Cart_Item['ID']);
                            if ($tag == "infolink" && $this->_Cart_Item['TypeID'] == GC_PRODUCT_TYPE_ID)
                            {
                                $request = new Request();
                                $request->setView("GiftCertificate");
                                return $request->getURL();
                            }
                            $value = $po->getProductTagValue($tag);
                        }
                    }
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

    /**
     * Reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     *
     * @var array
     */
    var $template;

    /**
     * Payment Module data for online processing.
     */
    var $paymentProcessingData = NULL;

    var $order_mode_enabled = false;

    /**#@-*/
}
?>