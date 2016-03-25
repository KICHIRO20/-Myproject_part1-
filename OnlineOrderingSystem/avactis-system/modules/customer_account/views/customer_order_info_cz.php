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

class OrderInfo
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-order-info.ini'
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

    function OrderInfo()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;

        if ($application->issetBlockTagFatalErrors("OrderInfo"))
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
    }

    function out_OrderTaxes()
    {
        $html_code = '';

        foreach($this->order_info['Price']['tax_dops'] as $price_tag => $price_value)
        {
            $this->current_price_info = array(
                'name' => $price_value["name"]
               ,'display_name' => preg_replace("/:$/", '', $price_value["name"])
               ,'value' => modApiFunc('Localization','currency_format',$price_value["value"])
            );
            $html_code .= $this->templateFiller->fill('PriceItem');
        };

        return $html_code;
    }

    function out_OptionsList()
    {
        $html_code = '';

        foreach($this->current_product_info['options'] as $option_info)
        {
            $this->current_option_info = $option_info;
            $html_code .= $this->templateFiller->fill('ProductOptionsItem');
        };

        return $html_code;
    }

    function out_KeyDetails()
    {
        $html_code = '';

        foreach($this->key_details as $field_name)
        {
            $field_value = null;

            switch($field_name)
            {
                case 'Invoice':
                    $r = new Request();
                    $r->setView('CustomerOrderInvoice');
                    $r->setKey('order_id',$this->order_id);
                    $field_value = '<a target="_blank" href="'.$r->getURL().'">'.cz_getMsg('ORDER_INFO_KEY_FIELD_INVOICE_LINK').'</a>';
                    break;
                case 'Date':
                    $field_value = $this->__format_date($this->order_info[$field_name]);
                    break;
                case "TrackId":
                    $field_value = nl2br($this->order_info[$field_name]);
                    break;
                default:
                    $field_value = prepareHTMLDisplay($this->order_info[$field_name]);
                    break;
            };

            $this->current_info_field = array(
                    'name' => $field_name
                   ,'display_name' => cz_getMsg('ORDER_INFO_KEY_FIELD_'._ml_strtoupper($field_name))
                   ,'value' => $field_value);

            $html_code .= $this->templateFiller->fill('GroupField');
        };

        return $html_code;
    }

    function out_CustomerInfoGroup($group_name)
    {
        $html_code = '';

        if(array_key_exists($group_name, $this->order_info) and !empty($this->order_info[$group_name]['attr']))
        {
            foreach($this->order_info[$group_name]['attr'] as $field_info)
            {
                $this->current_info_field = array(
                    'name' => $field_info['tag']
                   ,'display_name' => $field_info['name']
                   ,'value' => $field_info['value']
                );

                $html_code .= $this->templateFiller->fill('GroupField');
            };
        };

        return $html_code;
    }

    function out_ProductsList()
    {
        $html_code = '';

        foreach($this->order_info['Products'] as $product_info)
        {
            $this->current_product_info = $product_info;
            $html_code .= $this->templateFiller->fill('OrderedProduct');
        };

        return $html_code;
    }

    function out_Prices()
    {
        $html_code = '';

        $prices = array(
            'Subtotal' => 'ORDER_PRICE_SUBTOTAL'
           ,'SubtotalGlobalDiscount' => 'ORDER_PRICE_GLOBAL_DISCOUNT'
           ,'SubtotalPromoCodeDiscount' => 'ORDER_PRICE_PROMO_CODE_DISCOUNT'
           ,'QuantityDiscount' => 'ORDER_PRICE_QUANTITY_DISCOUNT'
           ,'DiscountedSubtotal' => 'ORDER_PRICE_DISCOUNTED_SUBTOTAL'
           ,'TotalShippingAndHandlingCost' => 'ORDER_PRICE_SHIPPING_HANDLING'
           ,'Total' => 'ORDER_PRICE_TOTAL'
           ,'OrderTotalPrepaidByGC' => 'ORDER_TOTAL_PREPAID_BY_GC'
           ,'OrderTotalToPay' => 'ORDER_TOTAL_TO_PAY'
        );

        foreach($prices as $price_name => $price_lang_code)
        {
            $this->current_price_info = array(
                'name' => $price_name
               ,'display_name' => cz_getMsg($price_lang_code)
               ,'value' => null);

            switch($price_name)
            {
                case 'Subtotal':
                    $this->current_price_info['value'] = modApiFunc('Localization','currency_format',$this->order_info['Subtotal']);
                    break;
                case 'Total':
                    $html_code .= $this->out_OrderTaxes();
                    $this->current_price_info = array(
                        'name' => $price_name
                       ,'display_name' => cz_getMsg($price_lang_code)
                       ,'value' => modApiFunc('Localization','currency_format',$this->order_info['Total']));
                    break;
                default:
                    $this->current_price_info['value'] = modApiFunc('Localization','currency_format',$this->order_info['Price'][$price_name]);
                    break;
            };

            $html_code .= $this->templateFiller->fill('PriceItem');
        };

        return $html_code;
    }

    function out_Items()
    {
        $html_code = '';

        foreach($this->order_info_groups as $group_name => $group_lang_suffix)
        {
            $this->current_info_group = array('name' => $group_name, 'lang_suffix' => $group_lang_suffix);
            switch($group_name)
            {
                case 'key':
                    $html_code .= $this->templateFiller->fill('GroupHeader');
                    $html_code .= $this->out_KeyDetails();
                    break;
                case 'billing':
                case 'shipping':
                    if(modApiFunc('Customer_Account','isPersionInfoGroupActive',_ml_ucfirst($group_name)))
                    {
                        $html_code .= $this->templateFiller->fill('GroupHeader');
                        $html_code .= $this->out_CustomerInfoGroup(_ml_ucfirst($group_name));
                    };
                    break;
                case 'products':
                    $html_code .= $this->templateFiller->fill('GroupHeader');
                    $html_code .= $this->templateFiller->fill('ProductsContainer');
                    break;
            };
        };

        return $html_code;
    }

    function output($forced_order_id = null)
    {
        if($this->NoView)
        {
            return '';
        };

        $this -> forced_order_id = $forced_order_id;

        global $application;

        $_template_tags = array(
            'Local_Invoice',
            'Local_OrderDate',
            'Local_OrderStatus',
            'Local_OrderPaymentStatus',
            'Local_OrderPaymentMethod',
            'Local_OrderPaymentProcessorOrderId',
            'Local_OrderShippingMethod',
            'Local_OrderTrackId',
            'Local_OrderID',
            'Local_Items',
            'Local_GroupName',
            'Local_FieldName',
            'Local_FieldValue',
            'Local_OrderedProducts',
            'Local_OrderPrices',
            'Local_ProductID',
            'Local_ProductSKU',
            'Local_ProductName',
            'Local_ProductQuantity',
            'Local_ProductSalePrice',
            'Local_ProductListPrice',
            'Local_ProductManufacturer',
            'Local_ProductSmallImage',
            'Local_ProductLargeImage',
            'Local_ProductShortDescription',
            'Local_ProductDetailedDescription',
            'Local_ProductAmount',
            'Local_ProductOptions',
            'Local_OptionsList',
            'Local_OptionName',
            'Local_OptionValue',
            'Local_ProductFilesLink',
            'Local_PriceName',
            'Local_PriceValue',
            'Local_ProductList',
            'Local_ShippingCommentArea',
            'Local_ShippingCommentLine',
            'Local_BillingCommentArea',
            'Local_BillingCommentLine',
            'Local_ShippingFirstname',
            'Local_ShippingLastname',
            'Local_ShippingStreetline1',
            'Local_ShippingStreetline2',
            'Local_ShippingCity',
            'Local_ShippingCountry',
            'Local_ShippingState',
            'Local_ShippingPhone',
            'Local_ShippingEmail',
            'Local_BillingFirstname',
            'Local_BillingLastname',
            'Local_BillingStreetline1',
            'Local_BillingStreetline2',
            'Local_BillingCity',
            'Local_BillingCountry',
            'Local_BillingState',
            'Local_BillingPhone',
            'Local_BillingEmail',
            'Local_ForcedOrderID'
          );

        $_template_tags=apply_filters("avactis_customer_order_info_addAttributes",$_template_tags);

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OrderInfo');
        $this->templateFiller->setTemplate($this->template);

        if ($forced_order_id)
            $this -> order_id = $forced_order_id;

        if(($this->customer_obj != null && $this->order_id != null) || $forced_order_id)
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

        if(preg_match("/^local_(.+)$/i",$tag,$matches))
        {
            switch(strtolower($matches[1]))
            {
                case 'invoice':
                    $r = new Request();
                    $r->setView('CustomerOrderInvoice');
                    $r->setKey('order_id',$this->order_id);
                    $value = '<a target="_blank" href="'.$r->getURL().'">'.cz_getMsg('ORDER_INFO_KEY_FIELD_INVOICE_LINK').'</a>';
                    break;

                case "orderdate":
                    $value = $this->__format_date($this->order_info['Date']);
                    if (empty($value )) $value = '--';
                    break;

                case "orderstatus":
                    $value = $this->order_info['Status'];
                    if (empty($value )) $value = '--';
                    break;

                case "orderpaymentstatus":
                    $value = $this->order_info['PaymentStatus'];
                    if (empty($value )) $value = '--';
                    break;

                case "orderpaymentmethod":
                    $value = $this->order_info['PaymentMethod'];
                    if (empty($value )) $value = '--';
                    break;

                case "orderpaymentprocessororderid":
                    $value = $this->order_info['PaymentProcessorOrderId'];
                    if (empty($value )) $value = '--';
                    break;

                case "ordershippingmethod":
                    $value = $this->order_info['ShippingMethod'];
                    if (empty($value )) $value = '--';
                    break;

                case "ordertrackid":
                    $value = nl2br($this->order_info['TrackId']);
                    if (empty($value )) $value = '--';
                    break;

                case 'orderid':
                    $value = $this->order_info['ID'];
                    break;
                case 'items':
                    $value = $this->out_Items();
                    break;
                case 'groupname':
                    $value = cz_getMsg('ORDER_INFO_GROUP_'.$this->current_info_group['lang_suffix']);
                    break;
                case 'fieldname':
                    $value = $this->current_info_field['display_name'];
                    break;
                case 'fieldvalue':
                    $value = $this->current_info_field['value'];
                    break;
                case 'orderedproducts':
                    $value = $this->out_ProductsList();
                    break;
                case 'productlist':
                    $value = $this->templateFiller->fill('ProductsContainer');
                    break;
                case 'orderprices':
                    $value = $this->out_Prices();
                    break;
                case 'pricename':
                    $value = $this->current_price_info['display_name'];
                    break;
                case 'pricevalue':
                    $value = $this->current_price_info['value'];
                    break;
                case 'productid':
                    $value = $this->current_product_info['storeProductID'];
                    break;
                case 'productsku':
                    $value = array_key_exists('SKU',$this->current_product_info) ? $this->current_product_info['SKU'] : null;
                    break;
                case 'productname':
                    $value = $this->current_product_info['name'];
                    break;
                case 'productquantity':
                    $value = $this->current_product_info['qty'];
                    break;
                case 'productsaleprice':
                    $value = modApiFunc('Localization','currency_format',$this->current_product_info['SalePrice']);
                    break;
                case 'productlistprice':
                    $value = modApiFunc('Localization','currency_format',$this->current_product_info['ListPrice']);
                    break;
                case 'productmanufacturer':
                    $value = $this->current_product_info['Manufacturer'];
                    break;
                case 'productsmallimage':
                    $value = $this->current_product_info['SmallImage'];
                    break;
                case 'productlargeimage':
                    $value = $this->current_product_info['LargeImage'];
                    break;
                case 'productshortdescription':
                    $value = $this->current_product_info['ShortDescription'];
                    break;
                case 'productdetaileddescription':
                    $value = $this->current_product_info['DetailedDescription'];
                    break;
                case 'productamount':
                    $value = modApiFunc('Localization','currency_format',$this->current_product_info['SalePrice'] * $this->current_product_info['qty']);
                    break;
                case 'productoptions':
                    if(isset($this->current_product_info['options']) and !empty($this->current_product_info['options']))
                    {
                        $value = $this->templateFiller->fill('ProductOptionsContainer');
                    };
                    break;
                case 'optionslist':
                    $value = $this->out_OptionsList();
                    break;
                case 'optionname':
                    $value = $this->current_option_info['option_name'];
                    break;
                case 'optionvalue':
                    $value = $this->current_option_info['option_value'];
                    break;
                case 'productfileslink':
		    $orderInfo = modApiFunc("Checkout", "getBaseOrderInfo",$this->order_id);
		    if (($orderInfo["PaymentStatus"]=="Fully Paid") && count(modApiFunc('Product_Files','getHotlinksList',$this->current_product_info['id'])) > 0)
		    {
                        $r = new Request();
                        $r->setView('CustomerOrderDownloadLinks');
                        $r->setKey('order_id',$this->order_id);
                        $r->setKey('order_product_id',$this->current_product_info['id']);
                        $value = '<a href="'.$r->getURL().'">'.cz_getMsg('ORDERED_PRODUCT_FILES_LINK').'</a>';
                    };
                    break;
                case 'forcedorderid':
                    $value = $this -> forced_order_id;
                    break;
                default:
                    if(preg_match('/^product(.+)custom$/i',$matches[1],$m))
                    {
                        foreach($this->current_product_info['custom_attributes'] as $cattr_info)
                        {
                            if(strtolower($m[1]) == strtolower($cattr_info['tag']))
                            {
                                $value = $cattr_info['value'];
                            };
                        };
                    }
                    else
                    {
                        if (isset($this->direct_tags[$tag]))
                        {
                            $value = $this->direct_tags[$tag];
                        }
                        else
                        {
                            $value = '';
                        }
                    }
                    break;
            }
        }
        else if (preg_match('/^product(.+)custom$/i', $tag, $matches))
        {
            foreach($this->current_product_info['custom_attributes'] as $cattr_info)
            {
                if(_ml_strtolower($matches[1]) == _ml_strtolower($cattr_info['tag']))
                {
                    $value = $cattr_info['value'];
                };
            };
        };
        if($value=='')
        {
            do_action("customer_order_info",$this->order_id,strtolower($matches[1]));
            $value=modApiFunc('Session', 'get','plugin_return_action');
        }

        return $value;
    }

    function __format_date($date)
    {
        $arr = explode("-", array_shift(explode(' ', $date)));
        $ts = mktime(0,0,0,$arr[1],$arr[2],$arr[0]);
        return modApiFunc('Localization','timestamp_date_format',$ts);
    }

    var $customer_obj;
    var $order_id;
    var $order_info;

    var $order_info_groups = array(
        'key' => 'KEY_DETAILS'
       ,'products' => 'ORDERED_PRODUCTS'
       ,'billing' => 'BILLING_INFO'
       ,'shipping' => 'SHIPPING_INFO'
    );

    var $key_details = array(
            'ID','Date','Status','PaymentStatus','PaymentMethod','PaymentProcessorOrderId'
           ,'ShippingMethod','TrackId','Invoice'
        );

    var $current_info_group;
    var $current_info_field;
    var $current_product_info;
    var $current_option_info;
    var $current_price_info;
    var $direct_tags=array();
    var $custom_tags=array('billing'=>array(),'shipping'=>array());
    var $forced_order_id = null;
};

?>