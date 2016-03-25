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

class CustomersList
{
    function CustomersList()
    {
        $this->search_filter = array(
            'type' => 'letter'
           ,'search_string' => ''
           ,'use_paginator' => true
           ,'letter_filter_by' => 'account_name'
           ,'sort_by' => 'customer_account'
           ,'sort_dir' => 'asc'
        );

        $request = new Request();
        $filter = $request->getValueByKey('filter');
        if($filter != null)
        {
            if($filter == 'letter')
            {
                $this->search_filter['type'] = 'letter';
                $letter = $request->getValueByKey('search_string');
                if($letter != null)
                {
                    $this->search_filter['search_string'] = $letter;
                };
            };
            if($filter == 'custom')
            {
                $this->search_filter['type'] = 'custom';
                $search_string = $request->getValueByKey('search_string');
                if($search_string != null)
                {
                    $this->search_filter['search_string'] = trim($search_string);
                };
            };
        }
        else
        {
            if(modApiFunc('Session','is_set','CustomersListSearchFilter'))
            {
                $this->search_filter = modApiFunc('Session','get','CustomersListSearchFilter');
            };
        };

        $letter_filter_by = $request->getValueByKey('letter_filter_by');
        if($letter_filter_by != null)
        {
             $this->search_filter['letter_filter_by'] = $letter_filter_by;
        }
        else
        {
            if(modApiFunc('Session','is_set','CustomersListSearchFilter'))
            {
                $_tmp = modApiFunc('Session','get','CustomersListSearchFilter');
                $this->search_filter['letter_filter_by'] = $_tmp['letter_filter_by'];
            };
        }

        $sort_by = $request->getValueByKey('sort_by');
        if($sort_by != null)
        {
            $this->search_filter['sort_by'] = $sort_by;
            $sort_dir = $request->getValueByKey('sort_dir');
            if($sort_dir != null)
            {
                $this->search_filter['sort_dir'] = $sort_dir;
            };
        }
        else
        {
            if(modApiFunc('Session','is_set','CustomersListSearchFilter'))
            {
                $_tmp = modApiFunc('Session','get','CustomersListSearchFilter');
                $this->search_filter['sort_by'] = $_tmp['sort_by'];
                $this->search_filter['sort_dir'] = $_tmp['sort_dir'];
            };
        };

        modApiFunc('Session','set','CustomersListSearchFilter',$this->search_filter);

        modApiFunc('Paginator','setCurrentPaginatorName','CustomersList');

        if($request->getValueByKey('pgnum') == null)
        {
            modApiFunc('Paginator','setPaginatorPage','CustomersList',1);
        };

        modApiFunc('Customer_Account','setCustomersSearchFilter',$this->search_filter);

        // filling up the paginator data
        if ($this -> search_filter['use_paginator'])
        {
            $this -> search_filter['paginator'] = null;
            $this -> search_filter['paginator'] = modApiFunc('Customer_Account', 'getPgSearchCustomersResult', $this -> search_filter);
            modApiFunc('Customer_Account', 'setCustomersSearchFilter', $this->search_filter);
        }

        $this->customers = modApiFunc('Customer_Account','getSearchCustomersResult');
        $this->customer_groups = modApiFunc('Customer_Account','getGroups','exclude unsigned');

        loadCoreFile('html_form.php');
    }

