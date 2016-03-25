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

class OrderSearchByIdForm
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-order-search-by-id-form.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OrderSearchByIdForm()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("OrderSearchByIdForm"))
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
                    $o_id = $request->getValueByKey('order_id');
                    if (!$o_id || !is_int($o_id))
                    {
                        return;
                    }
                    $orders_search_filter = array(
                        'type' => 'id'
                       ,'order_status' => ORDER_STATUS_ALL
                       ,'order_id' => intval($o_id)
                    );
                }

                $this->incoming_filter = $orders_search_filter;
            };
        };
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
           ,'Local_SearchedOrderID'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OrderSearchByIdForm');
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
                $r->setKey('filter','id');
                $value = $r->getURL();
                break;
            case 'Local_SearchedOrderID':
                if(isset($this->incoming_filter['order_id']))
                {
                    $value = $this->incoming_filter['order_id'];
                }
                else
                {
                    $value = '';
                };
                break;
        };

        return $value;
    }


};

?>