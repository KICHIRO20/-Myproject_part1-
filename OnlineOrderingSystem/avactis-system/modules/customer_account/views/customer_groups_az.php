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
 * @author Alexey V. Astafyev
 *
 */

class CustomerGroups
{
    function CustomerGroups()
    {
        $this->customer_groups = modApiFunc('Customer_Account','getGroups','exclude unsigned');
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
            return $this->mTmplFiller->fill("customer_account/groups/", "result_message.tpl.html",array());
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
                $return_html_code.=$this->mTmplFiller->fill("customer_account/groups/", "error_message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function out_CustomerGroupsTable()
    {
        global $application;

        $template_contents = array(
            'CustomerGroupsRows' => $this->out_CustomerGroupsRows()
           ,'cn_Header' => $this->out_ListColumnHeader('name')
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/groups/", "list_container.tpl.html",array());
    }

    function out_ListColumnHeader($header_name)
    {
        global $application;

        switch($header_name)
        {
            case 'name': $column_lang_code = 'LBL_NAME'; break;
        };

        $column_name = getMsg('CA',$column_lang_code);
        return $column_name;
    }

    function out_CustomerGroupsRows()
    {
        global $application;

        $html_code = '';

        foreach($this->customer_groups as $group_id=>$group_name)
        {
            $template_contents = array(
                'CustomerGroupID' => $group_id
               ,'CustomerGroupName' => $group_name
               ,'HideCheckBox' => ($group_id == 1 ? ' style="display:none;"' : '')
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("customer_account/groups/", "customer_group_row.tpl.html",array());
        };

        return $html_code;
    }

    function output()
    {
        global $application;

        $template_contents = array(
            'CustomerGroupsTable' => $this->out_CustomerGroupsTable()
           ,'ResultMessage'  => $this->out_ResultMessage()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/groups/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $customer_groups;

};

?>