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

class ExportCustomers
{
    function ExportCustomers()
    {
        $this->search_filter = modApiFunc('Session','get','CustomersListSearchFilter');
        $this->search_filter['use_paginator'] = false;
        modApiFunc('Customer_Account','setCustomersSearchFilter',$this->search_filter);
        $this->customers = modApiFunc('Customer_Account','getSearchCustomersResult');
    }

    function out_Attributes($group_name)
    {
        global $application;
        $attrs = modApiFunc('Customer_Account','getAttributesForExport',$group_name);

        $html_code = '';

        foreach($attrs as $attr_short_name => $attr_short_info)
        {
            $template_contents = array(
                'AttributeVisibleName' => $attr_short_info['visible']
               ,'AttributeTag' => $attr_short_info['tag']
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("customer_account/export_customers/", "attribute.tpl.html",array());
        };

        return $html_code;
    }

    function out_AttrsGroup($group_name)
    {
        global $application;

        $template_contents = array(
            'GroupName' => getMsg('CA','PIG_'._ml_strtoupper($group_name))
           ,'GroupAttrs' => $this->out_Attributes($group_name)
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/export_customers/", "group-container.tpl.html",array());
    }

    function out_AttrsSide($side_name)
    {
        $html_code = '';

        foreach($this->attrs_sides[$side_name] as $group_name)
        {
            $html_code .= $this->out_AttrsGroup($group_name);
        };

        return $html_code;
    }

    function output()
    {
        global $application;
        $request = $application->getInstance('Request');
        $cust_Ids=$request->getValueByKey('cust_Id');
        $customerArray=array();
        if(isset($cust_Ids) && !empty($cust_Ids)){
        	$customerIDs=explode('|', $cust_Ids);
        	foreach($this->customers as $localCustomer){
        		if(in_array($localCustomer['customer_id'],$customerIDs)){
        			$customerArray[]=$localCustomer;
        		}
        	}
        	modApiFunc('Session','set','Customer_Export',$customerArray);
        }else{
        	$customerArray=$this->customers;
        }

        $template_contents = array(
            'CustomersToExportCount' => count($customerArray)
           ,'CustomersFilter' => gzdeflate(serialize($this->search_filter))
           ,'AttrsLeftSide' => $this->out_AttrsSide('left')
           ,'AttrsRightSide' => $this->out_AttrsSide('right')
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/export_customers/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $customers;
    var $search_filter;
    var $attrs_sides = array(
        'left' => array('system','customer','orders')
       ,'right' => array('billing','shipping')
    );
};

?>