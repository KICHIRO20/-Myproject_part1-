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

class OrderList
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-order-list.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
               ,'OrderItem' => TEMPLATE_FILE_SIMPLE
               ,'NoOrders' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OrderList()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("OrderList"))
        {
            $this->NoView = true;
        }

        $settings = modApiFunc('Customer_Account','getSettings');
        if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
        {
            $this->NoView = true;
        };

        loadCoreFile('html_form.php');

        $this->customer_obj = null;
        $this->incoming_filter = null;

        $email = modApiFunc('Customer_Account','getCurrentSignedCustomer');

        if($email !== null)
        {
            $this->customer_obj = &$application->getInstance('CCustomerInfo',$email);

            $request = new Request();
            $filter = $request->getValueByKey('filter');

            if($filter != null)
            {
                $orders_search_filter = null;

                if($filter == 'id')
                {
                    $orders_search_filter = array(
                        'type' => 'id'
                       ,'order_status' => ORDER_STATUS_ALL
                       ,'order_id' => intval($request->getValueByKey('order_id'))
                    );
                }
                elseif($filter != 'custom' and defined('ORDER_STATUS_'._ml_strtoupper($filter)))
                {
                    $orders_search_filter = array(
                        'type' => 'quick'
                       ,'order_status' => constant('ORDER_STATUS_'._ml_strtoupper($filter))
                    );
                }
                elseif($filter == 'custom')
                {
                    $orders_search_filter = array(
                        'type' => 'custom'
                       ,'order_status' => $request->getValueByKey('order_status')
                       ,'order_payment_status' => $request->getValueByKey('order_payment_status')
                       ,'day_from' => $request->getValueByKey('day_from')
                       ,'month_from' => $request->getValueByKey('month_from')
                       ,'year_from' => $request->getValueByKey('year_from')
                       ,'day_to' => $request->getValueByKey('day_to')
                       ,'month_to' => $request->getValueByKey('month_to')
                       ,'year_to' => $request->getValueByKey('year_to')
                    );
                };

                $this->incoming_filter = $orders_search_filter;
                $this->customer_obj->setOrdersHistoryFilter($this->incoming_filter);
            };
        };
    }

    function out_Items()
    {
        $html_code = '';

        foreach($this->customer_obj->getOrdersIDs() as $order_id)
        {
            $this->current_order_id = $order_id;
            $currency_id = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $this->current_order_id);
            modApiFunc("Localization", "pushDisplayCurrency", $currency_id, $currency_id);
            $html_code .= $this->templateFiller->fill('OrderItem');
            modApiFunc("Localization", "popDisplayCurrency");
        };

        return $html_code;
    }

    function out_FilterInfo()
    {
        $lang_suffix = 'custom';
        switch($this->incoming_filter['type'])
        {
            case 'quick':
                switch($this->incoming_filter['order_status'])
                {
                    case ORDER_STATUS_ALL:
                        $lang_suffix = 'all';
                        break;
                    case ORDER_STATUS_NEW:
                        $lang_suffix = 'new';
                        break;
                    case ORDER_STATUS_IN_PROGRESS:
                        $lang_suffix = 'in_progress';
                        break;
                    case ORDER_STATUS_READY_TO_SHIP:
                        $lang_suffix = 'ready_to_ship';
                        break;
                    case ORDER_STATUS_SHIPPED:
                        $lang_suffix = 'shipped';
                        break;
                    case ORDER_STATUS_CANCELLED:
                        $lang_suffix = 'cancelled';
                        break;
                    case ORDER_STATUS_DECLINED:
                        $lang_suffix = 'declined';
                        break;
                    case ORDER_STATUS_COMPLETED:
                        $lang_suffix = 'completed';
                        break;
                };
                break;
            case 'id':
                    $lang_suffix = 'by_id';
                break;
        };

        $filter_name = cz_getMsg('ORDER_STATUS_'._ml_strtoupper($lang_suffix));

        if($this->customer_obj->getOrdersCount() == 0)
            $lang_suffix = 'none';
        else
            $lang_suffix = 'summary';

        $currency_id = modApiFunc("Localization", "getMainStoreCurrency");
        modApiFunc("Localization", "pushDisplayCurrency", $currency_id, $currency_id);
        $ret = cz_getMsg('ORDER_FILTER_INFO_'._ml_strtoupper($lang_suffix)
                     ,$filter_name
                     ,$this->customer_obj->getOrdersCount()
                     ,$this->__format_date($this->customer_obj->getOrdersMinDate())
                     ,$this->__format_date($this->customer_obj->getOrdersMaxDate())
                     ,modApiFunc('Localization','currency_format',$this->customer_obj->getOrdersAmount())
                );
        modApiFunc("Localization", "popDisplayCurrency");
        return $ret;
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_FilterInfo'
           ,'Local_Items'
           ,'Local_OrderId'
           ,'Local_OrderDate'
           ,'Local_OrderAmount'
           ,'Local_OrderStatus'
           ,'Local_OrderPaymentStatus'
           ,'Local_OrderPaymentProcessorOrderId'
           ,'Local_OrderPaymentMethod'
           ,'Local_OrderShippingMethod'
           ,'Local_OrderTrackingNumber'
           ,'Local_OrderInfoLink'
           ,'Local_OrderInvoiceLink'
           ,'Local_OrderTax'
          );

       $_template_tags=apply_filters("avactis_customer_order_list_addAttributes",$_template_tags);

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OrderList');
        $this->templateFiller->setTemplate($this->template);

        if($this->customer_obj !== null)
        {
            return $this->templateFiller->fill('Container');
        }
        else
        {
            return $this->templateFiller->fill('AccessDenied');
        };
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_FilterInfo':
                $value = $this->out_FilterInfo();
                break;
            case 'Local_Items':
                $value = $this->customer_obj->getOrdersCount() > 0 ? $this->out_Items() : $this->templateFiller->fill('NoOrders');
                break;
            default:
                if(preg_match("/local_order(.+)$/i",$tag,$matches))
                {
                    $base_info = $this->customer_obj->getBaseOrderInfo($this->current_order_id);
                    switch(_ml_strtolower($matches[1]))
                    {
                        case 'id':
                            $value = modApiFunc('Checkout', 'outputOrderId', $this->current_order_id);
                            break;
                        case 'infolink':
                            $r = new Request();
                            $r->setView('CustomerOrderInfo');
                            $r->setKey('order_id', $this->current_order_id);
                            $value = $r->getURL();
                            break;
                        case 'invoicelink':
                            $order_status = getMsg('SYS','ORDER_STATUS_'.sprintf("%03d",$base_info['order_status_id']));
                            $payment_status = getMsg('SYS','ORDER_PAYMENT_STATUS_'.sprintf("%03d",$base_info['order_payment_status_id']));
                            if($payment_status == "Fully Paid")
                             {
                            $r = new Request();
                            $r->setView('CustomerOrderInvoice');
                            $r->setKey('order_id',$this->current_order_id);
                            $url = $r->getURL();
                            $value = "<a title='Order Info' target='_blank' href=\"$url\">HTML</a>" ;
                             }
                            else{$value = "----";}
                            break;
                        case 'amount':
                            $value = modApiFunc('Localization','currency_format',$base_info['order_total']);
                            break;
                        case 'date':
                            $value = $this->__format_date($base_info['order_date']);
                            break;
                        case 'status':
                            $value = getMsg('SYS','ORDER_STATUS_'.sprintf("%03d",$base_info['order_status_id']));
                            break;
                        case 'paymentstatus':
                            $value = getMsg('SYS','ORDER_PAYMENT_STATUS_'.sprintf("%03d",$base_info['order_payment_status_id']));
                            break;
                        case 'paymentprocessororderid':
                            $value = $base_info['order_payment_processor_order_id'];
                            break;
                        case 'paymentmethod':
                            $value = $base_info['order_payment_method'];
                            break;
                        case 'shippingmethod':
                            $value = $base_info['order_shipping_method'];
                            break;
                        case 'trackingnumber':
                            $value = $base_info['order_track_id'];
                            break;
                        case 'tax':
                            $value = modApiFunc('Localization','currency_format',$base_info['order_tax_total']);
                            break;

                       default:

                            do_action("customer_order_list",$this->current_order_id,_ml_strtolower($matches[1]));
                            $value=modApiFunc('Session', 'get','plugin_return_action');
                            break;
                    };
                };
        };

        return $value;
    }

    function __format_date($date)
    {
        if($date == null)
            return null;

        $arr = explode("-", array_shift(explode(' ', $date)));
        $ts = mktime(0,0,0,$arr[1],$arr[2],$arr[0]);
        return modApiFunc('Localization','timestamp_date_format',$ts);
    }

    var $customer_obj;
    var $current_order_id;
    var $incoming_filter;
};

?>