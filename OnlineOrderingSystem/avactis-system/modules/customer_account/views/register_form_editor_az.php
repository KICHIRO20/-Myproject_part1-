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

class RegisterFormEditor
{
    function RegisterFormEditor()
    {
        $group_info = modApiFunc('Customer_Account','getPersonInfoGroupInfoByName','Customer');
        $this->reg_form_attrs = modApiFunc('Customer_Account','getPersonInfoGroupAttrs',$group_info['group_id']);

        loadCoreFile('html_form.php');

        $this->not_change_props = array(
            'Password'
           ,'RePassword'
           ,'Email'
        );

        $this->hidden_props = array(
            'AccountName'
        );

        $this->login_attr_id = null;
        $this->email_attr_id = null;

        $this->settings = modApiFunc('Customer_Account','getSettings');
    }

    function outputResultMessage()
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
            return $this->mTmplFiller->fill("customer_account/misc/", "result-message.tpl.html",array());
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
                $return_html_code.=$this->mTmplFiller->fill("customer_account/misc/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function out_Attributes()
    {
        global $application;

        $html_code = '';

        $yes_no_select = array(
            'name' => ''
           ,'value' => ''
           ,'onChange' => ''
           ,'id' => ''
           ,'is_checked' => ''
        );

        foreach($this->reg_form_attrs as $attr_info)
        {
            switch($attr_info['attr_name'])
            {
                case 'AccountName':
                    $this->login_attr_id = $attr_info['attr_id'];
                    break;
                case 'Email':
                    $this->email_attr_id = $attr_info['attr_id'];
                    break;
                case 'Country':
                    $this->country_attr_id = $attr_info['attr_id'];
                    break;
                case 'State':
                    $this->state_attr_id = $attr_info['attr_id'];
                    break;
            };

            if(in_array($attr_info['attr_name'], $this->hidden_props))
            {
                continue;
            };

            foreach(array('is_visible','is_required') as $prop_name)
            {
                if(in_array($attr_info['attr_name'], $this->not_change_props))
                {
                    $param = 'disabled';
                }
                else
                {
                    $param = '';
                };

                $var_name = $prop_name.'_checkbox';
                $$var_name = $yes_no_select;
                ${$var_name}['name'] = $attr_info['attr_name'].'['.$prop_name.']';
                ${$var_name}['value'] = "Y";
                ${$var_name}['onChange'] = 'onChange_'.$prop_name.'('.$attr_info['attr_id'].');';
                ${$var_name}['is_checked'] = ($attr_info[$prop_name] == "Y")?"checked":"";
                ${$var_name}['id'] = $attr_info['attr_id'].'_'.$prop_name;
                $$var_name = HtmlForm::genCheckbox($$var_name, $param);
                if ($param == 'disabled')
                {
                    $$var_name .="\n <input type='hidden' ".HtmlForm::genHiddenField($attr_info['attr_name'].'[disabled]', "disabled")." />";
                }
            };

            $template_contents = array(
                'Name' => prepareHTMLDisplay($attr_info['visible_name'])
               ,'AttrID' => $attr_info['attr_id']
               ,'AttrName' => $attr_info['attr_name']
               ,'Descr' => getMsg('CA',$attr_info['lang_code'])
               ,'IsVisible' => $is_visible_checkbox
               ,'IsRequiered' => $is_required_checkbox
            );


            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("customer_account/register_form_editor/", "attribute.tpl.html",array());
        };

        return $html_code;
    }

    function out_AuthSchemeSelect()
    {
        $scheme_select = array(
            'select_name' => 'sets[AUTH_SCHEME]'
           ,'selected_value' => $this->settings['AUTH_SCHEME']
           ,'onChange' => 'setAuthSchemeFieldRequired(); changeHint(\'auth_scheme\',this.value);'
           ,'id' => 'AuthScheme'
           ,'values' => array(
                array('value' => AUTH_SCHEME_BY_LOGIN, 'contents' => getMsg('CA','AUTH_SCHEME_BY_LOGIN'))
               ,array('value' => AUTH_SCHEME_BY_EMAIL, 'contents' => getMsg('CA','AUTH_SCHEME_BY_EMAIL'))
           )
        );

        return HtmlForm::genDropdownSingleChoice($scheme_select, 'disabled');
    }

    function out_AttrsAsSortItems()
    {
        $return_html_code="";

        for($i=0;$i<count($this->reg_form_attrs);$i++)
        {
            if(in_array($this->reg_form_attrs[$i]['attr_name'], $this->hidden_props))
            {
                continue;
            };

            $return_html_code.="<option value=".$this->reg_form_attrs[$i]['attr_id'].">".$this->reg_form_attrs[$i]['visible_name']."</option>";
        };

        return $return_html_code;
    }

    function out_SortOrderBlock()
    {
        global $application;

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setAction('update_group_sort_order');

        $template_contents = array(
            'SortFormAction' => $r->getURL()
           ,'SortItems' => $this->out_AttrsAsSortItems()
           ,'GroupName' => 'Customer'
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/register_form_editor/", "sort_form.tpl.html",array());
    }

    function out_AccountActivationSchemeSelect()
    {
        $_select = array(
            'select_name' => 'sets[ACCOUNT_ACTIVATION_SCHEME]'
           ,'selected_value' => $this->settings['ACCOUNT_ACTIVATION_SCHEME']
           ,'id' => 'ActivationScheme'
           ,'onChange' => 'setActivationSchemeField(); changeHint(\'activation_scheme\',this.value);'
           ,'values' => array(
                array('value' => ACCOUNT_ACTIVATION_SCHEME_NONE, 'contents' => getMsg('CA','ACCOUNT_ACTIVATION_SCHEME_NONE'))
               ,array('value' => ACCOUNT_ACTIVATION_SCHEME_BY_ADMIN, 'contents' => getMsg('CA','ACCOUNT_ACTIVATION_SCHEME_BY_ADMIN'))
               ,array('value' => ACCOUNT_ACTIVATION_SCHEME_BY_CUSTOMER, 'contents' => getMsg('CA','ACCOUNT_ACTIVATION_SCHEME_BY_CUSTOMER'))
           )
        );

        return HtmlForm::genDropdownSingleChoice($_select);
    }

    function output()
    {
        global $application;

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setAction('update_reg_form');

        $template_contents = array(
            'Attributes' => $this->out_Attributes()
           ,'LoginAttrID' => $this->login_attr_id
           ,'EmailAttrID' => $this->email_attr_id
           ,'CountryAttrID' => $this->country_attr_id
           ,'StateAttrID' => $this->state_attr_id
           ,'AuthSchemeSelect' => $this->out_AuthSchemeSelect()
           ,'AuthShemeByLogin' => AUTH_SCHEME_BY_LOGIN
           ,'AuthShemeByEmail' => AUTH_SCHEME_BY_EMAIL
           ,'RegFormAction' => $r->getURL()
           ,'ResultMessage' => $this->outputResultMessage()
           ,'SortOrderBlock' => $this->out_SortOrderBlock()
           ,'AccountActivationSchemeSelect' => $this->out_AccountActivationSchemeSelect()
           ,'CheckoutTypeQuickChecked' => $this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK ? 'checked' : ''
           ,'CheckoutTypeAutoAccountChecked' => $this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_AUTOACCOUNT ? 'checked' : ''
           ,'CheckoutTypeAccountRequiredChecked' => $this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_ACCOUNT_REQUIRED ? 'checked' : ''
           ,'SetsAutoCreateAccountState' => ($this->settings['AUTO_CREATE_ACCOUNT'] == 'Y' ? 'checked' : '') . ($this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_AUTOACCOUNT ? '' : ' disabled')
           ,'SetsMergeState' => ($this->settings['MERGE_ORDERS_BY_EMAIL'] == 'Y' ? 'checked' : '') . ($this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_AUTOACCOUNT ? '' : ' disabled')
           ,'AuthSchemeHint' => $this->settings['AUTH_SCHEME'] == AUTH_SCHEME_BY_LOGIN ? getMsg('CA','HINT_AUTH_SCHEME_BY_LOGIN') : getMsg('CA','HINT_AUTH_SCHEME_BY_EMAIL')
           ,'ActivationSchemeHint' => $this->settings['ACCOUNT_ACTIVATION_SCHEME'] == ACCOUNT_ACTIVATION_SCHEME_NONE ? getMsg('CA','HINT_ACTIVATION_SCHME_NONE') : ($this->settings['ACCOUNT_ACTIVATION_SCHEME'] == ACCOUNT_ACTIVATION_SCHEME_BY_ADMIN ? getMsg('CA','HINT_ACTIVATION_SCHEME_BY_ADMIN') : getMsg('CA','HINT_ACTIVATION_SCHEME_BY_CUSTOMER'))
           ,'FullFormDisplay' => $this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK ? 'none' : ''
           ,'SingleFormDisplay' => $this->settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK ? '' : 'none'
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("customer_account/register_form_editor/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $reg_form_attrs;
    var $not_change_props;
    var $hidden_props;
    var $login_attr_id;
    var $email_attr_id;
    var $country_attr_id;
    var $state_attr_id;
    var $settings;
};

?>