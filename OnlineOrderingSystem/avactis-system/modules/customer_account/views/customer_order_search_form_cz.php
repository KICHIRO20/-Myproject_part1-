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

class OrderSearchForm
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-order-search-form.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OrderSearchForm()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("OrderSearchForm"))
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

                if($filter == 'custom')
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
            };
        };
    }

    function out_SelectDate($type, $from_to)
    {
        $select_array = array(
            "select_name" => $type.'_'.$from_to
           ,"values" => array()
        );

        switch($type)
        {
            case 'day':
                for($i=1;$i<=31;$i++)
                {
                    $select_array["values"][] = array(
                            "value" => $i
                           ,"contents" => $i
                    );
                };
                break;
            case 'month':
                for($i=1;$i<=12;$i++)
                {
                    $select_array["values"][] = array(
                            "value" => $i
                           ,"contents" => getMsg('SYS','GENERAL_MONTH_'.sprintf("%02d",$i))
                    );
                };
                break;
            case 'year':
                $start_year = (int)(modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_START_YEAR'));
                $end_year = (int)(date("Y")) + modApiFunc('Settings','getParamValue','VISUAL_INTERFACE','SEARCH_YEAR_OFFSET');
                for($i=$start_year;$i<=$end_year;$i++)
                {
                    $select_array["values"][] = array(
                            "value" => $i
                           ,"contents" => $i
                    );
                };
                break;
        };

        if(isset($this->incoming_filter[$type.'_'.$from_to]))
        {
            $select_array["selected_value"] = $this->incoming_filter[$type.'_'.$from_to];
        }
        elseif ($from_to == 'to')
        {
            switch ($type)
            {
                case 'day': $select_array["selected_value"] = date("d"); break;
                case 'month': $select_array["selected_value"] = date("m"); break;
                case 'year': $select_array["selected_value"] = date("Y"); break;
            }
        }

        return HtmlForm::genDropdownSingleChoice($select_array);
    }

    function out_SelectOrderStatus()
    {
        $select_array = array(
            "select_name" => "order_status"
           ,"values" => array(
                array("value" => ORDER_STATUS_ALL, "contents" => 'Any') // !resource in code
           )
        );

        $statuses = modApiFunc('Checkout','getOrderStatusList');

        foreach($statuses as $k => $info)
        {
            $select_array["values"][] = array("value" => $info['id'], "contents" => $info['name']);
        };

        if(isset($this->incoming_filter['order_status']))
        {
            $select_array["selected_value"] = $this->incoming_filter['order_status'];
        };

        return HtmlForm::genDropdownSingleChoice($select_array);
    }

    function out_SelectOrderPaymentStatus()
    {
        $select_array = array(
            "select_name" => "order_payment_status"
           ,"values" => array(
                array("value" => ORDER_PAYMENT_STATUS_ALL, "contents" => 'Any') // !resource in code
           )
        );

        $statuses = modApiFunc('Checkout','getOrderPaymentStatusList');

        foreach($statuses as $k => $info)
        {
            $select_array["values"][] = array("value" => $info['id'], "contents" => $info['name']);
        };

        if(isset($this->incoming_filter['order_payment_status']))
        {
            $select_array["selected_value"] = $this->incoming_filter['order_payment_status'];
        };

        return HtmlForm::genDropdownSingleChoice($select_array);
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_FormActionURL'
           ,'Local_SelectDate_Day_From'
           ,'Local_SelectDate_Month_From'
           ,'Local_SelectDate_Year_From'
           ,'Local_SelectDate_Day_To'
           ,'Local_SelectDate_Month_To'
           ,'Local_SelectDate_Year_To'
           ,'Local_Select_OrderStatus'
           ,'Local_Select_OrderPaymentStatus'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OrderSearchForm');
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
            case 'Local_FormActionURL':
                $r = new Request();
                $r->setView('CustomerOrdersHistory');
                $r->setKey('filter','custom');
                $value = $r->getURL();
                break;
            case 'Local_Select_OrderStatus':
                $value = $this->out_SelectOrderStatus();
                break;
            case 'Local_Select_OrderPaymentStatus':
                $value = $this->out_SelectOrderPaymentStatus();
                break;
            default:
                if(preg_match("/^local_selectdate_([a-z]+)_([a-z]+)$/i",$tag,$matches))
                {
                    $value = $this->out_SelectDate(_ml_strtolower($matches[1]),_ml_strtolower($matches[2]));
                }
        };

        return $value;
    }

    var $customer_obj;
    var $incoming_filter;
};

?>