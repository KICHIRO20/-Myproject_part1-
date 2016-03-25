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

class ManageOrders
{

    function ManageOrders()
    {
            modApiFunc('paginator', 'setCurrentPaginatorName', "Checkout_Orders");
            $this->_orders = modApiFunc('Checkout', 'getOrderList');
            $this->_fetched_orders = $this->__fetch_base_orders_info($this->_orders);
    }

    /**
     * The main function to output the given view.
     */
    function output()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $this->_filter = modApiFunc('Checkout', 'getOrderSearchFilter');
        $application->registerAttributes(array(
            'SearchOrders'
           ,'SearchStatusSelector'
           ,'SearchPaymentStatusSelector'
           ,'SearchResults'
           ,'SearchBy'
           ,'CountByStatus0'
           ,'CountByStatus1'
           ,'CountByStatus2'
           ,'CountByStatus3'
           ,'ResultCount'
           ,'ResultDateRange'
           ,'ResultAmount'
           ,'ResultTaxTotal'
           ,'ResultFullTaxExempt'
           ,'ResultTaxTotalMinusFullTaxExempt'
           ,'OrderId'
           ,'OrderIdInt'
           ,'OrderPersonId'
           ,'OrderPersonCustomerId'
           ,'OrderCustomerName'
           ,'OrderCustomerInfoName'
           ,'OrderCustomerID'
           ,'OrderDate'
           ,'OrderPriceTotal'
           ,"OrderPriceTaxes"
           ,'OrderStatus'
           ,'OrderIdLinkTitle'
           ,'OrderCustomerNameLinkTitle'
           ,'OrderStatusSelector'
           ,'OrderStatusSelectorItems'
           ,'StatusId'
           ,'StatusName'
           ,'StatusSelected'
           ,'OrderPaymentStatus'
           ,'OrderPaymentStatusSelector'
           ,'OrderPaymentStatusSelectorItems'
           ,'PaymentStatusId'
           ,'PaymentStatusName'
           ,'PaymentStatusSelected'
           ,'HighLightNewOrders'
           ,'HighLightInProgress'
           ,'HighLightReadyToShip'
           ,'HighLightAll'
           ,'HighLightDate'
           ,'HighLightDateOrderPaymentStatus'
           ,'HighLightDateOrderStatus'
           ,'HighLightOrderId'
           ,'HighLightAffiliateId'
           ,'SearchingOrderId'
           ,'SearchFromDaySelector'
           ,'SearchFromMonthSelector'
           ,'SearchFromYearSelector'
           ,'SearchToDaySelector'
           ,'SearchToMonthSelector'
           ,'SearchToYearSelector'
           ,'SimpleSelectorOption'
           ,'SimpleCheckBoxGroup_Orders'
           ,'SimpleCheckBoxGroup_Payments'
           ,'DeleteOrdersLink'
           ,'PaginatorLine'
           ,'PaginatorRows'
           ,"ResultMessageRow"
           ,"ResultMessage"
           ,'PackingSlipLink'
           ,'InvoiceLink'
           ,'AffiliateIDSearch'
        ));

