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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

loadModuleFile('customer_account/views/customer_order_info_cz.php');

class OrderInvoice extends OrderInfo
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-order-invoice.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
               ,'GroupHeader' => TEMPLATE_FILE_SIMPLE
               ,'GroupField' => TEMPLATE_FILE_SIMPLE
               ,'ProductsContainer' => TEMPLATE_FILE_SIMPLE
               ,'OrderedProduct' => TEMPLATE_FILE_SIMPLE
               ,'PriceItem' => TEMPLATE_FILE_SIMPLE
               ,'ProductOptionsContainer' => TEMPLATE_FILE_SIMPLE
               ,'ProductOptionsItem' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OrderInvoice()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("OrderInvoice"))
        {
            $this->NoView = true;
        }

        $settings = modApiFunc('Customer_Account','getSettings');
        if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
        {
            $this->NoView = true;
        };

        $this->customer_obj = null;
        $this->order_id = null;

        $email = modApiFunc('Customer_Account','getCurrentSignedCustomer');

        if($email != null)
        {
            $this->customer_obj = &$application->getInstance('CCustomerInfo',$email);

            $request = new Request();
            $this->order_id = $request->getValueByKey('order_id');

            $q_filter = array(
                'type' => 'quick'
               ,'order_status' => ORDER_STATUS_ALL
            );

            $this->customer_obj->setOrdersHistoryFilter($q_filter);

            if(!in_array($this->order_id, $this->customer_obj->getOrdersIDs()))
            {
                $this->order_id = null;
            };
        };

        $this->order_info_groups = array();

        $this->key_details = array(
                'ID','Date','Status','PaymentStatus','PaymentMethod','PaymentProcessorOrderId'
               ,'ShippingMethod','TrackId'
            );

    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            "Local_Invoice",
            "Local_OrderDate",
            "Local_OrderStatus",
            "Local_OrderPaymentStatus",
            "Local_OrderPaymentMethod",
            "Local_OrderPaymentProcessorOrderId",
            "Local_OrderShippingMethod",
            "Local_OrderTrackId",
            'Local_OrderID'
           ,'Local_GroupName'
           ,'Local_FieldName'
           ,'Local_FieldValue'
           ,'Local_OrderedProducts'
           ,'Local_OrderPrices'
           ,'Local_ProductID'
           ,'Local_ProductSKU'
           ,'Local_ProductName'
           ,'Local_ProductQuantity'
           ,'Local_ProductSalePrice'
           ,'Local_ProductAmount'
           ,'Local_ProductOptions'
           ,'Local_OptionsList'
           ,'Local_OptionName'
           ,'Local_OptionValue'
           ,'Local_ProductFilesLink'
           ,'Local_PriceName'
           ,'Local_PriceValue'
           ,'Local_PersonInfoShipping'
           ,'Local_PersonInfoBilling'
           ,'Local_KeyDetails'
           ,'Local_ProductsDetails'
           ,'Local_ProductList'
           ,'Local_ShippingCommentArea'
           ,'Local_ShippingCommentLine'
           ,'Local_ShippingFirstname'
           ,'Local_ShippingLastname'
           ,'Local_ShippingStreetline1'
           ,'Local_ShippingStreetline2'
           ,'Local_ShippingCity'
           ,'Local_ShippingState'
           ,'Local_ShippingPostcode'
           ,'Local_ShippingCountry'
           ,'Local_ShippingPhone'
           ,'Local_ShippingEmail'

           ,'Local_BillingCommentArea'
           ,'Local_BillingCommentLine'
           ,'Local_BillingFirstname'
           ,'Local_BillingLastname'
           ,'Local_BillingStreetline1'
           ,'Local_BillingStreetline2'
           ,'Local_BillingCity'
           ,'Local_BillingState'
           ,'Local_BillingPostcode'
           ,'Local_BillingCountry'
           ,'Local_BillingPhone'
           ,'Local_BillingEmail'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OrderInvoice');
        $this->templateFiller->setTemplate($this->template);

        if($this->customer_obj != null and $this->order_id != null)
        {
            $currency_id = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $this->order_id);
            modApiFunc("Localization", "pushDisplayCurrency", $currency_id, $currency_id);
            $this->order_info = modApiFunc('Checkout','getOrderInfo',$this->order_id, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $this->order_id));

            $this->direct_tags = array();
            $this->custom_tags = array('billing'=>array(),'shipping'=>array());
            foreach ($this->order_info['Billing']['attr'] as $attr)
            {
                if (strpos($attr['tag'],'CUSTOM')===0)
                {
                    $this->custom_tags['billing'][] = array('name'=>$attr['name'], 'value'=>$attr['value']);
                }
                else
                {
                    $this->direct_tags['Local_Billing'.$attr['tag']] = $attr['value'];
                }
            }
            foreach ($this->order_info['Shipping']['attr'] as $attr)
            {
                if (strpos($attr['tag'],'CUSTOM')===0)
                {
                    $this->custom_tags['shipping'][] = array('name'=>$attr['name'], 'value'=>$attr['value']);
                }
                else
                {
                    $this->direct_tags['Local_Shipping'.$attr['tag']] = $attr['value'];
                }
            }
            $application->registerAttributes($this->direct_tags);

            // register all custom attributes as tags
            $_tags = array();
            foreach($this->order_info['Products'] as $product)
            {
                foreach($product['custom_attributes'] as $custom_attr_info)
                {
                    $_tags[] = 'Local_Product'.$custom_attr_info['tag'].'Custom';
                };
            };
            $application->registerAttributes($_tags);

            $ret = $this->templateFiller->fill('Container');
            modApiFunc("Localization", "popDisplayCurrency");
            return $ret;
        }
        else
        {
            return $this->templateFiller->fill('AccessDenied');
        };
    }

    function getBillingCustomTags()
    {
        return $this->custom_tags['billing'];
    }

    function getShippingCustomTags()
    {
        return $this->custom_tags['shipping'];
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_PersonInfoShipping':
            case 'Local_PersonInfoBilling':
                $group_name = _ml_strtolower(str_replace('Local_PersonInfo','',$tag));
                $lang_suffix = _ml_strtoupper($group_name.'_info');
                $this->current_info_group = array('name' => $group_name, 'lang_suffix' => $lang_suffix);
                $value = $this->templateFiller->fill('GroupHeader');
                $value .= $this->out_CustomerInfoGroup(_ml_ucfirst($group_name));
                break;
            case 'Local_KeyDetails':
                $this->current_info_group = array('name' => 'key', 'lang_suffix' => 'KEY_DETAILS');
                $value = $this->templateFiller->fill('GroupHeader');
                $value .= $this->out_KeyDetails();
                break;
            case 'Local_ProductsDetails':
                $this->current_info_group = array('name' => 'products', 'lang_suffix' => 'ORDERED_PRODUCTS');
                $value = $this->templateFiller->fill('GroupHeader');
                $value .= $this->templateFiller->fill('ProductsContainer');
                break;
            default:
                $value = parent::getTag($tag);
                break;
        };

        return $value;
    }
};

?>