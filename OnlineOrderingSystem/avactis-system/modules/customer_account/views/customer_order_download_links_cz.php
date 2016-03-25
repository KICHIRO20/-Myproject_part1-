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

class OrderDownloadLinks
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-order-download-links.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
               ,'DownloadLinkInfo' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OrderDownloadLinks()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("OrderDownloadLinks"))
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
            $this->order_product_id = $request->getValueByKey('order_product_id');

            $q_filter = array(
                'type' => 'quick'
               ,'order_status' => ORDER_STATUS_ALL
            );

            $this->customer_obj->setOrdersHistoryFilter($q_filter);

            if(!in_array($this->order_id, $this->customer_obj->getOrdersIDs()))
            {
                $this->order_id = null;
            };

            if($this->order_id != null and $this->order_product_id != null)
            {
                $pids = modApiFunc('Checkout','getOrderProductsIDs',$this->order_id);
                if(!in_array($this->order_product_id, $pids))
                {
                    $this->order_product_id = null;
                };
            };
        };
    }

    function out_HotlinksList()
    {
        $html_code = '';

        foreach($this->PFHotlinks as $k => $hl_info)
        {
            $this->current_hotlink_info = $hl_info;
            $html_code .= $this->templateFiller->fill('DownloadLinkInfo');
        }

        return $html_code;
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_OrderID'
           ,'Local_OrderedProductName'
           ,'Local_HotlinksList'
           ,'Local_FileName'
           ,'Local_HotlinkStatus'
           ,'Local_HotlinkValue'
           ,'Local_HotlinkKey'
           ,'Local_HotlinkExpireDate'
           ,'Local_HotlinkTries'
           ,'Local_OrderInfoLink'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OrderDownloadLinks');
        $this->templateFiller->setTemplate($this->template);

        if(modApiFunc('Customer_Account','getCurrentSignedCustomer') !== null
            and $this->order_id != null and $this->order_product_id != null)
        {
            $this->ordered_product_info = modApiFunc('Checkout','getOrderProductInfo',$this->order_product_id);
            $this->PFHotlinks = modApiFunc('Product_Files','getHotlinksList',$this->order_product_id);
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

        if(preg_match("/^local_(.+)$/i",$tag,$matches))
        {
            switch(_ml_strtolower($matches[1]))
            {
                case 'orderinfolink':
                    $r = new Request();
                    $r->setView('CustomerOrderInfo');
                    $r->setKey('order_id', $this->order_id);
                    $value = $r->getURL();
                    break;
                case 'orderid':
                    $value = sprintf("%05d",$this->order_id);
                    break;
                case 'orderedproductname':
                    $value = $this->ordered_product_info['order_product_name'];
                    break;
                case 'hotlinkslist':
                    $value = $this->out_HotlinksList();
                    break;
                case 'filename':
                    $finfo = modApiFunc('Product_Files','getPFileInfo',$this->current_hotlink_info['file_id']);
                    $value = ($finfo != null) ? $finfo['file_name'] : '<span style="color: red;">'.getMsg('PF','FILE_WAS_DELETED').'</span>';
                    break;
                case 'hotlinkstatus':
                    $value = $this->current_hotlink_info['status'];
                    break;
                case 'hotlinkvalue':
                    $value = $this->current_hotlink_info['hotlink_value'];
                    break;
                case 'hotlinkkey':
                    $value = str_rev_pad($this->current_hotlink_info['hotlink_value'],90);
                    break;
                case 'hotlinkexpiredate':
                    $value = modApiFunc('Localization','timestamp_date_format',$this->current_hotlink_info['expire_date']) . ' ' .
                             modApiFunc('Localization','timestamp_time_format',$this->current_hotlink_info['expire_date']);
                    break;
                case 'hotlinktries':
                    $value = $this->current_hotlink_info['was_try'].'/'.$this->current_hotlink_info['max_try'];
                    break;
            };
        };

        return $value;
    }

    var $customer_obj;
    var $order_id;
    var $order_product_id;
    var $ordered_product_info;
    var $PFHotlinks;
    var $current_hotlink_info;

};

?>