    function out_CustomersRows()
    {
        global $application;

        $html_code = '';

        loadClass('CCustomerInfo');
        foreach($this->customers as $customer_info)
        {
            $template_contents = array(
                'CustomerIcon' => ($customer_info['customer_status'] == 'B') ? 'not-reg' : ( $customer_info['customer_status'] == 'R' ? 'reg-dp' :($customer_info['customer_status'] == 'A' ? 'reg' : 'reg-na'))
               ,'CustomerID' => $customer_info['customer_id']
               ,'CustomerAccount' => CCustomerInfo::getDisplayAccountNameExt($customer_info['customer_account'], $customer_info['customer_status'])
               ,'CustomerName' => prepareHTMLDisplay($customer_info['name'])
               ,'CustomerOrdersCount' => $customer_info['orders_count']
               ,'CustomerTotalFullyPaid' => modApiFunc('Localization','currency_format',$customer_info['fully_paid_amount'])
               ,'CustomerTotalAmount' => modApiFunc('Localization','currency_format',$customer_info['total_amount'])
               ,'CustomerMembership' => $this->out_CustomerMembershipDropdown($customer_info['customer_id'], $customer_info['group_id'])
               ,'CustomerStatusMsg' => getMsg('CA','CUSTOMER_STATUS_'.$customer_info['customer_status'])
               ,'CustomerStatusLongMsg' => getMsg('CA','CUSTOMER_STATUS_LONG_'.$customer_info['customer_status'])
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("customer_account/customers_list/", "customer-row.tpl.html",array());
        };

        if(count($this->customers) < 5)
        {
            for($i=count($this->customers);$i<5;$i++)
            {
                $html_code .= $this->mTmplFiller->fill("customer_account/customers_list/", "empty-row.tpl.html",array());
            };
        };

        return $html_code;
    }

    function out_ListColumnHeader($header_name)
    {
        global $application;

        if($this->search_filter['sort_by'] == $header_name)
        {
            $new_sort_dir = $this->search_filter['sort_dir'] == 'asc' ? 'desc' : 'asc';
        }
        else
        {
            $new_sort_dir = 'asc';
        };

        switch($header_name)
        {
            case 'customer_account':  $column_lang_code = 'LBL_ACCOUNT';           break;
            case 'name':              $column_lang_code = 'LBL_NAME';              break;
            case 'orders_count':      $column_lang_code = 'LBL_ORDERS';            break;
            case 'fully_paid_amount': $column_lang_code = 'LBL_TOTAL_FULLY_PAID';  break;
            case 'total_amount':      $column_lang_code = 'LBL_TOTAL_AMOUNT';      break;
            case 'group_id':          $column_lang_code = 'LBL_MEMBERSHIP';        break;
        };

        if($header_name == $this->search_filter['sort_by'])
        {
            $column_name = getMsg('CA',$column_lang_code);
        }
        else
        {
            $column_name = getMsg('CA',$column_lang_code);
        };

        $template_contents = array(
            'sort_by' => $header_name
           ,'new_sort_dir' => $new_sort_dir
           ,'cur_sort_dir' => $this->search_filter['sort_dir']
           ,'img_display' => $this->search_filter['sort_by'] == $header_name ? '' : 'none'
           ,'column_name' => $column_name
           ,'js_mouse_events' => $this->search_filter['sort_by'] == $header_name ? '' : 'onMouseOver="this.rows[0].cells[0].style.textDecoration=\'underline\';" onMouseOut="this.rows[0].cells[0].style.textDecoration=\'none\';"'
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/customers_list/", "list_column_header.tpl.html",array());
    }

    function out_CustomersTable()
    {
        global $application;

        $template_contents = array(
            'CustomersRows' => $this->out_CustomersRows()
           ,'ca_Header' => $this->out_ListColumnHeader('customer_account')
           ,'cn_Header' => $this->out_ListColumnHeader('name')
           ,'oc_Header' => $this->out_ListColumnHeader('orders_count')
           ,'fp_Header' => $this->out_ListColumnHeader('fully_paid_amount')
           ,'ta_Header' => $this->out_ListColumnHeader('total_amount')
           ,'ms_Header' => $this->out_ListColumnHeader('group_id')
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/customers_list/", "list_container.tpl.html",array());
    }

    function out_SearchLettersList()
    {
        $items = array(
            array('display' => getMsg('CA','LBL_ALL'), 'value' => '')
        );

        for ($i=65; $i<=90; $i++)
        {
            $items[] = array('display' => '&nbsp;<b>'._byte_chr($i).'</b>&nbsp;', 'value' => _ml_strtolower(_byte_chr($i)));
        };

        $html_code = '';

        foreach($items as $item_info)
        {
            $html_code .= '';

            if($this->search_filter['type'] == 'letter'
                and $this->search_filter['search_string'] == $item_info['value'])
            {
                $html_code .= '<li class="active"><a href="javascript:void(0);">'.$item_info['display'].'</a>';
            }
            else
            {
                $html_code .= '<li><a href="customers.php?filter=letter&search_string='.$item_info['value'].'">'.$item_info['display'].'</a>';
            };

            $html_code .= '</li>';
        };

        return $html_code;
    }

    function out_SearchLetterFilterSelect()
    {
        $filter_select = array(
            'select_name' => 'letter_filter_by'
           ,'selected_value' => $this->search_filter['letter_filter_by']
	   ,'class' => 'form-control input-sm input-small inline'
           ,'onChange' => "window.location = 'customers.php?letter_filter_by='+this.value;"
           ,'values' => array(
                array('value' => 'account_name', 'contents' => getMsg('CA','LBL_FILTER_BY_ACCOUNT'))
               ,array('value' => 'customer_name', 'contents' => getMsg('CA','LBL_FILTER_BY_LASTNAME'))
           )
        );

        return HtmlForm::genDropdownSingleChoice($filter_select);
    }

    function out_CustomerMembershipDropdown($customer_id, $customer_group=null)
    {
        return modApiFunc('Customer_Account','getGroupsDropDown', $customer_id, $customer_group);
    }

    function out_SearchForm()
    {
        global $application;

        $template_contents = array(
            'SearchLettersList' => $this->out_SearchLettersList()
           ,'SearchLetterFilterSelect' => $this->out_SearchLetterFilterSelect()
           ,'SearchString' => $this->search_filter['type'] == 'custom' ? prepareHTMLDisplay($this->search_filter['search_string']) : ''
           ,'SearchFormLabelColor' => $this->search_filter['type'] == 'custom' ? '#0000FF' : '#666666'
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/customers_list/", "search-form.tpl.html",array());
    }

    function out_ResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('CA',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("customer_account/customers_list/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => getMsg('CA',$eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("customer_account/customers_list/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function output()
    {
        global $application;

        $searchForm = $this->out_SearchForm();

        $customersTable = $this->out_CustomersTable();

        $resultMessage = $this->out_ResultMessage();

        $template_contents = array(
            'SearchForm' => $searchForm
           ,'CustomersTable' => $customersTable
           ,'PaginatorLine' => getPaginatorLine('CustomersList','CustomersList')
           ,'PaginatorRows' => getPaginatorRows('CustomersList','CustomersList','PGNTR_CUST_ITEMS')
           ,'ResultMessage' => $resultMessage
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/customers_list/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $customers;
    var $customer_groups;
    var $search_filter;
};

?>