        return $this->TemplateFiller->fill("checkout/orders/", "container.tpl.html", array());
    }

    /**
     * Returns order_id, order_date, list<price_total, currency_code, currency_type> for each order
     */
    function __fetch_base_orders_info($order_ids)
    {
        global $application;
        if(empty($order_ids))
        {
            return array();
        }
        else
        {
            $res = execQuery('SELECT_BASE_ORDERS_INFO', array("order_ids" => $order_ids));

            $orders = array();
            foreach($res as $row)
            {
                if(!isset($orders[$row['order_id']]))
                {
                    $orders[$row['order_id']] = array
                    (
                        "order_id"    =>  $row['order_id']
                       ,"order_date"  =>  $row['order_date']
                       ,"payment_status_id" => $row['payment_status_id']
                       ,"person_id"   =>  $row['person_id']
                       ,"status_id"   =>  $row['status_id']
                       ,"price_total" => array()
                    );
                }
                $orders[$row['order_id']]["price_total"][$row['currency_code']] = array
                (
                    "order_total"   => $row['order_total']
                   ,"order_tax_total"   => $row['order_tax_total']
                   ,"currency_code" => $row['currency_code']
                   ,"currency_type" => $row['currency_type']
                );
            }

            //convert currency data to checkout format
            foreach($orders as $order_id => $info)
            {
                $info =& $orders[$order_id];
                $order_currencies = array();
                foreach($info['price_total'] as $price_info)
                {
                    $order_currencies[] = array
                    (
                        'currency_type' => $price_info['currency_type']
                       ,'currency_code' => $price_info['currency_code']
                    );
                }
                $info['order_currencies_list'] = modApiFunc("Checkout", "getOrderCurrencyList", $order_id, $order_currencies);
                unset($info);
            }

            return $orders;
        }
    }

    function __getFullTaxExemptOrders()
    {
        $res = modApiFunc("TaxExempts", "getOrderFullTaxExempts");
        $value = array();
        //                :
        foreach($res as $order)
        {
            $value[$order['order_id']] = $order;
        }
        return $value;
    }

    function __getTaxSummary()
    {
        $ResultTaxTotal = 0.0;
        $ResultFullTaxExempt = 0.0;
        $ResultTaxTotalMinusFullTaxExempt = 0.0;

        if (count($this->_orders) == 0)
        {
            return array
            (
                "ResultTaxTotal" => modApiFunc("Localization", "currency_format", $ResultTaxTotal)
               ,"ResultFullTaxExempt" => modApiFunc("Localization", "currency_format", $ResultFullTaxExempt)
               ,"ResultTaxTotalMinusFullTaxExempt" => modApiFunc("Localization", "currency_format", $ResultTaxTotalMinusFullTaxExempt)
            );
        }

        $tax_total = 0;
        $all_orders_are_in_main_currency = true;
        $main_store_currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
        $full_tax_exempt_orders = $this->__getFullTaxExemptOrders();
        foreach ($this->_orders as $order_id)
        {
            //           order_tax_total   main_store_currency       .
            //           order'                      ,                                 default ( . .
            //       main_store_currency                 ).
            //     default currency                                                 main_store_currency,
            //                                               ,            .          order_total
            //                                          .
            $order_default_currency = modApiFunc("Localization", "getOrderMainCurrency", $order_id, $this->_fetched_orders[$order_id]['order_currencies_list']);

            $order_tax_total_in_order_default_currency = $this->_fetched_orders[$order_id]['price_total'][$order_default_currency]['order_tax_total'];

            $tax_dbg = array(
                 "oid" => $order_id
                ,"order_df_crcy" => $order_default_currency
                ,"tax_in_df_crcy" => $order_tax_total_in_order_default_currency
            );

            if($order_tax_total_in_order_default_currency != PRICE_N_A)
            {
                if($order_default_currency == $main_store_currency)
                {
                    $total = $order_tax_total_in_order_default_currency;
                }
                else
                {
                    $all_orders_are_in_main_currency = false;

                    $total = modApiFunc('Currency_Converter','convert', $order_tax_total_in_order_default_currency, $order_default_currency, $main_store_currency);
//                    $ResultTaxTotal += $total;

                    $tax_dbg["order_main_crcy"] = $main_store_currency;
                    $tax_dbg["tax_in_mn_crcy"] = $total;
                }
                $ResultTaxTotal += $total;

                //                                                                       :
                if(array_key_exists($order_id, $full_tax_exempt_orders))
                {
                    $ResultFullTaxExempt += $total;
                    $tax_dbg["tax_exemtion"] = "true";
                }
                $tax_dbg["tax_total_now"] = $ResultTaxTotal;
                $tax_dbg["tax_exempt_now"] = $ResultFullTaxExempt;
                $this->tax_debug[] = $tax_dbg;
            }
        }
        $main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");
        modApiFunc("Localization", "pushDisplayCurrency", $main_store_currency_id, $main_store_currency_id);

        $ResultTaxTotalMinusFullTaxExempt = $ResultTaxTotal - $ResultFullTaxExempt;
        if($ResultTaxTotalMinusFullTaxExempt < 0.0)
        {
            $ResultTaxTotalMinusFullTaxExempt = 0.0;
        }
        $ResultTaxTotal = modApiFunc("Localization", "currency_format", $ResultTaxTotal);
        $ResultFullTaxExempt = modApiFunc("Localization", "currency_format", -1 * $ResultFullTaxExempt);
        $ResultTaxTotalMinusFullTaxExempt = modApiFunc("Localization", "currency_format", $ResultTaxTotalMinusFullTaxExempt);

        modApiFunc("Localization", "popDisplayCurrency");

        if($all_orders_are_in_main_currency == false)
        {
            $ResultTaxTotal = "~".$ResultTaxTotal;
            $ResultFullTaxExempt = "~".$ResultFullTaxExempt;
            $ResultTaxTotalMinusFullTaxExempt = "~".$ResultTaxTotalMinusFullTaxExempt;
        }

        return array
        (
            "ResultTaxTotal" => $ResultTaxTotal
           ,"ResultFullTaxExempt" => $ResultFullTaxExempt
           ,"ResultTaxTotalMinusFullTaxExempt" => $ResultTaxTotalMinusFullTaxExempt
        );
    }

    /**
     * Views an order list.
     */
    function getOrders()
    {
        $result = "";

        //SQL queries number optimization
        $orders =& $this->_fetched_orders;

        //                           :
        $customer_ids = array();
        foreach($orders as $order_info)
        {
            $customer_ids[] = $order_info['person_id'];
        }
        $customer_names = modApiFunc("Customer_Account", "__fetch_customer_names", $customer_ids);
        $customer_statuses = modApiFunc("Customer_Account", "__fetch_customer_statuses", $customer_ids);
        $customer_accounts = modApiFunc("Customer_Account", "__fetch_customer_accounts", $customer_ids);

        //                      2008.06.                 "main store currency"   "customer selected"       .
        //  "Main store currency"            . "Customer selected" -
        //        ,             "main store currency" (                ).
        //              -                                  -                             .
        //                   -           ,                                            ,
        //                     -                          .
        $last_pushed_currency_id = null;
        foreach ($this->_orders as $order_id)
        {
            //            ,                                                            ,                                      .
            //                                :
            //$order_currency_id = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id, $orders[$order_id]['order_currencies_list']);
            //$order_currency_code = modApiFunc("Localization", "getCurrencyCodeById", $order_currency_id);

            //                                                 -          ,                                               . .
            //                     CUSTOMER_SELECTED
            $order_currency_code = $orders[$order_id]['order_currencies_list'][CURRENCY_TYPE_CUSTOMER_SELECTED]['currency_code'];
            $order_currency_id =  modApiFunc("Localization", "getCurrencyIdByCode", $order_currency_code);
            $order_total = $orders[$order_id]['price_total'][$order_currency_code]['order_total'];

            if($last_pushed_currency_id !== null &&
               $last_pushed_currency_id != $order_currency_id)
            {
                modApiFunc("Localization", "popDisplayCurrency");
                modApiFunc("Localization", "pushDisplayCurrency", $order_currency_id, $order_currency_id);
                $last_pushed_currency_id = $order_currency_id;
            }
            elseif($last_pushed_currency_id === null)
            {
                modApiFunc("Localization", "pushDisplayCurrency", $order_currency_id, $order_currency_id);
                $last_pushed_currency_id = $order_currency_id;
            }
            else
            {
                //                              :                                  .
            }

            $order = array();
            $order["Date"] = modApiFunc("Localization", "SQL_date_format", $orders[$order_id]['order_date']);
            $order["Total"] = modApiFunc("Localization", "currency_format", $order_total);
            $order["IdInt"] = (int)$order_id;
            $order["Id"] = modApiFunc("Checkout", "outputOrderId", $order_id);
            $order["PaymentStatusId"] = $orders[$order_id]['payment_status_id'];
            $order["StatusId"] = $orders[$order_id]['status_id'];
            $order["PersonId"] = $orders[$order_id]['person_id'];
            loadClass('CCustomerInfo');
            $order["PersonName"] = isset($customer_accounts[$orders[$order_id]['person_id']]) ?  CCustomerInfo::getDisplayAccountNameExt($customer_accounts[$orders[$order_id]['person_id']], $customer_statuses[$orders[$order_id]['person_id']]) : "";
            $order["PersonInfoName"] = isset($customer_names[$orders[$order_id]['person_id']]) ? htmlspecialchars($customer_names[$orders[$order_id]['person_id']]) : 'N/A';
            $order['price_total'] = $orders[$order_id]['price_total'];
            $order['TotalInMainStoreCurrency'] = NULL;

            //                                              "customer selected"
            //  "main store currency" -                              .
            if($orders[$order_id]['order_currencies_list'][CURRENCY_TYPE_MAIN_STORE_CURRENCY]['currency_code'] !=
               $orders[$order_id]['order_currencies_list'][CURRENCY_TYPE_CUSTOMER_SELECTED]['currency_code'])
            {
                $_currency_code = $orders[$order_id]['order_currencies_list'][CURRENCY_TYPE_MAIN_STORE_CURRENCY]['currency_code'];
                $_currency_id = modApiFunc("Localization", "getCurrencyIdByCode", $_currency_code);
                $_total = $orders[$order_id]['price_total'][$_currency_code]['order_total'];
                //                                                (              ),
                //        .
                modApiFunc("Localization", "pushDisplayCurrency", $_currency_id, $_currency_id);
                $order['TotalInMainStoreCurrency'] = modApiFunc("Localization", "currency_format", $_total);
                modApiFunc("Localization", "popDisplayCurrency");
            }

            $this->_order = $order;
            $result .= modApiFunc('TmplFiller', 'fill', "checkout/orders/", "item_result.tpl.html", array());
        }
        modApiFunc("Localization", "popDisplayCurrency");
        $this->_order = null;
        return $result;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            if ($msg == 'MSG_GNRL_SET_UPDATED')
                return '';
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("checkout/orders/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }



    /**
     * @ describe the function ManageOrders->.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'CountByStatus0':
                $value = modApiFunc('Checkout', 'getOrderCount', 0);
                break;

            case 'CountByStatus1':
                $value = modApiFunc('Checkout', 'getOrderCount', 1);
                break;

            case 'CountByStatus2':
                $value = modApiFunc('Checkout', 'getOrderCount', 2);
                break;

            case 'CountByStatus3':
                $value = modApiFunc('Checkout', 'getOrderCount', 3);
                break;

            case 'SearchStatusSelector':
            	$this->_simple_selector['options'] = array();
                $status_array = modApiFunc('Checkout', 'getOrderStatusList');

                if (isset($this->_filter['status_id']) && ($this->_filter['status_id'] != ""))
            	{
                    $this->_simple_selector['selected'] = $this->_filter['status_id'];
            	}

                foreach ($status_array as $status)
                {
                    $sel = 0;

                	if (isset($this->_filter['order_statuses']) && is_array($this->_filter['order_statuses']) && isset($this->_filter['order_statuses'][$status['id']]))
            	    {
            	    	$sel = 1;
            	    }
                	$this->_simple_selector['options'][] = array('value'=>$status['id'], 'name'=>$status['name'],'selected'=>$sel);
                }

                $value = $this->TemplateFiller->fill("checkout/orders/", "search-status-selector.tpl.html", array());
                break;

            case 'SearchPaymentStatusSelector':
            	$this->_simple_selector['options'] = array();
                if (isset($this->_filter['payment_status_id']) && ($this->_filter['payment_status_id'] != ""))
            	{
                    $this->_simple_selector['selected'] = $this->_filter['payment_status_id'];
            	}

                $status_array = modApiFunc('Checkout', 'getOrderPaymentStatusList');
                foreach ($status_array as $status)
                {
                    $sel = 0;
                    if (isset($this->_filter['payment_statuses']) && is_array($this->_filter['payment_statuses']) && isset($this->_filter['payment_statuses'][$status['id']]))
            	    {
            		    $sel = 1;
            	    }
                	$this->_simple_selector['options'][] = array('value'=>$status['id'], 'name'=>$status['name'], 'selected'=>$sel);
                }

                $value = $this->TemplateFiller->fill("checkout/orders/", "search-payment-status-selector.tpl.html", array());
                break;

            case 'SearchFromDaySelector':
                $this->_simple_selector['selected'] = $this->_filter['from_day'];
                $this->_simple_selector['options'] = array();
                for ($i = 1; $i <= 31; $i++)
                {
                    $num = sprintf("%02d", $i);
                    $this->_simple_selector['options'][] = array('value'=>$num, 'name'=>$i);
                }
                $value = $this->TemplateFiller->fill("checkout/orders/", "search-from-day-selector.tpl.html", array());
                break;

            case 'SearchFromMonthSelector':
                $this->_simple_selector['selected'] = $this->_filter['from_month'];
                $this->_simple_selector['options'] = array();
                for ($i = 1; $i <= 12; $i++)
                {
                    $num = sprintf("%02d", $i);
                    $this->_simple_selector['options'][] = array('value'=>$num, 'name'=>$this->MessageResources->getMessage("GENERAL_MONTH_".$num));
                }
                $value = $this->TemplateFiller->fill("checkout/orders/", "search-from-month-selector.tpl.html", array());
                break;

            case 'SearchFromYearSelector':
                $this->_simple_selector['selected'] = $this->_filter['from_year'];
                $this->_simple_selector['options'] = array();
                $curr_year_4digits = date('Y');
                $start_year = (int)(modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_START_YEAR'));
                $offset_to = modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_YEAR_OFFSET');

                for ($i = $start_year; $i <= $curr_year_4digits + $offset_to; $i++)
                {
                    $this->_simple_selector['options'][] = array('value'=>$i, 'name'=>$i);
                }
                $value = $this->TemplateFiller->fill("checkout/orders/", "search-from-year-selector.tpl.html", array());
                break;

            case 'SearchToDaySelector':
                if (empty($this->_filter['to_day']) == false)
            	{
                    $this->_simple_selector['selected'] = $this->_filter['to_day'];
            	}
            	else
            	{
            		$this->_simple_selector['selected'] = date("j");
            	}
                $this->_simple_selector['options'] = array();
                for ($i = 1; $i <= 31; $i++)
                {
                    $num = sprintf("%02d", $i);
                    $this->_simple_selector['options'][] = array('value'=>$num, 'name'=>$i);
                }
                $value = $this->TemplateFiller->fill("checkout/orders/", "search-to-day-selector.tpl.html", array());
                break;

            case 'SearchToMonthSelector':
                if (empty($this->_filter['to_month']) == false)
            	{
                    $this->_simple_selector['selected'] = $this->_filter['to_month'];
            	}
            	else
            	{
            		$this->_simple_selector['selected'] = date("m");
            	}
                $this->_simple_selector['options'] = array();
                for ($i = 1; $i <= 12; $i++)
                {
                    $num = sprintf("%02d", $i);
                    $this->_simple_selector['options'][] = array('value'=>$num, 'name'=>$this->MessageResources->getMessage("GENERAL_MONTH_".$num));
                }
                $value = $this->TemplateFiller->fill("checkout/orders/", "search-to-month-selector.tpl.html", array());
                break;

            case 'SearchToYearSelector':
                if (empty($this->_filter['to_year']) == false)
            	{
                    $this->_simple_selector['selected'] = $this->_filter['to_year'];
            	}
            	else
            	{
            		$this->_simple_selector['selected'] = date("Y");
            	}
                $this->_simple_selector['options'] = array();
                $curr_year_4digits = date('Y');
                $start_year = (int)(modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_START_YEAR'));
                $offset_to = modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_YEAR_OFFSET');

                for ($i = $start_year; $i <= $curr_year_4digits + $offset_to; $i++)
                {
                    $this->_simple_selector['options'][] = array('value'=>$i, 'name'=>$i);
                }
                $value = $this->TemplateFiller->fill("checkout/orders/", "search-to-year-selector.tpl.html", array());
                break;

            case 'SimpleSelectorOption':
                $selected = $this->_simple_selector['selected'];
                $value = "";
                foreach ($this->_simple_selector['options'] as $option)
                {
                    $sel = $option['value'] == $selected ? " selected" : "";
                    $value .= "<OPTION value=\"".$option['value']."\"".$sel.">".$option['name']."</OPTION>\n";
                }
                break;

            case 'SimpleCheckBoxGroup_Orders':
                $selected = $this->_simple_selector['selected'];
                $value = "";

                $items_per_col = 2; // number of items per column
                $idx = 0;
                $flag = 0;
                foreach ($this->_simple_selector['options'] as $option)
                {
                    if ($idx % $items_per_col == 0)
                    {
                    	$value .= "<TR>\n";
                    	$flag = 0;
                    }

                    $sel = ""; $highlight = "";
                    if ($option['selected'] == 1)
                    {
                	    $sel = "checked";
                	    if ($this->_filter['search_by'] == "date")
                	    {
                	        $highlight = "style='color: black;'";
                	    }
                    }

                	$name = "order_".preg_replace("/ /","",$option['name']);
                    $value .= "<TD $highlight><INPUT class='form-control input-inline input-sm' id='".$name."' name='order_status[".$option['value']."]' type='checkbox' ".$sel."> ".$option['name']."</TD>\n";

                    if ($idx % $items_per_col == 0 && $flag == 1)
                    {
                    	$value .= "</TR>\n";
                    	$flag = 0;
                    }

                    $flag = 1;
                    $idx++;
                }
            break;

            case 'SimpleCheckBoxGroup_Payments':
                $selected = $this->_simple_selector['selected'];
                $value = "";

                $items_per_col = 1; // number of items per column
                $idx = 0;
                $flag = 0;
                foreach ($this->_simple_selector['options'] as $option)
                {
                    if ($idx % $items_per_col == 0)
                    {
                    	$value .= "<TR>\n";
                    	$flag = 0;
                    }

                    $sel = ""; $highlight = "";
                    if ($option['selected'] == 1)
                    {
                	    $sel = "checked";
                        if ($this->_filter['search_by'] == "date")
                	    {
                	        $highlight = "color: black;";
                	    }
                    }

                    $name = "payment_".preg_replace("/ /","",$option['name']);
                    $value .= "<TD style='margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;".$highlight."'><INPUT class='form-control input-inline input-sm' id='".$name."' name='payment_status[".$option['value']."]' type='checkbox' ".$sel."> ".$option['name']."</TD>\n";

                    if ($idx % $items_per_col == 0 && $flag == 1)
                    {
                    	$value .= "</TR>\n";
                    	$flag = 0;
                    }

                    $flag = 1;
                    $idx++;
                }
            break;

            case 'ResultCount':
                $from = modApiFunc("Paginator", "getCurrentPaginatorOffset")+1;
                $to = modApiFunc("Paginator", "getCurrentPaginatorOffset") +  modApiFunc("Paginator", "getPaginatorRowsPerPage", "Checkout_Orders");
                $total = modApiFunc("Paginator", "getCurrentPaginatorTotalRows");
                if ($to > $total)
                {
                    $to = $total;
                }
                if ($total <= modApiFunc("Paginator", "getPaginatorRowsPerPage", "Checkout_Orders"))
                {
                    $value = $this->MessageResources->getMessage(new ActionMessage(array("ORDERS_RESULTS_LESS_THEN_ROWS_PER_PAGE_FOUND", $total)));
                }
                else
                {
                    $value = $this->MessageResources->getMessage(new ActionMessage(array("ORDERS_RESULTS_MORE_THEN_ROWS_PER_PAGE_FOUND", $from, $to, $total)));
                }
                break;

            case 'ResultDateRange':
                $count = count($this->_orders);
                if ($count == 0)
                {
                    $value = "";
                    break;
                }
                elseif ($count == 1)
                {
                    $orderInfo = $this->_fetched_orders[$this->_orders[0]];
                    $value = modApiFunc("Localization", "SQL_date_format", $orderInfo['order_date']);
                    break;
                }

                $first_in_list_order_info = $this->_fetched_orders[$this->_orders[0]];
                $last_in_list_order_info = $this->_fetched_orders[$this->_orders[$count-1]];
                $value = $value = modApiFunc("Localization", "SQL_date_format", $last_in_list_order_info['order_date']) . " - " . modApiFunc("Localization", "SQL_date_format", $first_in_list_order_info['order_date']);
                break;

            case 'ResultAmount':
                if (count($this->_orders) == 0)
                {
                    $value = 0;
                    break;
                }
                $amount = 0;
                $all_orders_are_in_main_currency = true;
                $main_store_currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
                foreach ($this->_orders as $order_id)
                {
                    //           order_total   main_store_currency       .
                    //           order'                      ,                                 default ( . .
                    //       main_store_currency                 ).
                    //     default currency                                                 main_store_currency,
                    //                                               ,            .          order_total
                    //                                          .
                    $order_default_currency = modApiFunc("Localization", "getOrderMainCurrency", $order_id, $this->_fetched_orders[$order_id]['order_currencies_list']);

                    $order_total_in_order_default_currency = $this->_fetched_orders[$order_id]['price_total'][$order_default_currency]['order_total'];
                    if($order_default_currency == $main_store_currency)
                    {
                        //var_dump($order);
                        $amount += $order_total_in_order_default_currency;
                    }
                    else
                    {
                        $all_orders_are_in_main_currency = false;

                        $total = modApiFunc('Currency_Converter','convert', $order_total_in_order_default_currency, $order_default_currency, $main_store_currency);
                        $amount += $total;
                    }
                }
                $main_store_currency_id = modApiFunc("Localization", "getMainStoreCurrency");

                modApiFunc("Localization", "pushDisplayCurrency", $main_store_currency_id, $main_store_currency_id);
                $value = modApiFunc("Localization", "currency_format", $amount);
                modApiFunc("Localization", "popDisplayCurrency");

                if($all_orders_are_in_main_currency == false)
                {
                    $value = "~".$value;
                }
                break;

            case 'ResultTaxTotal':break;
            case 'ResultFullTaxExempt':break;
            case 'ResultTaxTotalMinusFullTaxExempt':
                $tax_summary = $this->__getTaxSummary();
                $value = $tax_summary[$tag];

                break;

            case 'Items':
                $value = $this->getOrders();
                break;

            case 'OrderStatusSelector':
                $value = '<select class="form-control input-sm input-small" name="status_id['. $this->_order['IdInt'] .']" onchange="onStatusChanged('. $this->_order['IdInt'] .')">';

                if(!isset($this->OrderStatusList))
                {
                    $this->OrderStatusList = modApiFunc('Checkout', 'getOrderStatusList');
                }
                foreach ($this->OrderStatusList as $status)
                {
                    $value .= '<option value="'. $status['id'] .'" '. ($status['id'] == $this->_order['StatusId'] ? " selected" : "").'>'. $status['name']. '</option>';
                }
                $value .= '</select>';
                break;

            case 'OrderIdLinkTitle':
                $value = $this->MessageResources->getMessage('ORDERS_RESULTS_ORDER_ID_LINK_TITLE');
                break;

            case 'OrderCustomerNameLinkTitle':
                $value = $this->MessageResources->getMessage('ORDERS_RESULTS_ORDER_CUSTOMER_NAME_LINK_TITLE');
                break;

            case 'OrderPaymentStatusSelector':
                $value = '<select class="form-control input-sm input-small" name="payment_status_id['. $this->_order['IdInt'] .']" onchange="onStatusChanged('. $this->_order['IdInt']. ')">\n';
                if(!isset($this->OrderPaymentStatusList))
                {
                    $this->OrderPaymentStatusList = modApiFunc('Checkout', 'getOrderPaymentStatusList');
                }
                foreach ($this->OrderPaymentStatusList as $status)
                {
                    $this->_payment_status = $status;
                    $value .= '<option value="' .$status['id']. '" '. ($status['id'] == $this->_order['PaymentStatusId'] ? ' selected' : ''). '>'. $status['name'] .'</option>';
                }
                $value .= '</select>';
                break;



            case 'SearchOrders':
                $value = $this->TemplateFiller->fill("checkout/orders/", "search.tpl.html", array());
                break;

            case 'SearchBy':
                if ($this->_filter['search_by'] == 'status')
                {
                    $msg = "";
                    switch ($this->_filter['filter_status_id'])
                    {
                        case 0:
                            $msg = $this->MessageResources->getMessage('ORDERS_SEARCH_ALL');
                            break;
                        case 1:
                            $msg = $this->MessageResources->getMessage('ORDERS_SEARCH_NEW_ORDERS');
                            break;
                        case 2:
                            $msg = $this->MessageResources->getMessage('ORDERS_SEARCH_IN_PROGRESS');
                            break;
                        case 3:
                            $msg = $this->MessageResources->getMessage('ORDERS_SEARCH_READY_TO_SHIP');
                            break;
                    }
                    $value = $msg;
                }
                elseif ($this->_filter['search_by'] == 'date')
                {
                    $value = $this->MessageResources->getMessage('ORDERS_SEARCH_FILTER');
                }
                elseif ($this->_filter['search_by'] == 'id')
                {
                    $value = $this->MessageResources->getMessage('ORDERS_SEARCH_ORDER_ID');
                }
                break;

            case 'SearchResults':
                if (count($this->_orders) == 0)
                {
                    $value = modApiFunc('TmplFiller', 'fill', "checkout/orders/", "empty.tpl.html", array());
                }
                else
                {
                    $value = $this->TemplateFiller->fill("checkout/orders/", "results.tpl.html", array());
                }
                break;

            case 'HighLightAll':
                if ($this->_filter['search_by'] == 'status' && $this->_filter['filter_status_id'] == 0)
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightNewOrders':
                if ($this->_filter['search_by'] == 'status' && $this->_filter['filter_status_id'] == 1)
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightInProgress':
                if ($this->_filter['search_by'] == 'status' && $this->_filter['filter_status_id'] == 2)
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightReadyToShip':
                if ($this->_filter['search_by'] == 'status' && $this->_filter['filter_status_id'] == 3)
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightDate':
                if ($this->_filter['search_by'] == 'date')
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightDateOrderStatus':
                if ($this->_filter['search_by'] == 'date' && isset($this->_filter['order_statuses']))
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightDateOrderPaymentStatus':
                if ($this->_filter['search_by'] == 'date' && isset($this->_filter['payment_statuses']))
                {
                    $value = "color: blue;";
                }
                break;

            case 'HighLightOrderId':
                if ($this->_filter['search_by'] == 'id')
                {
                    $value = "color: blue;";
                }
                break;
            case 'HighLightAffiliateId':
                if ($this->_filter['search_by'] == 'date' && !empty($this->_filter['affiliate_id']))
                {
                    $value = "style='color: blue;font-weight:bold;'";
                }
                break;
            case 'SearchingOrderId':
                $value = "";
                if ($this->_filter['search_by'] == 'id' && !empty($this->_filter['order_id']))
                {
                    $value = $this->_filter['order_id'];
                }
                break;
            case 'DeleteOrdersLink':
                $request = new Request();
                $request->setView  ('DeleteOrders');
                $request->setAction('SetOrdersForDeleteAction');
                $value = $request->getURL();
                break;

            case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("Checkout_Orders", "Orders");
                break;

            #                               PaginatorRows
            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("Checkout_Orders", 'Orders', 'PGNTR_ORD_ITEMS');
                break;

            case 'ResultMessageRow':
                $value = $this->outputResultMessage();
                break;

            case 'ResultMessage':
                $value = $this->_Template_Contents['ResultMessage'];
                break;

            case 'PackingSlipLink':
                $request = new Request();
                $request -> setView('OrderPackingSlip');
                $request -> setAction('SetCurrentOrder');
                $request -> setKey('order_id', $this -> _order['IdInt']);
                // uncomment the following link to force printing
                // $request -> setKey('do_print', 'Y');
                $value = $request -> getURL();
                break;

            case 'InvoiceLink':
                $request = new Request();
                $request -> setView('OrderInvoice');
                $request -> setAction('SetCurrentOrder');
                $request -> setKey('order_id', $this -> _order['IdInt']);
                // uncomment the following link to force printing
                // $request -> setKey('do_print', 'Y');
                $value = $request -> getURL();
                break;

            case 'AffiliateIDSearch':
                $v = (isset($this->_filter['affiliate_id']))?$this->_filter['affiliate_id']:"";
                $value = "<input type='text' name='affiliate_id' size='28' class='form-control form-filter input-sm' value='".$v."' />";
                break;

            default:
                list($entity, $tag) = getTagName($tag);
                if ($entity == 'order')
                {
                    if (_ml_strpos($tag, 'price') === 0)
                    {
                        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('price')));
                        if ($tag == 'total')
                        {
                            $value = $this->_order['Total'];
                            if($this->_order['TotalInMainStoreCurrency'] !== NULL)
                            {
                                $value = $this->_order['TotalInMainStoreCurrency'] . ' (' . $value . ')';
                            }
                        }
                        elseif ($tag == 'subtotal')
                        {
                            $value = $this->_order['Subtotal'];
                        }
                        else if ($tag == 'taxes')
                        {
                            $full_tax_exempt_orders = $this->__getFullTaxExemptOrders();
//                            else
                            {
                                $code = $this->_fetched_orders[$this->_order['IdInt']]["order_currencies_list"]["CURRENCY_TYPE_MAIN_STORE_CURRENCY"]["currency_code"];
                                $value = $this->_fetched_orders[$this->_order['IdInt']]["price_total"][$code]["order_tax_total"];
                                $crcy_id = modApiFunc("Localization", "getCurrencyIdByCode", $code);

                                modApiFunc("Localization", "pushDisplayCurrency", $crcy_id, $crcy_id);
                                $value = modApiFunc("Localization", "currency_format", $value);
                                $null_value = modApiFunc("Localization", "currency_format", "0.0000");
                                modApiFunc("Localization", "popDisplayCurrency");
                            }
                            if(array_key_exists($this->_order['IdInt'], $full_tax_exempt_orders))
                            {
                                $value = $null_value . " (ex. $value)";
                            }
                        }
                        else
                        {
                            $prices = getKeyIgnoreCase('price', $this->_order);
                            $value = $prices[$tag];
                        }
                    }
                    elseif (_ml_strpos($tag, 'customer') === 0)
                    {
                        $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('customer')));
                        switch($tag)
                        {
                            case 'name':
                                $value = $this->_order['PersonName'];
                                break;
                            case 'id':
                                $value = $this->_order['PersonId'];
                                break;
                            case 'infoname':
                                $value = $this->_order['PersonInfoName'];
                                break;
                        };
                    }
                    else
                    {
                       $value = getKeyIgnoreCase($tag, $this->_order);
                    }
                }
                break;
        }
        return $value;
    }

    var $TemplateFiller;
    var $MessageResources;
    var $_filter;
    var $_orders;
    var $_order;
    var $_status;
    var $_payment_status;
    var $_simple_selector;

    var $tax_debug;
    var $tax_expt_debug;
}
?>